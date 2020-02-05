<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NasaImageRepository")
 */
class NasaImage
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @Groups({"info", "details"})
     */
    private $id;

    /**
     * @Groups({"info", "details"})
     * @ORM\Column(type="datetime", name="earth_date")
     */
    private $earthDate;

    /**
     * @Groups({"info", "details"})
     * @ORM\Column(type="string", length=20)
     */
    private $rover;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $cameraAbbreviation;

    /**
     * @Groups({"details"})
     * @ORM\Column(type="string", length=255)
     */
    private $imgSrc;

    /**
     * NasaImage constructor.
     * @param $id
     * @param $earthDate
     * @param $rover
     * @param $cameraAbbreviation
     * @param $imgSrc
     */
    public function __construct($id, $earthDate, $rover, $cameraAbbreviation, $imgSrc)
    {
        $this->id = $id;
        $this->earthDate = $earthDate;
        $this->rover = $rover;
        $this->cameraAbbreviation = $cameraAbbreviation;
        $this->imgSrc = $imgSrc;
    }

    /**
     * @Groups({"info", "details"})
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return \DateTimeInterface|null
     * @Groups({"info", "details"})
     */
    public function getEarthDate(): ?\DateTimeInterface
    {
        return $this->earthDate;
    }

    public function setEarthDate(\DateTimeInterface $earthDate): self
    {
        $this->earthDate = $earthDate;

        return $this;
    }

    /**
     * @Groups({"info", "details"})
     */
    public function getRover(): ?string
    {
        return $this->rover;
    }

    public function setRover(string $rover): self
    {
        $this->rover = $rover;

        return $this;
    }

    /**
     * @Groups({"info", "details"})
     */
    public function getCameraAbbreviation(): ?string
    {
        return $this->cameraAbbreviation;
    }

    public function setCameraAbbreviation(string $cameraAbbreviation): self
    {
        $this->cameraAbbreviation = $cameraAbbreviation;

        return $this;
    }

    /**
     * @Groups({"details"})
     */
    public function getImgSrc()
    {
        return $this->imgSrc;
    }

    /**
     * @param mixed $imgSrc
     * @return NasaImage
     */
    public function setImgSrc($imgSrc)
    {
        $this->imgSrc = $imgSrc;
        return $this;
    }

}
