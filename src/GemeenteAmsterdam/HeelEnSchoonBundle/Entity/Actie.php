<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"foto"="FotoActie", "notitie"="NotitieActie", "bel"="BelActie", "status"="StatusActie"})
 */
abstract class Actie
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     * @Assert\NotNull
     */
    private $datumTijdAangemaakt;

    /**
     * @var Medewerker
     * @ORM\ManyToOne(targetEntity="Medewerker")
     * @ORM\JoinColumn(name="medewerker_id", referencedColumnName="id", nullable=true)
     */
    private $medewerker;

    /**
     * @var Ticket
     * @ORM\ManyToOne(targetEntity="Ticket", inversedBy="acties")
     * @ORM\JoinColumn(name="ticket_id", referencedColumnName="id", nullable=false)
     */
    private $ticket;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $verwijderd;

    public function __construct()
    {
        $this->datumTijdAangemaakt = new \DateTime();
        $this->verwijderd = false;
    }

    abstract public function getType();

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set datumTijdAangemaakt
     *
     * @param \DateTime $datumTijdAangemaakt
     *
     * @return Actie
     */
    public function setDatumTijdAangemaakt($datumTijdAangemaakt)
    {
        $this->datumTijdAangemaakt = $datumTijdAangemaakt;

        return $this;
    }

    /**
     * Get datumTijdAangemaakt
     *
     * @return \DateTime
     */
    public function getDatumTijdAangemaakt()
    {
        return $this->datumTijdAangemaakt;
    }

    /**
     * Set medewerker
     *
     * @param \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Medewerker $medewerker
     *
     * @return Actie
     */
    public function setMedewerker(\GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Medewerker $medewerker = null)
    {
        $this->medewerker = $medewerker;

        return $this;
    }

    /**
     * Get medewerker
     *
     * @return \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Medewerker
     */
    public function getMedewerker()
    {
        return $this->medewerker;
    }

    /**
     * Set ticket
     *
     * @param \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Ticket $ticket
     *
     * @return Actie
     */
    public function setTicket(\GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Ticket $ticket)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * Get ticket
     *
     * @return \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * Set verwijderd
     *
     * @param boolean $verwijderd
     *
     * @return Actie
     */
    public function setVerwijderd($verwijderd)
    {
        $this->verwijderd = $verwijderd;

        return $this;
    }

    /**
     * Get verwijderd
     *
     * @return boolean
     */
    public function getVerwijderd()
    {
        return $this->verwijderd;
    }
}
