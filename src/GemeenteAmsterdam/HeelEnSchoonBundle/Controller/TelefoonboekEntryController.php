<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\TelefoonboekEntry;
use GemeenteAmsterdam\HeelEnSchoonBundle\Form\Type\TelefoonboekEntryFormType;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied;

class TelefoonboekEntryController extends Controller
{
    /**
     * @Route("/telefoonboek/{gebiedId}/nieuw")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function createAction(Request $request, Gebied $gebied)
    {
        $telefoonboekEntry = new TelefoonboekEntry();
        $telefoonboekEntry->setGebied($gebied);

        $form = $this->createForm(TelefoonboekEntryFormType::class, $telefoonboekEntry);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($telefoonboekEntry);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_tickets_index', ['_fragment' => 'diensten', 'gebiedId' => $gebied->getId()]);
        }

        return new JsonResponse(['errors' => $form->getErrors(true, true)->__toString()], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/telefoonboek/{gebiedId}/{id}/update")
     * @ParamConverter("telefoonboekEntry", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\TelefoonboekEntry")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function updateAction(Request $request, TelefoonboekEntry $telefoonboekEntry, Gebied $gebied)
    {
        $form = $this->createForm(TelefoonboekEntryFormType::class, $telefoonboekEntry);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_tickets_index', ['_fragment' => 'diensten', 'gebiedId' => $gebied->getId()]);
        }

        return new JsonResponse(['errors' => $form->getErrors(true, true)->__toString()], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/telefoonboek/{gebiedId}/{id}/remove")
     * @Method("POST")
     * @ParamConverter("telefoonboekEntry", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\TelefoonboekEntry")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function removeAction(Request $request, TelefoonboekEntry $telefoonboekEntry, Gebied $gebied)
    {
        $telefoonboekEntry->setVerwijderd(true);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_tickets_index', ['_fragment' => 'diensten', 'gebiedId' => $gebied->getId()]);
    }

    /**
     * @Route("/telefoonboek/{gebiedId}/{id}/detail")
     * @ParamConverter("telefoonboekEntry", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\TelefoonboekEntry")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_USER')")
     */
    public function detailAction(Request $request, TelefoonboekEntry $telefoonboekEntry, Gebied $gebied)
    {
        $form = $this->createForm(TelefoonboekEntryFormType::class, $telefoonboekEntry, ['action' => $this->generateUrl('gemeenteamsterdam_heelenschoon_telefoonboekentry_update', ['id' => $telefoonboekEntry->getId(), 'gebiedId' => $gebied->getId()])]);

        return $this->render('HeelEnSchoonBundle:TelefoonboekEntry:detail.html.twig', [
            'telefoonboekEntry' => $telefoonboekEntry,
            'form' => $form->createView(),
            'gebied' => $gebied
        ]);
    }
}