<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PruebalitroRepository")
 */
class Pruebalitro
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Vehiculo")
     * @ORM\JoinColumn(nullable=false)
     */
    private $vehiculo;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range(
     *      min = 0.5,
     *      minMessage = "Los kilómetros recorridos deben ser mayor o igual a  {{ limit }} km",
     * )
     */
    private $kmsrecorrido;

    public function getId()
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getVehiculo(): ?Vehiculo
    {
        return $this->vehiculo;
    }

    public function setVehiculo(?Vehiculo $vehiculo): self
    {
        $this->vehiculo = $vehiculo;

        return $this;
    }

    public function getKmsrecorrido(): ?float
    {
        return $this->kmsrecorrido;
    }

    public function setKmsrecorrido(float $kmsrecorrido): self
    {
        $this->kmsrecorrido = $kmsrecorrido;

        return $this;
    }
}
