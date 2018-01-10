<?php
namespace GemeenteAmsterdam\HeelEnSchoonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table
 */
class Dienst
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=true)
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $start;

    /**
     * @var Medewerker
     * @ORM\ManyToOne(targetEntity="Medewerker")
     * @ORM\JoinColumn(name="medewerker_id", referencedColumnName="id", nullable=false)
     */
    private $medewerker;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $eind;

    /**
     * @var Gebied
     * @ORM\ManyToOne(targetEntity="Gebied")
     * @ORM\JoinColumn(name="gebied_id", referencedColumnName="id", nullable=false)
     */
    private $gebied;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set start
     *
     * @param \DateTime $start
     *
     * @return Dienst
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set eind
     *
     * @param \DateTime $eind
     *
     * @return Dienst
     */
    public function setEind($eind)
    {
        $this->eind = $eind;

        return $this;
    }

    /**
     * Get eind
     *
     * @return \DateTime
     */
    public function getEind()
    {
        return $this->eind;
    }

    /**
     * Set medewerker
     *
     * @param \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Medewerker $medewerker
     *
     * @return Dienst
     */
    public function setMedewerker(\GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Medewerker $medewerker)
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
     * Set gebied
     *
     * @param \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Gebied $gebied
     *
     * @return Dienst
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
