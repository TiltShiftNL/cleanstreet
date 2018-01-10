<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @ORM\Table()
 * @Vich\Uploadable
 */
class Foto
{
    /**
     * @var int
     * @ORM\Column
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="foto_image", fileNameProperty="filename")
     *
     * @var File
     */
    private $imageFile;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $filename;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     * @Assert\NotNull
     */
    private $datumTijdUpload;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datumTijdExif;

    /**
     * @var string geo
     * @ORM\Column(type="geography", nullable=true, options={"geometry_type"="POINT"})
     */
    private $geoUpload;

    /**
     * @var string geo
     * @ORM\Column(type="geography", nullable=true, options={"geometry_type"="POINT"})
     */
    private $geoExif;

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
     * @param File $imageFile
     */
    public function setImageFile(File $imageFile = null)
    {
        $this->imageFile = $imageFile;
        if ($imageFile !== null) {
            $this->datumTijdUpload = new \DateTime();
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\File\File
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * Set filename
     *
     * @param string $filename
     *
     * @return Foto
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set datumTijdUpload
     *
     * @param \DateTime $datumTijdUpload
     *
     * @return Foto
     */
    public function setDatumTijdUpload($datumTijdUpload)
    {
        $this->datumTijdUpload = $datumTijdUpload;

        return $this;
    }

    /**
     * Get datumTijdUpload
     *
     * @return \DateTime
     */
    public function getDatumTijdUpload()
    {
        return $this->datumTijdUpload;
    }

    /**
     * Set datumTijdExif
     *
     * @param \DateTime $datumTijdExif
     *
     * @return Foto
     */
    public function setDatumTijdExif($datumTijdExif)
    {
        $this->datumTijdExif = $datumTijdExif;

        return $this;
    }

    /**
     * Get datumTijdExif
     *
     * @return \DateTime
     */
    public function getDatumTijdExif()
    {
        return $this->datumTijdExif;
    }

    /**
     * Set geoUpload
     *
     * @param geography $geoUpload
     *
     * @return Foto
     */
    public function setGeoUpload($geoUpload)
    {
        $this->geoUpload = $geoUpload;

        return $this;
    }

    /**
     * Get geoUpload
     *
     * @return geography
     */
    public function getGeoUpload()
    {
        return $this->geoUpload;
    }

    /**
     * Set geoExif
     *
     * @param geography $geoExif
     *
     * @return Foto
     */
    public function setGeoExif($geoExif)
    {
        $this->geoExif = $geoExif;

        return $this;
    }

    /**
     * Get geoExif
     *
     * @return geography
     */
    public function getGeoExif()
    {
        return $this->geoExif;
    }
}
