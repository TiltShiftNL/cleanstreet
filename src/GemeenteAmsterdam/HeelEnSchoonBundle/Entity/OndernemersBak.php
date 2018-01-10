<?php
namespace GemeenteAmsterdam\HeelEnSchoonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uq_kenmerk", columns={"kenmerk"})}
 * )
 */
class OndernemersBak
{
    /**
     * @var string
     * @ORM\Column(type="string", length=36, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $uuid;

    /**
     * @var string
     * @ORM\Column(type="string", length=4, nullable=false)
     * @Assert\NotBlank
     * @Assert\Length(min=4, max=4)
     */
    private $kenmerk;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     * @Assert\NotNull
     * @Assert\Type("boolean")
     */
    private $verwijderd;

    /**
     * @var Onderneming
     * @ORM\ManyToOne(targetEntity="Onderneming", inversedBy="ondernemersBakken")
     * @ORM\JoinColumn(name="onderneming_id", referencedColumnName="id", nullable=false)
     * @Assert\NotNull
     */
    private $onderneming;

    /**
     * @var Foto[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="Foto", cascade={"persist"})
     * @ORM\JoinTable(
     *  name="ondernemers_bak_fotos",
     *  joinColumns={@ORM\JoinColumn(name="ondernemers_bak_uuid", referencedColumnName="uuid", onDelete="CASCADE")},
     *  inverseJoinColumns={@ORM\JoinColumn(name="foto_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private $fotos;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fotos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->verwijderd = false;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set kenmerk
     *
     * @param string $kenmerk
     *
     * @return OndernemersBak
     */
    public function setKenmerk($kenmerk)
    {
        $this->kenmerk = $kenmerk;

        return $this;
    }

    /**
     * Get kenmerk
     *
     * @return string
     */
    public function getKenmerk()
    {
        return $this->kenmerk;
    }

    /**
     * Set verwijderd
     *
     * @param boolean $verwijderd
     *
     * @return OndernemersBak
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
     * Set onderneming
     *
     * @param \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Onderneming $onderneming
     *
     * @return OndernemersBak
     */
    public function setOnderneming(\GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Onderneming $onderneming)
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
     * Add foto
     *
     * @param \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Foto $foto
     *
     * @return OndernemersBak
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
}
