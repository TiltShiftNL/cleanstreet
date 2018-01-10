<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use GemeenteAmsterdam\HeelEnSchoonBundle\Form\DataTransformer\OndernemersBakToNummerTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
    }
}