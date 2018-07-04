<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Medewerker;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MedewerkerFormType extends AbstractType
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('naam', TextType::class);
        $builder->add('actief', CheckboxType::class, [
            'required' => false
        ]);
        $builder->add('admin', CheckboxType::class, [
            'required' => false
        ]);
        $builder->add('plainPassword', PasswordType::class, [
            'required' => false
        ]);
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var $form FormType */
            $form = $event->getForm();
            /** @var $data Medewerker */
            $data = $event->getData();

            if ($data->plainPassword !== null) {
                $data->setPassword($this->passwordEncoder->encodePassword($data, $data->plainPassword));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => Medewerker::class,
            ));
    }
}