<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Ticket;
use Doctrine\ORM\Tools\Pagination\Paginator;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Categorie;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Notitie;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\LedigingsVerzoek;
use Symfony\Component\HttpFoundation\JsonResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Doctrine\ORM\Query;


class DashboardController extends Controller
{
    /**
     * @Route("/dashboard")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function indexAction(Request $request)
    {
        $categorien = $this->getDoctrine()->getRepository(Categorie::class)->findBy([], ['hoofdcategorie' => 'ASC', 'subcategorie' => 'ASC']);

        /** @var $qb QueryBuilder */
        $qb = $this->getDoctrine()->getManager()->getRepository(Ticket::class)->createQueryBuilder('ticket');

        $qb->join('ticket.gebied', 'gebied');
        $qb->addSelect('gebied');

        $gebied = $this->getDoctrine()->getRepository(Gebied::class)->find($request->query->get('gebiedId', -1));
        if ($gebied === null) {
            $gebied = $this->getDoctrine()->getRepository(Gebied::class)->findOneBy([]);
        }
        $qb->andWhere('ticket.gebied = :gebied');
        $qb->setParameter('gebied', $gebied);

        $startdatum = \DateTime::createFromFormat('Y-m-d', $request->query->get('startDatum'));
        $einddatum = \DateTime::createFromFormat('Y-m-d', $request->query->get('eindDatum'));

        if ($startdatum === null || $startdatum === false) {
            $startdatum = (new \DateTime())->sub(new \DateInterval('P31D')); // default
        } else {
            $startdatum->setTime(0, 0, 0);
        }

        if ($einddatum === null || $einddatum === false) {
            $einddatum = new \DateTime();
        } elseif ($einddatum->diff($startdatum)->days > 93) {
            $einddatum = (new \DateTime())->sub(new \DateInterval('P93D'));
        } else {
            $einddatum->setTime(23, 59, 59);
        }

        $qb->andWhere('((ticket.datumTijdAangemaakt BETWEEN :startDatum1 AND :eindDatum1) OR (ticket.datumTijdAangemaakt BETWEEN :startDatum2 AND :eindDatum2))');
        $qb->setParameter('startDatum1', $startdatum);
        $qb->setParameter('eindDatum1', $einddatum);
        $qb->setParameter('startDatum2', $startdatum);
        $qb->setParameter('eindDatum2', $einddatum);

        if ($request->query->has('adres')) {
            $matches = [];
            $success = preg_match("/^\s*(?P<straat>\S.*)\s*(?P<nummer>\d*)\s*$/Ui", $request->query->get('adres'), $matches);
            if ($success !== false && $success !== null) {
                if (empty($matches['straat']) === false) {
                    $qb->andWhere('LOWER(ticket.straat) = :straat');
                    $qb->setParameter('straat', strtolower($matches['straat']));
                }
                if (empty($matches['nummer']) === false) {
                    $qb->andWhere('LOWER(ticket.huisnummer) = :nummer');
                    $qb->setParameter('nummer', strtolower($matches['nummer']));
                }
            }
        }

        $records = $qb->getQuery()->execute();

        // tel records per categorie en oplossing
        $counters = ['oplossing' => [], 'categorie' => [], 'hoofdcategorie' => []];
        $counters['oplossing'][Ticket::OPLOSSING_GEEN] = count(array_filter($records, function (Ticket $ticket) {
            return $ticket->getOplossing() === Ticket::OPLOSSING_GEEN;
        }));
        $counters['oplossing'][Ticket::OPLOSSING_GELEEGD] = count(array_filter($records, function (Ticket $ticket) {
            return $ticket->getOplossing() === Ticket::OPLOSSING_GELEEGD;
        }));
        $counters['oplossing'][Ticket::OPLOSSING_NIETOPGELOST] = count(array_filter($records, function (Ticket $ticket) {
            return $ticket->getOplossing() === Ticket::OPLOSSING_NIETOPGELOST;
        }));
        $counters['oplossing'][Ticket::OPLOSSING_ONBEKEND] = count(array_filter($records, function (Ticket $ticket) {
            return $ticket->getOplossing() === Ticket::OPLOSSING_ONBEKEND;
        }));
        $counters['oplossing'][Ticket::OPLOSSING_OPGELOST] = count(array_filter($records, function (Ticket $ticket) {
            return $ticket->getOplossing() === Ticket::OPLOSSING_OPGELOST;
        }));
        foreach ($categorien as $categorie) {
            $counters['categorie'][$categorie->getId()] = count(array_filter($records, function (Ticket $ticket) use ($categorie) {
                if ($ticket instanceof LedigingsVerzoek) {
                    return false;
                }
                /** @var $ticket Notitie */
                return $ticket->getCategorien()->contains($categorie);
            }));
        }
        foreach ($categorien as $categorie) {
            if (isset($counters['hoofdcategorie'][$categorie->getHoofdcategorie()]) === false) {
                $counters['hoofdcategorie'][$categorie->getHoofdcategorie()] = 0;
            }
            $counters['hoofdcategorie'][$categorie->getHoofdcategorie()] = $counters['hoofdcategorie'][$categorie->getHoofdcategorie()] + $counters['categorie'][$categorie->getId()];
        }

