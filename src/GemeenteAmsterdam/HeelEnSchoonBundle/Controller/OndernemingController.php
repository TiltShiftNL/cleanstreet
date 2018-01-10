<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Onderneming;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use GemeenteAmsterdam\HeelEnSchoonBundle\Form\Type\OndernemingFormType;
use GemeenteAmsterdam\HeelEnSchoonBundle\Form\Type\OndernemersBakFormType;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\OndernemersBak;

class OndernemingController extends Controller
{
    /**
     * @Route("/onderneming/{gebiedId}/nieuw")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function createAction(Request $request, Gebied $gebied)
    {
        $onderneming = new Onderneming();
        $onderneming->setGebied($gebied);
        $form = $this->createForm(OndernemingFormType::class, $onderneming, ['showGebied' => false]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($onderneming);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_tickets_index', ['_fragment' => 'ondernemers', 'gebiedId' => $gebied->getId()]);
        }

        return new JsonResponse(['errors' => $form->getErrors(true, true)->__toString()], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/onderneming/{gebiedId}/{id}/update")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @ParamConverter("onderneming", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Onderneming")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function updateAction(Request $request, Onderneming $onderneming, Gebied $gebied)
    {
        $form = $this->createForm(OndernemingFormType::class, $onderneming);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_tickets_index', ['_fragment' => 'ondernemers', 'gebiedId' => $gebied->getId()]);
        }

        return new JsonResponse(['errors' => $form->getErrors(true, true)->__toString()], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/onderneming/{gebiedId}/{id}/remove")
     * @Method("POST")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @ParamConverter("onderneming", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Onderneming")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function removeAction(Request $request, Onderneming $onderneming, Gebied $gebied)
    {
        $onderneming->setVerwijderd(true);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_tickets_index', ['_fragment' => 'ondernemers', 'gebiedId' => $onderneming->getGebied()->getId()]);
    }

    /**
     * @Route("/onderneming/{gebiedId}/{id}/detail")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @ParamConverter("onderneming", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Onderneming")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function detailAction(Request $request, Onderneming $onderneming, Gebied $gebied)
    {
        $form = $this->createForm(OndernemingFormType::class, $onderneming, ['action' => $this->generateUrl('gemeenteamsterdam_heelenschoon_onderneming_update', ['id' => $onderneming->getId(), 'gebiedId' => $onderneming->getGebied()->getId()])]);

        $ondernemersBak = new OndernemersBak();
        $ondernemersBakForm = $this->createForm(OndernemersBakFormType::class, $ondernemersBak, ['action' => $this->generateUrl('gemeenteamsterdam_heelenschoon_ondernemersbak_create', ['ondernemingId' => $onderneming->getId(), 'gebiedId' => $onderneming->getGebied()->getId()])]);

        return $this->render('HeelEnSchoonBundle:Onderneming:detail.html.twig', [
            'onderneming' => $onderneming,
            'form' => $form->createView(),
            'ondernemersBakForm' => $ondernemersBakForm->createView()
        ]);
    }
}