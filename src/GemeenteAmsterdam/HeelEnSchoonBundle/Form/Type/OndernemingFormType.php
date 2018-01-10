<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OndernemingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('naam', TextType::class);
        $builder->add('straat', TextType::class);
        $builder->add('huisnummer', TextType::class);
        $builder->add('email', EmailType::class, ['required' => false]);
        $builder->add('telefoon', TextType::class, ['required' => false]);
        $builder->add('geoPoint', TextType::class);
        if ($options['showGebied'] === true) {
            $builder->add('gebied', EntityType::class, [
                'required' => true,
                'class' => Gebied::class,
                'choice_label' => function (Gebied $gebied) {
                    return $gebied->getNaam();
                }
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('showGebied', true);
    }
}