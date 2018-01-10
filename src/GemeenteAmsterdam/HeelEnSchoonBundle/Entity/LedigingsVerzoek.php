<?php
namespace GemeenteAmsterdam\HeelEnSchoonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class LedigingsVerzoek extends Ticket
{
    /**
     * @var OndernemersBak
     * @ORM\ManyToOne(targetEntity="OndernemersBak")
     * @ORM\JoinColumn(name="ondernemers_bak_uuid", referencedColumnName="uuid", nullable=true)
     * @Assert\NotNull
     */
    private $ondernemersBak;

    public function __construct()
    {
        parent::__construct();
    }

    public function getType()
    {
        return 'ledigingsverzoek';
    }

    /**
     * Set ondernemersBak
     *
     * @param \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\OndernemersBak $ondernemersBak
     *
     * @return LedigingsVerzoek
     */
    public function setOndernemersBak(\GemeenteAmsterdam\HeelEnSchoonBundle\Entity\OndernemersBak $ondernemersBak)
    {
        $this->ondernemersBak = $ondernemersBak;

        return $this;
    }

    /**
     * Get ondernemersBak
     *
     * @return \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\OndernemersBak
     */
    public function getOndernemersBak()
    {
        return $this->ondernemersBak;
    }
}
