<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class BelActie extends Actie
{
    /**
     * @var TelefoonboekEntry
     * @ORM\ManyToOne(targetEntity="TelefoonboekEntry")
     * @ORM\JoinColumn(name="telefoonboekentry_id", referencedColumnName="id", nullable=true)
     */
    private $telefoonboekEntry;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min=0, max=255)
     */
    private $nummer;

    public function getType()
    {
        return 'bel';
    }

    /**
     * Set nummer
     *
     * @param string $nummer
     *
     * @return BelActie
     */
    public function setNummer($nummer)
    {
        $this->nummer = $nummer;

        return $this;
    }

    /**
     * Get nummer
     *
     * @return string
     */
    public function getNummer()
    {
        return $this->nummer;
    }

    /**
     * Set telefoonboekEntry
     *
     * @param \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\TelefoonboekEntry $telefoonboekEntry
     *
     * @return BelActie
     */
    public function setTelefoonboekEntry(\GemeenteAmsterdam\HeelEnSchoonBundle\Entity\TelefoonboekEntry $telefoonboekEntry = null)
    {
        $this->telefoonboekEntry = $telefoonboekEntry;

        return $this;
    }

    /**
     * Get telefoonboekEntry
     *
     * @return \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\TelefoonboekEntry
     */
    public function getTelefoonboekEntry()
    {
        return $this->telefoonboekEntry;
    }
}
