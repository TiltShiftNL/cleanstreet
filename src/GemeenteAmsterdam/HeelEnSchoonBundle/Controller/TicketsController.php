<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Notitie;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\QueryBuilder;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Foto;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\TelefoonboekEntry;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Onderneming;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use GemeenteAmsterdam\HeelEnSchoonBundle\Form\Type\NotitieFormType;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Ticket;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\LedigingsVerzoek;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\StatusActie;
use GemeenteAmsterdam\HeelEnSchoonBundle\Form\Type\LedigingsVerzoekFormType;
use GemeenteAmsterdam\HeelEnSchoonBundle\Form\Type\OndernemingFormType;
use GemeenteAmsterdam\HeelEnSchoonBundle\Form\Type\TelefoonboekEntryFormType;
use GemeenteAmsterdam\HeelEnSchoonBundle\Form\Type\FotoActieFormType;
use GemeenteAmsterdam\HeelEnSchoonBundle\Form\Type\NotitieActieFormType;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\FotoActie;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\NotitieActie;
use GemeenteAmsterdam\HeelEnSchoonBundle\Util\FormErrorsToArray;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Query;

class TicketsController extends Controller
{
    /**
     * @Route("/tickets/{gebiedId}")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function indexAction(Request $request, Gebied $gebied)
    {
        // tickets
        /** @var $qb QueryBuilder */
        $datum = \DateTime::createFromFormat('Y-m-d', $request->query->get('datum', date('Y-m-d')));
        $qb = $this->getDoctrine()->getManager()->getRepository(Ticket::class)->createQueryBuilder('ticket');
        $qb->select('ticket');
        $qb->leftJoin('ticket.onderneming', 'onderneming');
        $qb->addSelect('onderneming');
        if ($request->query->getBoolean('filter', false) === true) {
            $qb->andWhere('ticket.status = :status');
            $qb->setParameter('status', false);
        } else {
            $datumTijdAangemaaktFirst = clone $datum;
            $datumTijdAangemaaktFirst->setTime(0, 0, 0);
            $datumTijdAangemaaktEnd = clone $datum;
            $datumTijdAangemaaktEnd->setTime(23, 59, 59);
            $qb->andWhere('ticket.datumTijdAangemaakt BETWEEN :datumTijdAangemaaktFirst AND :datumTijdAangemaaktEnd');
            $qb->setParameter('datumTijdAangemaaktFirst', $datumTijdAangemaaktFirst);
            $qb->setParameter('datumTijdAangemaaktEnd', $datumTijdAangemaaktEnd);
        }
        $qb->andWhere('ticket.verwijderd = :verwijderd');
        $qb->setParameter('verwijderd', false);
        $qb->andWhere('ticket.gebied = :gebied');
        $qb->setParameter('gebied', $gebied);
        $qb->addOrderBy('ticket.datumTijdAangemaakt', 'DESC');
        $qb->setFirstResult($request->query->getInt('skip'));
        $tickets = $qb->getQuery()->execute();

        // telefoonboek
        $diensten = $this->getDoctrine()->getManager()->getRepository(TelefoonboekEntry::class)->findBy(['gebied' => $gebied], ['titel' => 'ASC']);

        // ondernemingen
        $qb = $this->getDoctrine()->getManager()->getRepository(Onderneming::class)->createQueryBuilder('onderneming');
        $qb->select('onderneming');
        $qb->addOrderBy('onderneming.naam', 'ASC');
        $qb->andWhere('onderneming.gebied = :gebied');
        $qb->setParameter('gebied', $gebied);
        $ondernemingen = $qb->getQuery()->execute();

        // ondernemingen sort by adres
        $qb = $this->getDoctrine()->getManager()->getRepository(Onderneming::class)->createQueryBuilder('onderneming');
        $qb->select('onderneming');
        $qb->addOrderBy('onderneming.straat', 'ASC');
        $qb->addOrderBy('onderneming.huisnummer', 'ASC');
        $qb->andWhere('onderneming.gebied = :gebied');
        $qb->setParameter('gebied', $gebied);
        $ondernemingenByStraat = $qb->getQuery()->execute();

        // notitie toevoegen
        $notitie = new Notitie();
        $notitie->addFoto(new Foto());
        $notitieForm = $this->createForm(NotitieFormType::class, $notitie, ['action' => $this->generateUrl('gemeenteamsterdam_heelenschoon_tickets_createnotitie', ['gebiedId' => $gebied->getId()])]);

