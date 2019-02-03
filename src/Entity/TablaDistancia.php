<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Validator\TablaDistancia as TablaDistanciaConstraint;

/**
 * @ORM\Entity
 * @TablaDistanciaConstraint(origen="origen",destino="destino")
 */
class TablaDistancia
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $origen;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $destino;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range(
     *      min = 0.1,
     *      minMessage = "Los kilómetros deben ser igual o superior a {{ limit }} Km",
     * )
     */
    private $kms;

    public function getId()
    {
        return $this->id;
    }

    public function getOrigen(): ?string
    {
        return $this->origen;
    }

    public function setOrigen(string $origen): self
    {
        $this->origen = $origen;

        return $this;
    }

    public function getDestino(): ?string
    {
        return $this->destino;
    }

    public function setDestino(string $destino): self
    {
        $this->destino = $destino;

        return $this;
    }

    public function getKms(): ?float
    {
        return $this->kms;
    }

    public function setKms(float $kms): self
    {
        $this->kms = $kms;

        return $this;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if($this->getOrigen()==$this->getDestino())
            $context->buildViolation('El origen y el destino no deben coincidir')
                ->atPath('destino')
                ->addViolation();
    }
}
