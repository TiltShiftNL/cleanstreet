<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 */
class Notitie extends Ticket
{
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(min=0, max=4096)
     */
    private $tekst;

    /**
     * @var Foto[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="Foto", cascade={"persist"})
     * @ORM\JoinTable(
     *  name="notitie_fotos",
     *  joinColumns={@ORM\JoinColumn(name="notitie_id", referencedColumnName="id", onDelete="CASCADE")},
     *  inverseJoinColumns={@ORM\JoinColumn(name="foto_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private $fotos;

    /**
     * @var Categorie[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="Categorie")
     * @ORM\JoinTable(
     *  name="ticket_categorie",
     *  joinColumns={@ORM\JoinColumn(name="ticket_id", referencedColumnName="id")},
     *  inverseJoinColumns={@ORM\JoinColumn(name="categorie_id", referencedColumnName="id")}
     * )
     */
    private $categorien;

    public function __construct()
    {
        parent::__construct();
        $this->fotos = new ArrayCollection();
        $this->categorien = new ArrayCollection();
    }

    public function getType()
    {
        return 'notitie';
    }

    /**
     * Set tekst
     *
     * @param string $tekst
     *
     * @return Notitie
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

    /**
     * Add foto
     *
     * @param \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Foto $foto
     *
     * @return Notitie
     */
    public function addFoto(\GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Foto $foto)
    {
        $this->fotos[] = $foto;

        return $this;
    }

    /**
     * Remove foto
     *
     * @param \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Foto $foto
     */
    public function removeFoto(\GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Foto $foto)
    {
        $this->fotos->removeElement($foto);
    }

    /**
     * Get fotos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFotos()
    {
        return $this->fotos;
    }

    /**
     * @return \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Categorie[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getCategorien()
    {
        return $this->categorien;
    }

    public function clearCategorien()
    {
        $this->categorien->clear();
    }

    /**
     * @param Categorie $categorie
     */
    public function addCategorie(Categorie $categorie)
    {
        if ($this->hasCategorie($categorie) === false) {
            $this->categorien->add($categorie);
        }
    }

    /**
     * @param Categorie $categorie
     */
    public function removeCategorie(Categorie $categorie)
    {
        if ($this->hasCategorie($categorie) === true) {
            $this->categorien->removeElement($categorie);
        }
    }

    /**
     * @param Categorie $categorie
     * @return boolean
     */
    public function hasCategorie(Categorie $categorie)
    {
        return $this->categorien->contains($categorie);
    }
}