        // ledigingsverzoek toevoegen
        $ledigingsVerzoek = new LedigingsVerzoek();
        $ledigingsVerzoekForm = $this->createForm(LedigingsVerzoekFormType::class, $ledigingsVerzoek, ['action' => $this->generateUrl('gemeenteamsterdam_heelenschoon_tickets_createledigingsverzoek', ['gebiedId' => $gebied->getId()])]);

        // onderneming toevoegen
        $onderneming = new Onderneming();
        $ondernemingForm = $this->createForm(OndernemingFormType::class, $onderneming, ['showGebied' => false, 'action' => $this->generateUrl('gemeenteamsterdam_heelenschoon_onderneming_create', ['gebiedId' => $gebied->getId()])]);

        // telefoonboek entry toevoegen
        $telefoonBoekEntry = new TelefoonboekEntry();
        $telefoonBoekEntry->setGebied($gebied);
        $telefoonBoekEntryForm = $this->createForm(TelefoonboekEntryFormType::class, $telefoonBoekEntry, ['action' => $this->generateUrl('gemeenteamsterdam_heelenschoon_telefoonboekentry_create', ['gebiedId' => $gebied->getId()])]);

        return $this->render('HeelEnSchoonBundle:Tickets:index.html.twig', [
            'gebied' => $gebied,
            'tickets' => $tickets,
            'notitieForm' => $notitieForm->createView(),
            'ledigingsVerzoekForm' => $ledigingsVerzoekForm->createView(),
            'ondernemingForm' => $ondernemingForm->createView(),
            'telefoonBoekEntryForm' => $telefoonBoekEntryForm->createView(),
            'diensten' => $diensten,
            'ondernemingen' => $ondernemingen,
            'ondernemingenByStraat' => $ondernemingenByStraat,
            'filter' => $request->query->getBoolean('filter', false),
            'datum' => $datum
        ]);
    }

    /**
     * @Route("/tickets/{gebiedId}/preview")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function previewAction(Request $request, Gebied $gebied)
    {
        /** @var $qb QueryBuilder */
        $qb = $this->getDoctrine()->getRepository(Ticket::class)->createQueryBuilder('ticket');
        $qb->select('COUNT(ticket.id) AS aantal');
        $qb->andWhere('ticket.gebied = :gebied');
        $qb->setParameter('gebied', $gebied);
        if ($request->query->getInt('last') > 0) {
            $qb->andWhere('ticket.id > :last');
            $qb->setParameter('last', $request->query->getInt('last'));
        } else {
            $qb->andWhere('ticket.datumTijdAangemaakt > :datumTijdAangemaakt');
            $qb->setParameter('datumTijdAangemaakt', ((new \DateTime())->setTime(0, 0, 0)));
        }
        $num = $qb->getQuery()->execute(null, Query::HYDRATE_SINGLE_SCALAR);
        return new Response($num);
    }

    /**
     * @Route("/tickets/{gebiedId}/{ticketId}")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @ParamConverter("ticket", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Ticket", options={"id"="ticketId"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function detailAction(Request $request, Ticket $ticket, Gebied $gebied)
    {
        $diensten = $this->getDoctrine()->getManager()->getRepository(TelefoonboekEntry::class)->findBy(['gebied' => $gebied], ['titel' => 'ASC']);

        $ondernemingen = $this->getDoctrine()->getManager()->getRepository(Onderneming::class)->findBy([], ['naam' => 'ASC']);

        $fotoActie = new FotoActie();
        $fotoActie->addFoto(new Foto());
        $fotoActieForm = $this->createForm(FotoActieFormType::class, $fotoActie, ['action' => $this->generateUrl('gemeenteamsterdam_heelenschoon_acties_createfoto', ['ticketId' => $ticket->getId(), 'gebiedId' => $ticket->getGebied()->getId()])]);

        $notitieActie = new NotitieActie();
        $notitieActieForm = $this->createForm(NotitieActieFormType::class, $notitieActie, ['action' => $this->generateUrl('gemeenteamsterdam_heelenschoon_acties_createnotitie', ['ticketId' => $ticket->getId(), 'gebiedId' => $ticket->getGebied()->getId()])]);

        $formType = null;
        if ($ticket instanceof Notitie) {
            $formType = NotitieFormType::class;
        } elseif ($ticket instanceof LedigingsVerzoek) {
            $formType = LedigingsVerzoekFormType::class;
        }
        $ticketForm = null;
        if ($formType !== null) {
            $ticketForm = $this->createForm($formType, $ticket);
            $ticketForm->handleRequest($request);
            if ($ticketForm->isSubmitted() && $ticketForm->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_tickets_detail', ['ticketId' => $ticket->getId(), 'gebiedId' => $gebied->getId()]);
            }
        }

        return $this->render('HeelEnSchoonBundle:Tickets:detail.html.twig', [
            'ticket' => $ticket,
            'ticketForm' => ($ticketForm !== null ? $ticketForm->createView() : null),
            'ondernemingen' => $ondernemingen,
            'diensten' => $diensten,
            'fotoActieForm' => $fotoActieForm->createView(),
            'notitieActieForm' => $notitieActieForm->createView(),
            'filter' => $request->query->getBoolean('filter', false)
        ]);
    }

    /**
     * @Route("/tickets/{gebiedId}/nieuw/notitie")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function createNotitieAction(Request $request, Gebied $gebied)
    {
        $notitie = new Notitie();
        $notitie->setGebied($gebied);
        $notitie->addFoto(new Foto());

        $form = $this->createForm(NotitieFormType::class, $notitie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $notitie->setBron(Ticket::BRON_HANDMATIG);
            $notitie->setMedewerker($this->getUser());
            $this->getDoctrine()->getManager()->persist($notitie);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_tickets_index', ['gebiedId' => $gebied->getId()]);
        }

        return new JsonResponse(['errors' => FormErrorsToArray::convert($form->getErrors(false, true))], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/tickets/{gebiedId}/nieuw/ledigingsverzoek")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function createLedigingsverzoekAction(Request $request, Gebied $gebied)
    {
        $ledigingsVerzoek = new LedigingsVerzoek();
        $ledigingsVerzoek->setGebied($gebied);

        $form = $this->createForm(LedigingsVerzoekFormType::class, $ledigingsVerzoek);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $actie = new StatusActie();
            $actie->setMedewerker($this->getUser());
            $actie->setNieuweOplossing(Ticket::OPLOSSING_GELEEGD);
            $actie->setNieuweStatus(true);
            $actie->setOudeOplossing('');
            $actie->setOudeStatus(false);
            $actie->setTicket($ledigingsVerzoek);

            $this->getDoctrine()->getManager()->persist($ledigingsVerzoek);
            $this->getDoctrine()->getManager()->persist($actie);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_tickets_index', ['gebiedId' => $gebied->getId()]);
        }

        return new JsonResponse(['errors' => FormErrorsToArray::convert($form->getErrors(false, true))], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/tickets/{gebiedId}/{id}/bewerken")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @ParamConverter("ticket", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Ticket")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function editAction(Request $request, $ticket, Gebied $gebied)
    {
        // TODO implement
    }

    /**
     * @Route("/tickets/{gebiedId}/{ticketId}/status-veranderen")
     * @Method("POST")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @ParamConverter("ticket", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Ticket", options={"id"="ticketId"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function changeStatusAction(Request $request, Ticket $ticket, Gebied $gebied)
    {
        /** @var $validator \Symfony\Component\Validator\Validator\RecursiveValidator */
        $validator = $this->get('validator');

        $oudeOplossing = $ticket->getOplossing();
        $oudeStatus = $ticket->getStatus();
        $ticket->setOplossing($request->request->get('oplossing'));
        $ticket->setStatus($request->request->getBoolean('status'));

        if ($validator->validate($ticket)->count() === 0) {
            $actie = new StatusActie();
            $actie->setMedewerker($this->getUser());
            $actie->setOudeOplossing($oudeOplossing);
            $actie->setOudeStatus($oudeStatus);
            $actie->setNieuweOplossing($ticket->getOplossing());
            $actie->setNieuweStatus($ticket->getStatus());
            $actie->setTicket($ticket);

            $this->getDoctrine()->getManager()->persist($actie);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_tickets_detail', ['gebiedId' => $ticket->getGebied()->getId(), 'ticketId' => $ticket->getId()]);
        }

        return new JsonResponse(['status' => 'error']);
    }

    /**
     * @Route("/tickets/{gebiedId}/{id}/verwijderen")
     * @Method("POST")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @ParamConverter("ticket", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Ticket")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function remove(Request $request, Ticket $ticket, Gebied $gebied)
    {
        $ticket->setVerwijderd(true);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_tickets_index', ['gebiedId' => $ticket->getGebied()->getId()]);
    }
}