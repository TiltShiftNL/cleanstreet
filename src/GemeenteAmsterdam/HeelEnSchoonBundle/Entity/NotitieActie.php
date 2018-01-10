<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class NotitieActie extends Actie
{
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank
     * @Assert\Length(min=1, max=4096)
     */
    private $tekst;

    public function getType()
    {
        return 'notitie';
    }

    /**
     * Set tekst
     *
     * @param string $tekst
     *
     * @return NotitieActie
     */
    public function setTekst($tekst)
    {
        $this->tekst = $tekst;

        return $this;
    }

    /**
     * Get tekst
     *
     * @return string
     */
    public function getTekst()
    {
        return $this->tekst;
    }
}
