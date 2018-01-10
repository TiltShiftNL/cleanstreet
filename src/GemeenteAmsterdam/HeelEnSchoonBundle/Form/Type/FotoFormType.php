<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Foto;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class FotoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('imageFile', VichImageType::class, [
                        'required' => false,
                        'allow_delete' => false,
                        'download_link' => true,
                    ]);
        $builder->add('geoUpload', HiddenType::class);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Foto::class);
    }
}