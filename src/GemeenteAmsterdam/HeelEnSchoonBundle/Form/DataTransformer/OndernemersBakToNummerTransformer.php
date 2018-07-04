<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\ORM\EntityManager;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\OndernemersBak;
use Symfony\Component\Form\Exception\TransformationFailedException;

class OndernemersBakToNummerTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function transform($ondernemersBak)
    {
        if ($ondernemersBak === null || (!($ondernemersBak instanceof OndernemersBak))) {
            return '';
        }
        return $ondernemersBak->getKenmerk();
    }

    public function reverseTransform($kenmerk)
    {
        if (empty($kenmerk)) {
            return;
        }

        $ondernemersBak = $this->em->getRepository(OndernemersBak::class)->findOneBy(['kenmerk' => $kenmerk]);

        if ($ondernemersBak === null) {
            throw new TransformationFailedException('Ondernemersbak bestaat niet ' . $kenmerk);
        }

        return $ondernemersBak;
    }
}