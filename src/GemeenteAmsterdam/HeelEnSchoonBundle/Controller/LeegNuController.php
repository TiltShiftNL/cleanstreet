<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\OndernemersBak;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use GemeenteAmsterdam\HeelEnSchoonBundle\Form\DataTransformer\OndernemersBakToNummerTransformer;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\LedigingsVerzoek;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Ticket;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class LeegNuController extends Controller
{
    /**
     * @Route(
     *  "/",
     *  host="{hostname}",
     *  requirements={
     *      "hostname"=".*leeg.*"
     *  }
     * )
     */
    public function indexAction(Request $request)
    {
        $ledigingsVerzoek = new LedigingsVerzoek();

        $formBuilder = $this->createFormBuilder($ledigingsVerzoek);
        $formBuilder->add('ondernemersBak', TextType::class);
        $formBuilder->get('ondernemersBak')->addModelTransformer(new OndernemersBakToNummerTransformer($this->getDoctrine()->getManager()));
        $formBuilder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var $ondernemersBak OndernemersBak */
            $ondernemersBak = $event->getForm()->get('ondernemersBak')->getData();
            /** @var $ledigingsVerzoek LedigingsVerzoek */
            $ledigingsVerzoek = $event->getData();
            $ledigingsVerzoek->setBron(Ticket::BRON_LEEGNU);
            $ledigingsVerzoek->setGeo($ondernemersBak->getOnderneming()->getGeoPoint());
            $ledigingsVerzoek->setHuisnummer($ondernemersBak->getOnderneming()->getHuisnummer());
            $ledigingsVerzoek->setOndernemersBak($ondernemersBak);
            $ledigingsVerzoek->setOnderneming($ondernemersBak->getOnderneming());
            $ledigingsVerzoek->setStatus(false);
            $ledigingsVerzoek->setStraat($ondernemersBak->getOnderneming()->getStraat());
            $ledigingsVerzoek->setOplossing(Ticket::OPLOSSING_GEEN);
            $ledigingsVerzoek->setGebied($ondernemersBak->getOnderneming()->getGebied());
        });
        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var $ondernemersBak OndernemersBak */
            $ondernemersBak = $form->get('ondernemersBak')->getData();

            $this->getDoctrine()->getManager()->persist($ledigingsVerzoek);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('gemeenteamsterdam_heelenschoon_leegnu_submitsuccess', ['hostname' => $request->getHost()]);
        }

        $qb = $this->getDoctrine()->getManager()->getRepository(OndernemersBak::class)->createQueryBuilder('ondernemersbak');
        $qb->join('ondernemersbak.onderneming', 'onderneming');
        $qb->addSelect('onderneming');
        $qb->andWhere('onderneming.verwijderd = :verwijderdOnderneming');
        $qb->setParameter('verwijderdOnderneming', false);
        $qb->andWhere('ondernemersbak.verwijderd = :verwijderdOndernemersbak');
        $qb->setParameter('verwijderdOndernemersbak', false);
        $bakken = $qb->getQuery()->execute();

        $gebieden = $this->getDoctrine()->getManager()->getRepository(Gebied::class)->findAll();

        return $this->render('HeelEnSchoonBundle:LeegNu:index.html.twig', [
            'form' => $form->createView(),
            'bakken' => $bakken,
            'gebieden' => $gebieden
        ]);
    }

    /**
     * @Route(
     *  "/succes",
     *  host="{hostname}",
     *  requirements={
     *      "hostname"=".*leeg.*"
     *  }
     * )
     */
    public function submitSuccessAction(Request $request)
    {
        return $this->render('HeelEnSchoonBundle:LeegNu:index.html.twig', [
            'succes' => true
        ]);
    }
}
