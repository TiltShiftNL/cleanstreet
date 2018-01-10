<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class TelefoonboekEntryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('titel', TextType::class);
        $builder->add('omschrijving', TextareaType::class, ['required' => false]);
        $builder->add('email', EmailType::class, ['required' => false]);
        $builder->add('telefoon', TextType::class, ['required' => false]);
        $builder->add('url', UrlType::class, ['required' => false, 'default_protocol' => 'http']);
    }
}