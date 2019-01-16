<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Institucion")
     * @ORM\JoinColumn(nullable=false)
     */
    private $institucion;

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
        $hoy=new \DateTime('today');
        $anno=$hoy->format('y');
        if($this->getFecha()->format('y')!=$anno)
            $context->buildViolation('Seleccione una fecha dentro del año actual')
                ->atPath('fecha')
                ->addViolation();
        $mes=$hoy->format('m');
        if($this->getFecha()->format('m')!=$mes)
            $context->buildViolation('Seleccione una fecha dentro del mes actual')
                ->atPath('fecha')
                ->addViolation();
    }


}
