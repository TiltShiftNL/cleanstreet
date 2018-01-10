<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 */
class FotoActie extends Actie
{
    /**
     * @var Foto[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="Foto", cascade={"persist"})
     * @ORM\JoinTable(
     *  name="actie_fotos",
     *  joinColumns={@ORM\JoinColumn(name="actie_id", referencedColumnName="id", onDelete="CASCADE")},
     *  inverseJoinColumns={@ORM\JoinColumn(name="foto_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     * @Assert\Count(min=1)
     */
    private $fotos;

    public function __construct()
    {
        parent::__construct();
        $this->fotos = new ArrayCollection();
    }

    public function getType()
    {
        return 'foto';
    }

    /**
     * Add foto
     *
     * @param \GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Foto $foto
     *
     * @return FotoActie
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
