<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Validator\Period as PeriodConstraint;

/**
 * @ORM\Entity
 * @PeriodConstraint(from="fechainicio",to="fechafin",foreign="vehiculo",message="Ya existe un mantenimiento para el período indicado")
 */
class Mantenimiento
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Vehiculo")
     * @ORM\JoinColumn(nullable=false)
     */
    private $vehiculo;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fechainicio;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fechafin;

    /**
     * @ORM\Column(type="text")
     */
    private $descripcion;

    public function getId()
    {
        return $this->id;
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

    public function getFechainicio(): ?\DateTimeInterface
    {
        return $this->fechainicio;
    }

    public function setFechainicio(\DateTimeInterface $fechainicio): self
    {
        $this->fechainicio = $fechainicio;

        return $this;
    }

    public function getFechafin(): ?\DateTimeInterface
    {
        return $this->fechafin;
    }

    public function setFechafin(\DateTimeInterface $fechafin): self
    {
        $this->fechafin = $fechafin;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        $path=$this->getId() ? null : 'vehiculo';
        if(null==$this->getVehiculo())
            $context->buildViolation('Seleccione el vehículo')
                ->atPath($path)
                ->addViolation();
        elseif(1!=$this->getVehiculo()->getEstado())
            $context->buildViolation('Seleccione el vehículo que se encuentre en mantenimiento')
                ->atPath($path)
                ->addViolation();
        elseif(null==$this->getVehiculo()->getResponsable())
            $context->buildViolation('Seleccione un vehículo con responsable')
                ->atPath($path)
                ->addViolation();
        elseif(!$this->getVehiculo()->getResponsable()->getActivo())
            $context->buildViolation('Seleccione el vehículo con responsable activo')
                ->atPath($path)
                ->addViolation();

        if($this->getFechafin()<=$this->getFechainicio())
            $context->buildViolation('Compruebe las fecha de inicio y fin')
                ->atPath('fechainicio')
                ->addViolation();

        $hoy=new \DateTime('today');

        $anno=$hoy->format('y');
        if($this->getFechainicio()->format('y')!=$anno)
            $context->buildViolation('Seleccione una fecha dentro del año actual')
                ->atPath('fechainicio')
                ->addViolation();
        $mes=$hoy->format('m');
        if($this->getFechainicio()->format('m')!=$mes)
            $context->buildViolation('Seleccione una fecha dentro del mes actual')
                ->atPath('fechainicio')
                ->addViolation();
    }
}