        // bouw grafiek informatie over samenstelling per dag
        $stats = [];
        foreach ($records as $record) {
            if ($record instanceof Notitie) {
                /** @var $record Notitie */
                $datum = $record->getDatumTijdAangemaakt()->format('Y-m-d');
                if (isset($stats[$datum]) === false) {
                    $stats[$datum] = [];
                }

                $hoofdcategorieen = [];
                foreach ($record->getCategorien() as $categorie) {
                    $hoofdcategorieen[$categorie->getHoofdcategorie()] = true;
                }
                $hoofdcategorieen = array_keys($hoofdcategorieen);

                foreach ($hoofdcategorieen as $hoofdcategorie) {
                    if (isset($stats[$datum][$hoofdcategorie]) === false) {
                        $stats[$datum][$hoofdcategorie] = 0;
                    }
                    $stats[$datum][$hoofdcategorie] ++;
                }
            }
        }
        foreach ($stats as $datum => $data) {
            ksort($data);
            $stats[$datum] = $data;
        }

        // alle opties standaard aan
        if ($request->query->has('status') === false) {
            $request->query->set('status', ['geen', 'niet opgelost', 'opgelost', 'onbekend']);
        }
        if ($request->query->has('categorie') === false) {
            $request->query->set('categorie', array_map(function (Categorie $categorie) {
                return $categorie->getId();
            }, $categorien));
        }

        // straat namen zoeken
        $qb = $this->getDoctrine()->getManager()->getRepository(Ticket::class)->createQueryBuilder('ticket');
        $qb->select('ticket.straat AS straatnaam');
        $qb->join('ticket.gebied', 'gebied');
        $qb->addSelect('gebied.id AS gebied_id');
        $qb->addGroupBy('gebied.id');
        $qb->addGroupBy('ticket.straat');
        $straten = $qb->getQuery()->execute([], Query::HYDRATE_SCALAR);

        $stratenGebied = [];
        foreach ($straten as $item) {
            if (isset($stratenGebied[$item['gebied_id']]) === false) {
                $stratenGebied[$item['gebied_id']] = [];
            }
            if (empty($item['straatnaam']) === false) {
                $stratenGebied[$item['gebied_id']][] = $item['straatnaam'];
            }
        }
        $straten = $stratenGebied;

