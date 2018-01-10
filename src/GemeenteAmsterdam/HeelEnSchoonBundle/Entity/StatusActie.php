<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class StatusActie extends Actie
{
    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $oudeStatus;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $oudeOplossing;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $nieuweStatus;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nieuweOplossing;

    public function getType()
    {
        return 'status';
    }

    /**
     * Set oudeStatus
     *
     * @param boolean $oudeStatus
     *
     * @return StatusActie
     */
    public function setOudeStatus($oudeStatus)
    {
        $this->oudeStatus = $oudeStatus;

        return $this;
    }

    /**
     * Get oudeStatus
     *
     * @return boolean
     */
    public function getOudeStatus()
    {
        return $this->oudeStatus;
    }

    /**
     * Set oudeOplossing
     *
     * @param string $oudeOplossing
     *
     * @return StatusActie
     */
    public function setOudeOplossing($oudeOplossing)
    {
        $this->oudeOplossing = $oudeOplossing;

        return $this;
    }

    /**
     * Get oudeOplossing
     *
     * @return string
     */
    public function getOudeOplossing()
    {
        return $this->oudeOplossing;
    }

    /**
     * Set nieuweStatus
     *
     * @param boolean $nieuweStatus
     *
     * @return StatusActie
     */
    public function setNieuweStatus($nieuweStatus)
    {
        $this->nieuweStatus = $nieuweStatus;

        return $this;
    }

    /**
     * Get nieuweStatus
     *
     * @return boolean
     */
    public function getNieuweStatus()
    {
        return $this->nieuweStatus;
    }

    /**
     * Set nieuweOplossing
     *
     * @param string $nieuweOplossing
     *
     * @return StatusActie
     */
    public function setNieuweOplossing($nieuweOplossing)
    {
        $this->nieuweOplossing = $nieuweOplossing;

        return $this;
    }

    /**
     * Get nieuweOplossing
     *
     * @return string
     */
    public function getNieuweOplossing()
    {
        return $this->nieuweOplossing;
    }
}
