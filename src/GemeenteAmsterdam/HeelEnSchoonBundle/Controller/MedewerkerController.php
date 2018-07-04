<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use GemeenteAmsterdam\HeelEnSchoonBundle\Form\Type\MedewerkerFormType;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Medewerker;

class MedewerkerController extends Controller
{
    /**
     * @Route("/medewerker/{gebiedId}")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_ADMIN')")
     */
    public function indexAction(Request $request, Gebied $gebied)
    {
        $medewerkers = $this->getDoctrine()->getManager()->getRepository(Medewerker::class)->findBy([], ['naam' => 'ASC']);

        // medewerker toevoegen
        $medewerker = new Medewerker();
        $medewerkerForm = $this->createForm(MedewerkerFormType::class, $medewerker, ['action' => $this->generateUrl('gemeenteamsterdam_heelenschoon_medewerker_create', ['gebiedId' => $gebied->getId()])]);

        return $this->render('HeelEnSchoonBundle:Medewerker:index.html.twig', [
            'gebied' => $gebied,
            'medewerkers' => $medewerkers,
            'medewerkerForm' => $medewerkerForm->createView(),
        ]);
    }

    /**
     * @Route("/medewerker/{gebiedId}/nieuw")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_ADMIN')")
     */
    public function createAction(Request $request, Gebied $gebied)
    {
        $medewerker = new Medewerker();
        $form = $this->createForm(MedewerkerFormType::class, $medewerker);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($medewerker);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_medewerker_index', ['_fragment' => 'medewerker', 'gebiedId' => $gebied->getId()]);
        }

        return new JsonResponse(['errors' => $form->getErrors(true, true)->__toString()], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/medewerker/{gebiedId}/{id}/update")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @ParamConverter("medewerker", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Medewerker")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_ADMIN')")
     */
    public function updateAction(Request $request, Medewerker $medewerker, Gebied $gebied)
    {
        $form = $this->createForm(MedewerkerFormType::class, $medewerker);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_medewerker_index', ['_fragment' => 'medewerker', 'gebiedId' => $gebied->getId()]);
        }

        return new JsonResponse(['errors' => $form->getErrors(true, true)->__toString()], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/medewerker/{gebiedId}/{id}/detail")
     * @ParamConverter("gebied", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied", options={"id"="gebiedId"})
     * @ParamConverter("medewerker", class="GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Medewerker")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED') && is_granted('ROLE_ADMIN')")
     */
    public function detailAction(Request $request, Medewerker $medewerker, Gebied $gebied)
    {
        $form = $this->createForm(MedewerkerFormType::class, $medewerker, ['action' => $this->generateUrl('gemeenteamsterdam_heelenschoon_medewerker_update', ['id' => $medewerker->getId(), 'gebiedId' => $gebied->getId()])]);

        return $this->render('HeelEnSchoonBundle:Medewerker:detail.html.twig', [
            'medewerker' => $medewerker,
            'form' => $form->createView(),
            'gebied' => $gebied
        ]);
    }
}