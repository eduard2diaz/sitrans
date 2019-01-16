<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
     * @Assert\Range(
     *      min = 0.1,
     *      minMessage = "El precio debe ser mayor o igual a  {{ limit }}",
     * )
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

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (null==$this->getTipocombustible())
            $context->buildViolation('Seleccione el tipo de combustible')
                ->atPath('tipocombustible')
                ->addViolation();
    }
}
