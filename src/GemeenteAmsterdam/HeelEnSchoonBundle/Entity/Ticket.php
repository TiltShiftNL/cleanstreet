<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"notitie" = "Notitie", "ledigingsverzoek" = "LedigingsVerzoek"})
 */
abstract class Ticket
{
    /**
     * @var string
     */
    const BRON_HANDMATIG = 'handmatig';

    /**
     * @var string
     */
    const BRON_LEEGNU = 'leegnu-melding';

    /**
     * @var string
     */
    const BRON_CLEANSTREET = 'cleanstreet-contact';

    /**
     * @var string
     */
    const OPLOSSING_GEEN = 'geen';

    /**
     * @var string
     */
    const OPLOSSING_NIETOPGELOST = 'niet opgelost';

    /**
     * @var string
     */
    const OPLOSSING_OPGELOST = 'opgelost';

    /**
     * @var string
     */
    const OPLOSSING_ONBEKEND = 'onbekend';

    /**
     * @var string
     */
    const OPLOSSING_GELEEGD = 'geleegd';

    /**
     * @var int
     * @ORM\Column
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $bron;

    /**
     * @var Medewerker
     * @ORM\ManyToOne(targetEntity="Medewerker")
     * @ORM\JoinColumn(name="medewerker_id", referencedColumnName="id", nullable=true)
     */
    private $medewerker;

    /**
     * @var Onderneming
     * @ORM\ManyToOne(targetEntity="Onderneming", inversedBy="tickets")
     * @ORM\JoinColumn(name="onderneming_id", referencedColumnName="id", nullable=true)
     */
    private $onderneming;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $datumTijdAangemaakt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datumTijdGewijzigd;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datumTijdGesloten;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $status;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min=0, max=255)
     */
    private $oplossing;

    /**
     * @var string geo
     * @ORM\Column(type="geography", nullable=true, options={"geometry_type"="POINT"})
     */
    private $geo;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min=0, max=255)
     */
    private $straat;

    /**
     * @var string
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(min=0, max=50)
     */
    private $huisnummer;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $verwijderd;

    /**
     * @var Actie[]
     * @ORM\OneToMany(targetEntity="Actie", mappedBy="ticket")
     * @ORM\OrderBy({"datumTijdAangemaakt"="DESC"})
     */
    private $acties;

    /**
     * @var Gebied
     * @ORM\ManyToOne(targetEntity="Gebied")
     * @ORM\JoinColumn(name="gebied_id", referencedColumnName="id", nullable=false)
     */
    private $gebied;

    public function __construct()
    {
        $this->datumTijdAangemaakt= new \DateTime();
        $this->verwijderd = false;
        $this->acties = new ArrayCollection();
        $this->status = false;
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
     * Set bron
     *
     * @param string $bron
     *
     * @return Ticket
     */
    public function setBron($bron)
    {
        $this->bron = $bron;

        return $this;
    }

    /**
     * Get bron
     *
     * @return string
     */
    public function getBron()
    {
        return $this->bron;
    }

    /**
     * Set datumTijdAangemaakt
     *
     * @param \DateTime $datumTijdAangemaakt
     *
     * @return Ticket
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
     * Set datumTijdGewijzigd
     *
     * @param \DateTime $datumTijdGewijzigd
     *
     * @return Ticket
     */
    public function setDatumTijdGewijzigd($datumTijdGewijzigd)
    {
        $this->datumTijdGewijzigd = $datumTijdGewijzigd;

        return $this;
    }

    /**
     * Get datumTijdGewijzigd
     *
     * @return \DateTime
     */
    public function getDatumTijdGewijzigd()
    {
        return $this->datumTijdGewijzigd;
    }

    /**
     * Set datumTijdGesloten
     *
     * @param \DateTime $datumTijdGesloten
     *
     * @return Ticket
     */
    public function setDatumTijdGesloten($datumTijdGesloten)
    {
        $this->datumTijdGesloten = $datumTijdGesloten;

        return $this;
    }

    /**
     * Get datumTijdGesloten
     *
     * @return \DateTime
     */
    public function getDatumTijdGesloten()
    {
        return $this->datumTijdGesloten;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Ticket
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set oplossing
     *
     * @param string $oplossing
     *
     * @return Ticket
     */
    public function setOplossing($oplossing)
    {
        $this->oplossing = $oplossing;

        return $this;
    }

    /**
     * Get oplossing
     *
     * @return string
     */
    public function getOplossing()
    {
        return $this->oplossing;
    }

    /**
     * Set geo
     *
     * @param geography $geo
     *
     * @return Ticket
     */
    public function setGeo($geo)
    {
        $this->geo = $geo;

        return $this;
    }

    /**
     * Get geo
     *
     * @return geography
     */
    public function getGeo()
    {
        return $this->geo;
    }

    /**
     * Set straat
     *
     * @param string $straat
     *
     * @return Ticket
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
     * @return Ticket
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
     * Set verwijderd
     *
     * @param boolean $verwijderd
     *
     * @return Ticket
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
     * Set medewerker
     *
     * @param \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Medewerker $medewerker
     *
     * @return Ticket
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
     * Set onderneming
     *
     * @param \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Onderneming $onderneming
     *
     * @return Ticket
     */
    public function setOnderneming(\GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Onderneming $onderneming = null)
    {
        $this->onderneming = $onderneming;

        return $this;
    }

    /**
     * Get onderneming
     *
     * @return \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Onderneming
     */
    public function getOnderneming()
    {
        return $this->onderneming;
    }

    /**
     * Add acty
     *
     * @param \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Actie $acty
     *
     * @return Ticket
     */
    public function addActy(\GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Actie $acty)
    {
        $this->acties[] = $acty;

        return $this;
    }

    /**
     * Remove acty
     *
     * @param \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Actie $acty
     */
    public function removeActy(\GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Actie $acty)
    {
        $this->acties->removeElement($acty);
    }

    /**
     * Get acties
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActies()
    {
        return $this->acties;
    }

    /**
     * Set gebied
     *
     * @param \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied $gebied
     *
     * @return Ticket
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
