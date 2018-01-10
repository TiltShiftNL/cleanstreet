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


class DashboardController extends Controller
{
    /**
     * @Route("/dashboard")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function indexAction(Request $request)
    {
        /** @var $qb QueryBuilder */
        $qb = $this->getDoctrine()->getManager()->getRepository(Ticket::class)->createQueryBuilder('ticket');
        $qb->select('ticket');
        $qb->leftJoin('ticket.onderneming', 'onderneming');
        $qb->addSelect('onderneming');

        $datumAangemaakt = $request->query->has('datumAangemaakt') ? \DateTime::createFromFormat('Y-m-d', $request->query->get('datumAangemaakt')) : new \DateTime();
        if ($datumAangemaakt !== null) {
            $datumTijdAangemaaktFirst = clone $datumAangemaakt;
            $datumTijdAangemaaktFirst->setTime(0, 0, 0);
            $datumTijdAangemaaktEnd = clone $datumAangemaakt;
            $datumTijdAangemaaktEnd->setTime(23, 59, 59);
            $qb->andWhere('ticket.datumTijdAangemaakt BETWEEN :datumTijdAangemaaktFirst AND :datumTijdAangemaaktEnd');
            $qb->setParameter('datumTijdAangemaaktFirst', $datumTijdAangemaaktFirst);
            $qb->setParameter('datumTijdAangemaaktEnd', $datumTijdAangemaaktEnd);
        }

        $status = $request->query->getInt('status', -1);
        if ($status >= 0) {
            $qb->andWhere('ticket.status = :status');
            $qb->setParameter('status', (bool) $request->query->getInt('status', -1));
        }

        $oplossing = $request->query->get('oplossing', null);
        if ($oplossing !== null) {
            $qb->andWhere('ticket.oplossing = :oplossing');
            $qb->setParameter('oplossing', $request->query->get('oplossing'));
        }

        $qb->andWhere('ticket.verwijderd = :verwijderd');
        $qb->setParameter('verwijderd', false);
        $qb->addOrderBy('ticket.datumTijdAangemaakt', 'DESC');
        $qb->setMaxResults(500);
        $qb->setFirstResult($request->query->getInt('skip'));

        $tickets = new Paginator($qb->getQuery());

        return $this->render('HeelEnSchoonBundle:Dashboard:index.html.twig', [
            'tickets' => $tickets,
            'filter' => [
                'datumAangemaakt' => $datumAangemaakt,
                'status' => $status,
                'oplossing' => $oplossing,
            ],
            'pagination' => [
                'skip' => $request->query->get('skip'),
                'size' => 100
            ]
        ]);
    }

    /**
     * @Route("/dashboard/kaart")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function indexMapAction(Request $request)
    {
        /** @var $qb QueryBuilder */
        $qb = $this->getDoctrine()->getManager()->getRepository(Ticket::class)->createQueryBuilder('ticket');
        $qb->select('ticket');
        $qb->leftJoin('ticket.onderneming', 'onderneming');
        $qb->addSelect('onderneming');

        $datumAangemaakt = $request->query->has('datumAangemaakt') ? \DateTime::createFromFormat('Y-m-d', $request->query->get('datumAangemaakt')) : new \DateTime();
        if ($datumAangemaakt !== null) {
            $datumTijdAangemaaktFirst = clone $datumAangemaakt;
            $datumTijdAangemaaktFirst->setTime(0, 0, 0);
            $datumTijdAangemaaktEnd = clone $datumAangemaakt;
            $datumTijdAangemaaktEnd->setTime(23, 59, 59);
            $qb->andWhere('ticket.datumTijdAangemaakt BETWEEN :datumTijdAangemaaktFirst AND :datumTijdAangemaaktEnd');
            $qb->setParameter('datumTijdAangemaaktFirst', $datumTijdAangemaaktFirst);
            $qb->setParameter('datumTijdAangemaaktEnd', $datumTijdAangemaaktEnd);
        }

        $status = $request->query->getInt('status', -1);
        if ($status >= 0) {
            $qb->andWhere('ticket.status = :status');
            $qb->setParameter('status', (bool) $request->query->getInt('status', -1));
        }

        $oplossing = $request->query->get('oplossing', null);
        if ($oplossing !== null) {
            $qb->andWhere('ticket.oplossing = :oplossing');
            $qb->setParameter('oplossing', $request->query->get('oplossing'));
        }

        $qb->andWhere('ticket.verwijderd = :verwijderd');
        $qb->setParameter('verwijderd', false);
        $qb->addOrderBy('ticket.datumTijdAangemaakt', 'DESC');
        $qb->setMaxResults(500);
        $qb->setFirstResult($request->query->getInt('skip'));

        $tickets = new Paginator($qb->getQuery());

        return $this->render('HeelEnSchoonBundle:Dashboard:index.map.html.twig', [
            'tickets' => $tickets,
            'filter' => [
                'datumAangemaakt' => $datumAangemaakt,
                'status' => $status,
                'oplossing' => $oplossing,
            ],
            'pagination' => [
                'skip' => $request->query->get('skip'),
                'size' => 100
            ]
        ]);
    }

}