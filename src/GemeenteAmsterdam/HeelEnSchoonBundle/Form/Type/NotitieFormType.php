<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Onderneming;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Notitie;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Foto;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Categorie;
use Doctrine\ORM\EntityManager;

class NotitieFormType extends AbstractType
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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
        $builder->add('straat', TextType::class, ['required' => true]);
        $builder->add('huisnummer', TextType::class, ['required' => true]);

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

        $builder->add('hoofdcategorie', ChoiceType::class, [
                'required' => false,
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                        'Klein/Zwerf-vuil' => 'Klein/Zwerf-vuil',
                        'Zakken' => 'Zakken',
                        'Grofvuil' => 'Grofvuil',
                    ],
                'mapped' => false
            ]);
        $builder->add('subcategorieZakken', EntityType::class, [
                'required' => false,
                'expanded' => true,
                'multiple' => false,
                'class' => Categorie::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    $qb = $entityRepository->createQueryBuilder('categorie');
                    $qb->andWhere('categorie.hoofdcategorie = :hoofdcategorie');
                    $qb->setParameter('hoofdcategorie', 'Zakken');
                    $qb->addOrderBy('categorie.subcategorie', 'ASC');
                    return $qb;
                },
                'choice_attr' => function (Categorie $categorie) {
                    return ['pictogramNaam' => $categorie->getPictogramNaam()];
                },
                'mapped' => false
            ]);
        $builder->add('subcategorieGrofvuil', EntityType::class, [
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'class' => Categorie::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    $qb = $entityRepository->createQueryBuilder('categorie');
                    $qb->andWhere('categorie.hoofdcategorie = :hoofdcategorie');
                    $qb->setParameter('hoofdcategorie', 'Grofvuil');
                    $qb->addOrderBy('categorie.id', 'ASC');
                    return $qb;
                },
                'choice_attr' => function (Categorie $categorie) {
                    return ['pictogramNaam' => $categorie->getPictogramNaam()];
                },
                'mapped' => false
            ]);

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            /** @var $notitie Notitie */
            $notitie = $event->getData();
            if ($notitie->getCategorien()->count() > 0) {
                /** @var $categorie Categorie */
                $categorie = $notitie->getCategorien()->first();
                if ($categorie !== null) {
                    $event->getForm()->get('hoofdcategorie')->setData($categorie->getHoofdcategorie());
                    if ($categorie->getHoofdcategorie() === 'Zakken') {
                        $event->getForm()->get('subcategorieZakken')->setData($categorie);
                    } elseif ($categorie->getHoofdcategorie() === 'Grofvuil') {
                        $event->getForm()->get('subcategorieGrofvuil')->setData($notitie->getCategorien()->toArray());
                    }
                }
            }
        });

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var $notitie Notitie */
            $notitie = $event->getData();
            $notitie->clearCategorien();
            if ($event->getForm()->get('hoofdcategorie')->getData() === 'Klein/Zwerf-vuil') {
                $notitie->addCategorie($this->entityManager->getRepository(Categorie::class)->findOneBy(['hoofdcategorie' => 'Klein/Zwerf-vuil']));
            } elseif ($event->getForm()->get('hoofdcategorie')->getData() === 'Zakken') {
                $notitie->addCategorie($event->getForm()->get('subcategorieZakken')->getData());
            } elseif ($event->getForm()->get('hoofdcategorie')->getData() === 'Grofvuil') {
                foreach ($event->getForm()->get('subcategorieGrofvuil')->getData() as $subcategorie) {
                    $notitie->addCategorie($subcategorie);
                }
            }
        });

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