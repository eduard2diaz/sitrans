<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Validator\Period as PeriodConstraint;

/**
 * @ORM\Entity
 * @PeriodConstraint(from="fechainicio",to="fechafin",foreign="vehiculo",message="Ya existe una prueba de litro para el período indicado")
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
    private $fechainicio;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fechafin;

    /**
     * @var \Vehiculo
     *
     * @ORM\ManyToOne(targetEntity="Vehiculo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vehiculo", referencedColumnName="id", onDelete="CASCADE")
     * })
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

    /**
     * @var \Institucion
     *
     * @ORM\ManyToOne(targetEntity="Institucion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="institucion", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $institucion;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFechainicio()
    {
        return $this->fechainicio;
    }

    /**
     * @param mixed $fechainicio
     */
    public function setFechainicio($fechainicio): void
    {
        $this->fechainicio = $fechainicio;
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

    /**
     * @return mixed
     */
    public function getInstitucion()
    {
        return $this->institucion;
    }

    /**
     * @param mixed $institucion
     */
    public function setInstitucion($institucion): void
    {
        $this->institucion = $institucion;
    }

    /**
     * @return mixed
     */
    public function getFechafin()
    {
        return $this->fechafin;
    }

    /**
     * @param mixed $fechafin
     */
    public function setFechafin($fechafin): void
    {
        $this->fechafin = $fechafin;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if(null==$this->getInstitucion())
            $context->buildViolation('Seleccione la institución')
                ->atPath('institucion')
                ->addViolation();

        if(null==$this->getVehiculo())
            $context->buildViolation('Seleccione el vehículo')
                ->atPath('vehiculo')
                ->addViolation();
        elseif(0!=$this->getVehiculo()->getEstado())
            $context->buildViolation('Seleccione el vehículo que se encuentre activo')
                ->atPath('vehiculo')
                ->addViolation();
        elseif(null==$this->getVehiculo()->getResponsable())
            $context->buildViolation('Seleccione un vehículo con responsable')
                ->atPath('vehiculo')
                ->addViolation();
        elseif(!$this->getVehiculo()->getResponsable()->getActivo())
            $context->buildViolation('Seleccione el vehículo con responsable activo')
                ->atPath('vehiculo')
                ->addViolation();
        if($this->getFechainicio()>=$this->getFechafin())
            $context->buildViolation('Compruebe las fecha de inicio y fin')
                ->atPath('fechafin')
                ->addViolation();
        $hoy=new \DateTime('today');
        $anno=$hoy->format('y');
        $mes=$hoy->format('m');
        if($this->getFechainicio()->format('y')!=$anno)
            $context->buildViolation('Seleccione una fecha dentro del año actual')
                ->atPath('fechainicio')
                ->addViolation();

        if($this->getFechainicio()->format('m')!=$mes)
            $context->buildViolation('Seleccione una fecha dentro del mes actual')
                ->atPath('fechainicio')
                ->addViolation();
    }
}
