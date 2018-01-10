<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table
 */
class TelefoonboekEntry
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank
     * @Assert\Length(min=1, max=255)
     */
    private $titel;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(min=0, max=4096)
     */
    private $omschrijving;

    /**
     * @var string
     * @ORM\Column(type="string", length=25, nullable=true)
     * @Assert\Length(min=0, max=25)
     */
    private $telefoon;

    /**
     * @var string
     * @ORM\Column(type="string", length=25, nullable=true)
     * @Assert\Length(min=0, max=25)
     * @Assert\Email
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min=0, max=255)
     */
    private $url;

    /**
     * @var string
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $verwijderd;

    /**
     * @var Gebied
     * @ORM\ManyToOne(targetEntity="Gebied")
     * @ORM\JoinColumn(name="gebied_id", referencedColumnName="id", nullable=false)
     */
    private $gebied;

    public function __construct()
    {
        $this->verwijderd = false;
    }

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
     * Set titel
     *
     * @param string $titel
     *
     * @return TelefoonboekEntry
     */
    public function setTitel($titel)
    {
        $this->titel = $titel;

        return $this;
    }

    /**
     * Get titel
     *
     * @return string
     */
    public function getTitel()
    {
        return $this->titel;
    }

    /**
     * Set omschrijving
     *
     * @param string $omschrijving
     *
     * @return TelefoonboekEntry
     */
    public function setOmschrijving($omschrijving)
    {
        $this->omschrijving = $omschrijving;

        return $this;
    }

    /**
     * Get omschrijving
     *
     * @return string
     */
    public function getOmschrijving()
    {
        return $this->omschrijving;
    }

    /**
     * Set telefoon
     *
     * @param string $telefoon
     *
     * @return TelefoonboekEntry
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
     * Set email
     *
     * @param string $email
     *
     * @return TelefoonboekEntry
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
     * Set url
     *
     * @param string $url
     *
     * @return TelefoonboekEntry
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set verwijderd
     *
     * @param boolean $verwijderd
     *
     * @return TelefoonboekEntry
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
