<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Normalizer;

use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\OndernemersBak;

class OndernemersBakNormalizer extends SerializerAwareNormalizer implements NormalizerInterface
{
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Serializer\Normalizer\NormalizerInterface::normalize()
     * @param $object OndernemersBak
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return [
                'uuid' => $object->getUuid(),
                'kenmerk' => $object->getKenmerk(),
                'verwijderd' => $object->getVerwijderd(),
                'onderneming' => [
                    'id' => $object->getOnderneming()->getId(),
                    'naam' => $object->getOnderneming()->getNaam(),
                    'straat' => $object->getOnderneming()->getStraat(),
                    'nummer' => $object->getOnderneming()->getNummer(),
                    'geoPoint' => $object->getOnderneming()->getGeoPoint(),
                ]
            ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof OndernemersBak;
    }
}