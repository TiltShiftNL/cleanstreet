<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Onderneming;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\OndernemersBak;
use GemeenteAmsterdam\HeelEnSchoonBundle\Form\Type\OndernemersBakFormType;

class OndernemersBakController extends Controller
{
    /**
     * @Route("/onderneming/{gebiedId}/{ondernemingId}/ondernemersbak/nieuw")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @ParamConverter("onderneming", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Onderneming", options={"id"="ondernemingId"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function createAction(Request $request, Onderneming $onderneming, Gebied $gebied)
    {
        $ondernemersBak = new OndernemersBak();
        $ondernemersBak->setOnderneming($onderneming);
        $form = $this->createForm(OndernemersBakFormType::class, $ondernemersBak);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($ondernemersBak);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_onderneming_detail', ['gebiedId' => $gebied->getId(), 'id' => $onderneming->getId()]);
        }

        return new JsonResponse(['errors' => $form->getErrors(true, true)->__toString()], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/onderneming/{gebiedId}/{ondernemingId}/ondernemersbak/{uuid}/remove")
     * @Method("POST")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @ParamConverter("onderneming", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Onderneming", options={"id"="ondernemingId"})
     * @ParamConverter("ondernemersBak", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\OndernemersBak", options={"id"="uuid"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function removeAction(Request $request, Onderneming $onderneming, OndernemersBak $ondernemersBak, Gebied $gebied)
    {
        $ondernemersBak->setVerwijderd(true);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($this->generateUrl('gemeenteamsterdam_heelenschoon_onderneming_detail', ['gebiedId' => $gebied->getId(), 'id' => $onderneming->getId()]) );
    }
}