<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Validator\PlanefectivoCuenta as PlanefectivoCuentaConstraint;


/**
 * @ORM\Entity
 * @PlanefectivoCuentaConstraint()
 */
class PlanefectivoCuenta
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @var \Planefectivo
     *
     * @ORM\ManyToOne(targetEntity="Planefectivo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="planefectivo", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $planefectivo;

    /**
     * @var \Cuenta
     *
     * @ORM\ManyToOne(targetEntity="Cuenta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cuenta", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $cuenta;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Subelemento", inversedBy="planefectivos")
     */
    private $subelemento;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Centrocosto", inversedBy="planefectivos")
     */
    private $centrocosto;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range(
     *      min = 1,
     *      minMessage = "Debe  el valor debe ser al menos {{ limit }}",
     * )
     */
    private $valor;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    public function __construct()
    {
        $this->subelemento = new ArrayCollection();
        $this->centrocosto = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Collection|Subelemento[]
     */
    public function getSubelemento(): Collection
    {
        return $this->subelemento;
    }

    public function addSubelemento(Subelemento $subelemento): self
    {
        if (!$this->subelemento->contains($subelemento)) {
            $this->subelemento[] = $subelemento;
        }

        return $this;
    }

    public function removeSubelemento(Subelemento $subelemento): self
    {
        if ($this->subelemento->contains($subelemento)) {
            $this->subelemento->removeElement($subelemento);
        }

        return $this;
    }

    /**
     * @return Collection|Centrocosto[]
     */
    public function getCentrocosto(): Collection
    {
        return $this->centrocosto;
    }

    public function addCentrocosto(Centrocosto $centrocosto): self
    {
        if (!$this->centrocosto->contains($centrocosto)) {
            $this->centrocosto[] = $centrocosto;
        }

        return $this;
    }

    public function removeCentrocosto(Centrocosto $centrocosto): self
    {
        if ($this->centrocosto->contains($centrocosto)) {
            $this->centrocosto->removeElement($centrocosto);
        }

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

    public function getCuenta(): ?Cuenta
    {
        return $this->cuenta;
    }

    public function setCuenta(?Cuenta $cuenta): self
    {
        $this->cuenta = $cuenta;

        return $this;
    }

    /**
     * @return \Planefectivo
     */
    public function getPlanefectivo(): Planefectivo
    {
        return $this->planefectivo;
    }

    /**
     * @param \Planefectivo $planefectivo
     */
    public function setPlanefectivo(Planefectivo $planefectivo): void
    {
        $this->planefectivo = $planefectivo;
    }


    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
       if($this->getSubelemento()->isEmpty())
            $context->buildViolation('Seleccione al menos un subelemento')
                ->atPath('subelemento')
                ->addViolation();

       if($this->getCentrocosto()->isEmpty())
            $context->buildViolation('Seleccione al menos un centro de costo')
                ->atPath('centrocosto')
                ->addViolation();

        if(null==$this->getCuenta())
            $context->buildViolation('Seleccione una cuenta')
                ->atPath('cuenta')
                ->addViolation();
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }
}
