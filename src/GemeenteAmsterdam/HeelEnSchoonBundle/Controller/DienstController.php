<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Dienst;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DienstController extends Controller
{
    /**
     * @Route("/dienst/{gebiedId}/start")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function startAction(Request $request, Gebied $gebied)
    {
        $qb = $this->getDoctrine()->getManager()->getRepository(Dienst::class)->createQueryBuilder('dienst');
        $qb->andWhere('dienst.eind IS NULL');
        $qb->andWhere('dienst.gebied = :gebied');
        $qb->setParameter('gebied', $gebied);
        $diensten = $qb->getQuery()->execute();
        foreach ($diensten as $dienst) {
            /** @var $dienst Dienst */
            $dienst->setEind(new \DateTime());
        }
        $dienst = new Dienst();
        $dienst->setStart(new \DateTime());
        $dienst->setMedewerker($this->getUser());
        $dienst->setGebied($gebied);
        $this->getDoctrine()->getManager()->persist($dienst);
        $this->getDoctrine()->getManager()->flush();
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['dienst' => true, 'medewerker' => ['id' => $this->getUser()->getId(), 'naam' => $this->getUser()->getNaam(), 'gebiedId' => $gebied->getId(), 'gebiedNaam' => $gebied->getNaam()]]);
        }
        return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_tickets_index', ['gebiedId' => $gebied->getId()]);
    }

    /**
     * @Route("/dienst/{gebiedId}/stop")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function stopAction(Request $request, Gebied $gebied)
    {
        $qb = $this->getDoctrine()->getManager()->getRepository(Dienst::class)->createQueryBuilder('dienst');
        $qb->andWhere('dienst.eind IS NULL');
        $qb->andWhere('dienst.gebied = :gebied');
        $qb->setParameter('gebied', $gebied);
        $diensten = $qb->getQuery()->execute();
        foreach ($diensten as $dienst) {
            /** @var $dienst Dienst */
            $dienst->setEind(new \DateTime());
        }
        $this->getDoctrine()->getManager()->flush();
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['dienst' => false]);
        }
        return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_tickets_index', ['gebiedId' => $gebied->getId()]);
    }

}