<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Notitie;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Foto;

class FotoActieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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