<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Validator\Vehiculo as VehiculoConstraint;

/**
 * Vehiculo
 *
 * @ORM\Table(name="vehiculo", indexes={@ORM\Index(name="IDX_C9FA1603892BFD1A", columns={"tipocombustible"}), @ORM\Index(name="IDX_C9FA16031F7E3FEE", columns={"chofer"}), @ORM\Index(name="IDX_C9FA160352520D07", columns={"responsable"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"matricula"})
 * @VehiculoConstraint()
 */
class Vehiculo
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="vehiculo_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="matricula", type="string", nullable=false)
     */
    private $matricula;

    /**
     * @var string|null
     *
     * @ORM\Column(name="marca", type="string", nullable=false)
     */
    private $marca;

    /**
     * @var string|null
     *
     * @ORM\Column(name="modelo", type="string", nullable=false)
     */
    private $modelo;

    /**
     * @var float|null
     *
     * @ORM\Column(name="indconsumo", type="float", precision=10, scale=0, nullable=false)
     * @Assert\Range(
     *      min = 1,
     *      minMessage = "El índice de consumo debe ser al menos de  {{ limit }} km/litro",
     * )
     */
    private $indconsumo;

    /**
     * @var \Tipocombustible
     *
     * @ORM\ManyToOne(targetEntity="Tipocombustible")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipocombustible", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $tipocombustible;

    /**
     * @var \Tipovehiculo
     *
     * @ORM\ManyToOne(targetEntity="Tipovehiculo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipovehiculo", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $tipovehiculo;

    /**
     * @var \Chofer
     *
     * @ORM\ManyToOne(targetEntity="Chofer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="chofer", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $chofer;

    /**
     * @var \Responsable
     *
     * @ORM\ManyToOne(targetEntity="Responsable")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="responsable", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $responsable;

    /**
     * @ORM\Column(type="integer")
     */
    private $litrosentanque;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *      min = 1,
     *      minMessage = "Los kms/mantenimiento deben ser mayor o igual a {{ limit }}",
     * )
     */
    private $kmsxmantenimiento;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *      min = 0,
     *      max = 4,
     * )
     */
    private $estado;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Institucion")
     * @ORM\JoinColumn(nullable=false)
     */
    private $institucion;

    public function __construct()
    {
        $this->litrosentanque=0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMatricula(): ?string
    {
        return $this->matricula;
    }

    public function setMatricula(?string $matricula): self
    {
        $this->matricula = $matricula;

        return $this;
    }

    public function getMarca(): ?string
    {
        return $this->marca;
    }

    public function setMarca(?string $marca): self
    {
        $this->marca = $marca;

        return $this;
    }

    public function getModelo(): ?string
    {
        return $this->modelo;
    }

    public function setModelo(?string $modelo): self
    {
        $this->modelo = $modelo;

        return $this;
    }

    public function getIndconsumo(): ?float
    {
        return $this->indconsumo;
    }

    public function setIndconsumo(?float $indconsumo): self
    {
        $this->indconsumo = $indconsumo;

        return $this;
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

    /**
     * @return \Tipovehiculo
     */
    public function getTipovehiculo(): ?Tipovehiculo
    {
        return $this->tipovehiculo;
    }

    /**
     * @param \Tipovehiculo $tipovehiculo
     */
    public function setTipovehiculo(?Tipovehiculo $tipovehiculo): void
    {
        $this->tipovehiculo = $tipovehiculo;
    }

    public function getChofer(): ?Chofer
    {
        return $this->chofer;
    }

    public function setChofer(?Chofer $chofer): self
    {
        $this->chofer = $chofer;

        return $this;
    }

    public function getResponsable(): ?Responsable
    {
        return $this->responsable;
    }

    public function setResponsable(?Responsable $responsable): self
    {
        $this->responsable = $responsable;

        return $this;
    }

    public function getLitrosentanque(): ?int
    {
        return $this->litrosentanque;
    }

    public function setLitrosentanque(int $litrosentanque): self
    {
        $this->litrosentanque = $litrosentanque;

        return $this;
    }

    public function getKmsxmantenimiento(): ?int
    {
        return $this->kmsxmantenimiento;
    }

    public function setKmsxmantenimiento(int $kmsxmantenimiento): self
    {
        $this->kmsxmantenimiento = $kmsxmantenimiento;
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

    public function getEstado(): ?int
    {
        return $this->estado;
    }

    public function getEstadoToString(){
        $estados=['Activo','En mantenimiento o reparación','Inactivo temporalmente','Pendiente a baja','Baja'];
        return $estados[$this->getEstado()];
    }

    public function setEstado(int $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function __toString()
    {
        return $this->getMarca().' '.$this->getMatricula();
    }


    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if(null==$this->getChofer())
            $context->buildViolation('Seleccione un chofer')
                ->atPath('chofer')
                ->addViolation();
        elseif(!$this->getChofer()->getActivo())
            $context->buildViolation('Seleccione un chofer activo')
                ->atPath('chofer')
                ->addViolation();
        else
            foreach ($this->getTipovehiculo()->getIdlicencia() as $value){
                if(!$this->getChofer()->getIdlicencia()->contains($value)){
                    $context->buildViolation('El chofer seleccionado no posee la licencia necesaria')
                        ->atPath('chofer')
                        ->addViolation();
                    break;
                }
            }

        if(null==$this->getResponsable())
            $context->buildViolation('Seleccione un responsable')
                ->atPath('responsable')
                ->addViolation();
        elseif(!$this->getResponsable()->getActivo())
            $context->buildViolation('Seleccione un responsable activo')
                ->atPath('responsable')
                ->addViolation();
        else {
            $total_tarjetas=$this->getResponsable()->getTarjetas()->count();

            if($total_tarjetas!=1 )
            $context->buildViolation('Seleccione un responsable que posea una única tarjeta')
                ->atPath('responsable')
                ->addViolation();
            elseif(!$this->getResponsable()->getTarjetas()->first()->getActivo())
            $context->buildViolation('Seleccione un responsable que posea una tarjeta activa')
                ->atPath('responsable')
                ->addViolation();
        }
    }



}