        return $this->render('HeelEnSchoonBundle:Dashboard:index.html.twig', [
            'records' => $records,
            'counters' => $counters,
            'gebieden' => $this->getDoctrine()->getRepository(Gebied::class)->findAll(),
            'categorien' => $categorien,
            'stats' => $stats,
            'startdatum' => $startdatum,
            'einddatum' => $einddatum,
            'gebied' => $gebied,
            'straten' => $straten
        ]);
    }

    /**
     * @Route("/dashboard/excel")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function excelAction(Request $request)
    {
        $categorien = $this->getDoctrine()->getRepository(Categorie::class)->findBy([], ['hoofdcategorie' => 'ASC', 'subcategorie' => 'ASC']);

        /** @var $qb QueryBuilder */
        $qb = $this->getDoctrine()->getManager()->getRepository(Ticket::class)->createQueryBuilder('ticket');

        $qb->join('ticket.gebied', 'gebied');
        $qb->addSelect('gebied');

        $gebied = $this->getDoctrine()->getRepository(Gebied::class)->find($request->query->get('gebiedId', -1));
        if ($gebied === null) {
            $gebied = $this->getDoctrine()->getRepository(Gebied::class)->findOneBy([]);
        }
        $qb->andWhere('ticket.gebied = :gebied');
        $qb->setParameter('gebied', $gebied);

        $startdatum = \DateTime::createFromFormat('Y-m-d', $request->query->get('startDatum'));
        $einddatum = \DateTime::createFromFormat('Y-m-d', $request->query->get('eindDatum'));

        if ($startdatum === null || $startdatum === false) {
            $startdatum = (new \DateTime())->sub(new \DateInterval('P31D')); // default
        } else {
            $startdatum->setTime(0, 0, 0);
        }

        if ($einddatum === null || $einddatum === false) {
            $einddatum = new \DateTime();
        } elseif ($einddatum->diff($startdatum)->days > 93) {
            $einddatum = (new \DateTime())->sub(new \DateInterval('P93D'));
        } else {
            $einddatum->setTime(23, 59, 59);
        }

        $qb->andWhere('((ticket.datumTijdAangemaakt BETWEEN :startDatum1 AND :eindDatum1) OR (ticket.datumTijdAangemaakt BETWEEN :startDatum2 AND :eindDatum2))');
        $qb->setParameter('startDatum1', $startdatum);
        $qb->setParameter('eindDatum1', $einddatum);
        $qb->setParameter('startDatum2', $startdatum);
        $qb->setParameter('eindDatum2', $einddatum);

        if ($request->query->has('adres')) {
            $matches = [];
            $success = preg_match("/^\s*(?P<straat>\S.*)\s*(?P<nummer>\d*)\s*$/Ui", $request->query->get('adres'), $matches);
            if ($success !== false && $success !== null) {
                if (empty($matches['straat']) === false) {
                    $qb->andWhere('ticket.straat = :straat');
                    $qb->setParameter('straat', $matches['straat']);
                }
                if (empty($matches['nummer']) === false) {
                    $qb->andWhere('ticket.huisnummer = :nummer');
                    $qb->setParameter('nummer', $matches['nummer']);
                }
            }
        }

        if ($request->query->has('adres')) {
            $matches = [];
            $success = preg_match("/^\s*(?P<straat>\S.*)\s*(?P<nummer>\d*)\s*$/Ui", $request->query->get('adres'), $matches);
            if ($success !== false && $success !== null) {
                if (empty($matches['straat']) === false) {
                    $qb->andWhere('ticket.straat = :straat');
                    $qb->setParameter('straat', $matches['straat']);
                }
                if (empty($matches['nummer']) === false) {
                    $qb->andWhere('ticket.huisnummer = :nummer');
                    $qb->setParameter('nummer', $matches['nummer']);
                }
            }
        }

        $records = $qb->getQuery()->execute();


        // condition: groen bij 1
        $conditionalGroenBij1 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditionalGroenBij1->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditionalGroenBij1->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_EQUAL);
        $conditionalGroenBij1->addCondition('1');
        $conditionalGroenBij1->getStyle()->getFont()->setColor(new Color('FFFFFFFF'));
        $conditionalGroenBij1->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
        $conditionalGroenBij1->getStyle()->getFill()->setStartColor(new Color('FF63BE7B'));
        $conditionalGroenBij1->getStyle()->getFill()->setEndColor(new Color('FF63BE7B'));
        $conditionalGroenBij1->getStyle()->getFont()->setBold(true);
        $conditionalGroenBij1->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // condition: rood bij 0
        $conditionalRoodBij0 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditionalRoodBij0->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditionalRoodBij0->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_EQUAL);
        $conditionalRoodBij0->addCondition('0');
        $conditionalRoodBij0->getStyle()->getFont()->setColor(new Color('FFFFFFFF'));
        $conditionalRoodBij0->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
        $conditionalRoodBij0->getStyle()->getFill()->setStartColor(new Color('FFF8696B'));
        $conditionalRoodBij0->getStyle()->getFill()->setEndColor(new Color('FFF8696B'));
        $conditionalRoodBij0->getStyle()->getFont()->setBold(true);
        $conditionalRoodBij0->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // basis sheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setAutoFilterByColumnAndRow(1, 1, 12 + count($categorien), 1);
        $sheet->freezePaneByColumnAndRow(5, 2);

        // kolom breedte
        $sheet->getColumnDimensionByColumn(1)->setWidth(15);
        $sheet->getColumnDimensionByColumn(2)->setWidth(18);
        $sheet->getColumnDimensionByColumn(3)->setWidth(18);
        $sheet->getColumnDimensionByColumn(4)->setWidth(18);
        $sheet->getColumnDimensionByColumn(5)->setWidth(25);
        $sheet->getColumnDimensionByColumn(6)->setWidth(25);
        $sheet->getColumnDimensionByColumn(7)->setWidth(5);
        $sheet->getColumnDimensionByColumn(8)->setWidth(10);
        $sheet->getColumnDimensionByColumn(9)->setWidth(18);
        $sheet->getColumnDimensionByColumn(10)->setWidth(7);
        $sheet->getColumnDimensionByColumn(11)->setWidth(30);
        $sheet->getColumnDimensionByColumn(12)->setWidth(5);

        // header
        $sheet->setCellValueByColumnAndRow(1, 1, 'Type');
        $sheet->setCellValueByColumnAndRow(2, 1, 'Bron');
        $sheet->setCellValueByColumnAndRow(3, 1, 'Gebied');
        $sheet->setCellValueByColumnAndRow(4, 1, 'Aangemaakt');
        $sheet->setCellValueByColumnAndRow(5, 1, 'Medewerker');
        $sheet->setCellValueByColumnAndRow(6, 1, 'Onderneming');
        $sheet->setCellValueByColumnAndRow(7, 1, 'Status');
        $sheet->getStyleByColumnAndRow(7, 1)->getAlignment()->setTextRotation(90)->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValueByColumnAndRow(8, 1, 'Oplossing');
        $sheet->getStyleByColumnAndRow(8, 1)->getAlignment()->setTextRotation(90)->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValueByColumnAndRow(9, 1, 'Straat');
        $sheet->getStyleByColumnAndRow(9, 1)->getAlignment()->setTextRotation(90)->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValueByColumnAndRow(10, 1, 'Huisnummer');
        $sheet->getStyleByColumnAndRow(10, 1)->getAlignment()->setTextRotation(90)->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValueByColumnAndRow(11, 1, 'Tekst');
        $sheet->setCellValueByColumnAndRow(12, 1, 'Baknummer');
        foreach ($categorien as $i => $categorie) {
            $sheet->setCellValueByColumnAndRow(13 + $i, 1, $categorie->getHoofdcategorie() . ': ' . $categorie->getSubcategorie());
            $sheet->getStyleByColumnAndRow(13 + $i, 1)->getAlignment()->setTextRotation(45);
            $sheet->getColumnDimensionByColumn(13 + $i)->setWidth(7);
        }
        $sheet->getStyleByColumnAndRow(1, 1, (12 + count($categorien)), 1)->applyFromArray(['font' => ['bold' => true]]);

        // data
        foreach ($records as $row => $record) {
            /** @var $record Ticket */
            $sheet->setCellValueByColumnAndRow(1, 2 + $row, $record->getType());
            $sheet->setCellValueByColumnAndRow(2, 2 + $row, $record->getBron());
            $sheet->setCellValueByColumnAndRow(3, 2 + $row, $record->getGebied()->getNaam());
            $sheet->setCellValueByColumnAndRow(4, 2 + $row, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($record->getDatumTijdAangemaakt()));
            $sheet->getStyleByColumnAndRow(4, 2 + $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DATETIME);
            $sheet->setCellValueByColumnAndRow(5, 2 + $row, $record->getMedewerker() !== null ? $record->getMedewerker()->getNaam() : '');
            $sheet->setCellValueByColumnAndRow(6, 2 + $row, $record->getOnderneming() !== null ? $record->getOnderneming()->getNaam() : '');
            $sheet->setCellValueByColumnAndRow(7, 2 + $row, $record->getStatus() ? 1 : 0);
            $sheet->getStyleByColumnAndRow(7, 2 + $row)->setConditionalStyles([$conditionalGroenBij1, $conditionalRoodBij0]);
            $sheet->setCellValueByColumnAndRow(8, 2 + $row, $record->getOplossing());
            $sheet->setCellValueByColumnAndRow(9, 2 + $row, $record->getStraat());
            $sheet->setCellValueByColumnAndRow(10, 2 + $row, $record->getHuisnummer());
            $sheet->setCellValueByColumnAndRow(11, 2 + $row, $record instanceof Notitie ? $record->getTekst() : '');
            /** @var $record LedigingsVerzoek */
            $sheet->setCellValueByColumnAndRow(12, 2 + $row, $record instanceof LedigingsVerzoek && $record->getOndernemersBak() !== null ? $record->getOndernemersBak()->getKenmerk() : '');
            /** @var $record Notitie */
            foreach ($categorien as $i => $categorie) {
                $sheet->setCellValueByColumnAndRow(13 + $i, 2 + $row, $record instanceof Notitie ? $record->getCategorien()->contains($categorie) ? 1 : '' : '');
                $sheet->getStyleByColumnAndRow(13 + $i, 2 + $row)->setConditionalStyles([$conditionalGroenBij1]);
            }
        }



        $spreadsheet->getActiveSheet()->getStyle('B2')->getConditionalStyles();

        $tmpDir = $this->container->get('kernel')->getTempDir();
        $fs = new Filesystem();
        $fs->mkdir($tmpDir);
        $fileName = uniqid('excelexport-') . '.xlsx';
        $fullPath = $tmpDir . DIRECTORY_SEPARATOR . $fileName;

        $writer = new Xlsx($spreadsheet);
        $writer->save($fullPath);

        $response = new StreamedResponse(function () use ($fullPath) {
            echo file_get_contents($fullPath);
            unlink($fullPath);
        });
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'excel-export.xlsx'));
        return $response;
    }
}