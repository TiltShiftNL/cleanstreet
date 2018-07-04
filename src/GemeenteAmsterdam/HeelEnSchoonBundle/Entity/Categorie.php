<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table
 */
class Categorie
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
     * @ORM\Column(type="string", length=75, nullable=false)
     */
    private $hoofdcategorie;

    /**
     * @var string
     * @ORM\Column(type="string", length=75, nullable=false)
     */
    private $subcategorie;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pictogramNaam;

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
     * Get hoofdcategorie
     *
     * @return string
     */
    public function getHoofdcategorie()
    {
        return $this->hoofdcategorie;
    }

    /**
     * Get subcategorie
     *
     * @return string
     */
    public function getSubcategorie()
    {
        return $this->subcategorie;
    }

    /**
     * Get pictogramNaam
     *
     * @return string
     */
    public function getPictogramNaam()
    {
        return $this->pictogramNaam;
    }

    /**
     * Set hoofdcategorie
     *
     * @param string $hoofdcategorie
     */
    public function setHoofdcategorie($hoofdcategorie)
    {
        $this->hoofdcategorie = $hoofdcategorie;
    }

    /**
     * Set subcategorie
     *
     * @param string $subcategorie
     */
    public function setSubcategorie($subcategorie = null)
    {
        $this->subcategorie = $subcategorie;
    }

    /**
     * Set pictogramNaam
     *
     * @param string $pictogramNaam
     */
    public function setPictogramNaam($pictogramNaam = null)
    {
        $this->pictogramNaam = $pictogramNaam;
    }

    public function __toString()
    {
        return $this->subcategorie;
    }
}
