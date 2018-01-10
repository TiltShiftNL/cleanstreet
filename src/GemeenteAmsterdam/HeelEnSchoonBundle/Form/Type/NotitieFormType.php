<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Onderneming;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Notitie;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Foto;

class NotitieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('onderneming', EntityType::class, [
                        'class' => Onderneming::class,
                        'required' => false,
                        'placeholder' => '(notitie algemeen)',
                        'choice_label' => function (Onderneming $onderneming) {
                            return $onderneming->getNaam() . ', ' . $onderneming->getStraat();
                        },
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('onderneming')->orderBy('onderneming.naam', 'ASC');
                        },
                        'label' => 'Notitie gaat over (onderneming)',
            ]);
        $builder->add('geo', HiddenType::class);
        $builder->add('straat', HiddenType::class, ['required' => false]);
        $builder->add('huisnummer', HiddenType::class, ['required' => false]);

        $builder->add('tekst', TextareaType::class, ['attr' => ['rows' => 3]]);
        $builder->add('fotos', CollectionType::class, [
                        'allow_add' => true,
                        'allow_delete' => true,
                        'prototype' => true,
                        'by_reference' => false,
                        'required' => false,
                        'entry_type' => FotoFormType::class,
                        'delete_empty' => true
            ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var $notitie Notitie */
            $notitie = $event->getData();
            foreach ($notitie->getFotos() as $foto) {
                /** @var $foto Foto */
                if ($foto->getImageFile() === null) {
                    $notitie->removeFoto($foto);
                }
            }
        });
    }
}