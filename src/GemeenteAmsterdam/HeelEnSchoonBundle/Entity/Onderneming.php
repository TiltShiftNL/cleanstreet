<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(
 *  indexes={
 *      @ORM\Index(name="idx_straat", columns={"straat"})
 *  }
 * )
 */
class Onderneming
{
    /**
     * @var int
     * @ORM\Column
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank
     * @Assert\Length(min=1, max=255)
     */
    private $naam;

    /**
     * @var string
     * @ORM\Column(type="string", length=125, nullable=false)
     * @Assert\NotBlank
     * @Assert\Length(min=1, max=125)
     */
    private $straat;

    /**
     * @var string
     * @ORM\Column(type="string", length=25, nullable=false)
     * @Assert\NotBlank
     * @Assert\Length(min=1, max=25)
     */
    private $huisnummer;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min=0, max=255)
     * @Assert\Email
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string", length=25, nullable=true)
     * @Assert\Length(min=0, max=25)
     */
    private $telefoon;

    /**
     * @var OndernemersBak[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="OndernemersBak", mappedBy="onderneming", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"kenmerk"="ASC"})
     */
    private $ondernemersBakken;

    /**
     * @var Ticket[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Ticket", mappedBy="onderneming")
     * @ORM\OrderBy({"datumTijdAangemaakt"="DESC"})
     */
    private $tickets;

    /**
     * @var string geo
     * @ORM\Column(type="geography", nullable=true, options={"geometry_type"="POINT"})
     */
    private $geoPoint;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $verwijderd;

    /**
     * @var Gebied
     * @ORM\ManyToOne(targetEntity="Gebied")
     * @ORM\JoinColumn(name="gebied_id", referencedColumnName="id", nullable=false)
     */
    private $gebied;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ondernemersBakken = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tickets = new \Doctrine\Common\Collections\ArrayCollection();
        $this->verwijderd = false;
    }

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
     * Set naam
     *
     * @param string $naam
     *
     * @return Onderneming
     */
    public function setNaam($naam)
    {
        $this->naam = $naam;

        return $this;
    }

    /**
     * Get naam
     *
     * @return string
     */
    public function getNaam()
    {
        return $this->naam;
    }

    /**
     * Set straat
     *
     * @param string $straat
     *
     * @return Onderneming
     */
    public function setStraat($straat)
    {
        $this->straat = $straat;

        return $this;
    }

    /**
     * Get straat
     *
     * @return string
     */
    public function getStraat()
    {
        return $this->straat;
    }

    /**
     * Set huisnummer
     *
     * @param string $huisnummer
     *
     * @return Onderneming
     */
    public function setHuisnummer($huisnummer)
    {
        $this->huisnummer = $huisnummer;

        return $this;
    }

    /**
     * Get huisnummer
     *
     * @return string
     */
    public function getHuisnummer()
    {
        return $this->huisnummer;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Onderneming
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set telefoon
     *
     * @param string $telefoon
     *
     * @return Onderneming
     */
    public function setTelefoon($telefoon)
    {
        $this->telefoon = $telefoon;

        return $this;
    }

    /**
     * Get telefoon
     *
     * @return string
     */
    public function getTelefoon()
    {
        return $this->telefoon;
    }

    /**
     * Set geoPoint
     *
     * @param geography $geoPoint
     *
     * @return Onderneming
     */
    public function setGeoPoint($geoPoint)
    {
        $this->geoPoint = $geoPoint;

        return $this;
    }

    /**
     * Get geoPoint
     *
     * @return geography
     */
    public function getGeoPoint()
    {
        return $this->geoPoint;
    }

    /**
     * Add ondernemersBakken
     *
     * @param \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\OndernemersBak $ondernemersBakken
     *
     * @return Onderneming
     */
    public function addOndernemersBakken(\GemeenteAmsterdam\HeelEnSchoonBundle\Entity\OndernemersBak $ondernemersBakken)
    {
        $this->ondernemersBakken[] = $ondernemersBakken;

        return $this;
    }

    /**
     * Remove ondernemersBakken
     *
     * @param \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\OndernemersBak $ondernemersBakken
     */
    public function removeOndernemersBakken(\GemeenteAmsterdam\HeelEnSchoonBundle\Entity\OndernemersBak $ondernemersBakken)
    {
        $this->ondernemersBakken->removeElement($ondernemersBakken);
    }

    /**
     * Get ondernemersBakken
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOndernemersBakken()
    {
        return $this->ondernemersBakken;
    }

    /**
     * Add ticket
     *
     * @param \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Ticket $ticket
     *
     * @return Onderneming
     */
    public function addTicket(\GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Ticket $ticket)
    {
        $this->tickets[] = $ticket;

        return $this;
    }

    /**
     * Remove ticket
     *
     * @param \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Ticket $ticket
     */
    public function removeTicket(\GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Ticket $ticket)
    {
        $this->tickets->removeElement($ticket);
    }

    /**
     * Get tickets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTickets()
    {
        return $this->tickets;
    }

    public function __toString()
    {
        return $this->naam . ' (' . $this->straat . ' ' . $this->huisnummer . ')';
    }

    /**
     * Set verwijderd
     *
     * @param boolean $verwijderd
     *
     * @return Onderneming
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

    /**
     * Set gebied
     *
     * @param \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied $gebied
     *
     * @return Onderneming
     */
    public function setGebied(\GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied $gebied = null)
    {
        $this->gebied = $gebied;

        return $this;
    }

    /**
     * Get gebied
     *
     * @return \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied
     */
    public function getGebied()
    {
        return $this->gebied;
    }
}
