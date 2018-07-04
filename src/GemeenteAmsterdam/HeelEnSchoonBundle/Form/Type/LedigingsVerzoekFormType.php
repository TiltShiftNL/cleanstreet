<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use GemeenteAmsterdam\HeelEnSchoonBundle\Form\DataTransformer\OndernemersBakToNummerTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Ticket;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\LedigingsVerzoek;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\OndernemersBak;

class LedigingsVerzoekFormType extends AbstractType
{
    /**
     * @var OndernemersBakToNummerTransformer
     */
    private $ondernemersBakToNummerTransformer;

    /**
     * @param OndernemersBakToNummerTransformer $ondernemersBakToNummerTransformer
     */
    public function __construct(OndernemersBakToNummerTransformer $ondernemersBakToNummerTransformer)
    {
        $this->ondernemersBakToNummerTransformer = $ondernemersBakToNummerTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('ondernemersBak', TextType::class);
        $builder->get('ondernemersBak')->addModelTransformer($this->ondernemersBakToNummerTransformer);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var $ondernemersBak OndernemersBak */
            $ondernemersBak = $event->getForm()->get('ondernemersBak')->getData();
            /** @var $ledigingsVerzoek LedigingsVerzoek */
            $ledigingsVerzoek = $event->getData();
            $ledigingsVerzoek->setBron(Ticket::BRON_HANDMATIG);
            $ledigingsVerzoek->setGeo($ondernemersBak->getOnderneming()->getGeoPoint());
            $ledigingsVerzoek->setHuisnummer($ondernemersBak->getOnderneming()->getHuisnummer());
            $ledigingsVerzoek->setOndernemersBak($ondernemersBak);
            $ledigingsVerzoek->setOnderneming($ondernemersBak->getOnderneming());
            $ledigingsVerzoek->setStatus(true);
            $ledigingsVerzoek->setStraat($ondernemersBak->getOnderneming()->getStraat());
            $ledigingsVerzoek->setOplossing(Ticket::OPLOSSING_GELEEGD);
        });
    }
}