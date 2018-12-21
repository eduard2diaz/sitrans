<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class PrecioCombustible
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tipocombustible")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tipocombustible;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha;

    /**
     * @ORM\Column(type="float")
     */
    private $importe;

    public function getId()
    {
        return $this->id;
    }

    public function getTipocombustible(): ?Tipocombustible
    {
        return $this->tipocombustible;
    }

    public function setTipocombustible(?Tipocombustible $tipocombustible): self
    {
        $this->tipocombustible = $tipocombustible;

        return $this;
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

    public function getImporte(): ?float
    {
        return $this->importe;
    }

    public function setImporte(float $importe): self
    {
        $this->importe = $importe;

        return $this;
    }

    public function __toString()
    {
       return $this->getTipocombustible()->getNombre().': '.$this->getFecha()->format('d-m-Y');
    }
}
