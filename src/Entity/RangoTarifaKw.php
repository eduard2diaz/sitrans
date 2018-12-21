<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @UniqueEntity(fields={"inicio","tarifas"})
 * @UniqueEntity(fields={"fin","tarifas"})
 */
class RangoTarifaKw
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *      min = 0,
     *      minMessage = "El rango inicial debe ser mayor o igual a  {{ limit }} kw",
     * )
     */
    private $inicio;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $fin;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range(
     *      min = 0.001,
     *      minMessage = "El valor debe ser mayor o igual a  {{ limit }}",
     * )
     */
    private $valor;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TarifaKw", inversedBy="rangoTarifaKws")
     */
    private $tarifas;

    public function getId()
    {
        return $this->id;
    }

    public function getInicio(): ?int
    {
        return $this->inicio;
    }

    public function setInicio(int $inicio): self
    {
        $this->inicio = $inicio;

        return $this;
    }

    public function getFin(): ?int
    {
        return $this->fin;
    }

    public function setFin($fin=null): self
    {
        $this->fin = $fin;

        return $this;
    }

    public function getValor(): ?float
    {
        return $this->valor;
    }

    public function setValor(float $valor): self
    {
        $this->valor = $valor;

        return $this;
    }

    public function getTarifas(): ?TarifaKw
    {
        return $this->tarifas;
    }

    public function setTarifas(?TarifaKw $tarifas): self
    {
        $this->tarifas = $tarifas;

        return $this;
    }

    public function __toString()
    {
     return $this->getTarifas();
    }
}
