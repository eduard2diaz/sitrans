<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Validator\Period as PeriodConstraint;
use App\Validator\Importe as ImporteConstraint;

/**
 * Hojaruta
 *
 * @ORM\Table(name="hojaruta", indexes={@ORM\Index(name="IDX_8D8AE40175F7A2AF", columns={"tipoactividad"}), @ORM\Index(name="IDX_8D8AE401C9FA1603", columns={"vehiculo"})})
 * @ORM\Entity
 * @PeriodConstraint(from="fechasalida",to="fechallegada",foreign="vehiculo",message="Ya existe una hoja de ruta para el período indicado")
 * @ImporteConstraint(fecha="fechasalida",litros="litrosconsumidos",foreign="vehiculo",importe="importe")
 */
class Hojaruta
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="hruta_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codigo", type="string", nullable=false)
     */
    private $codigo;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fechasalida", type="datetime", nullable=false)
     */
    private $fechasalida;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fechallegada", type="datetime", nullable=true)
     */
    private $fechallegada;

    /**
     * @var string|null
     *
     * @ORM\Column(name="origen", type="string", nullable=false)
     */
    private $origen;

    /**
     * @var string|null
     *
     * @ORM\Column(name="destino", type="string", nullable=false)
     */
    private $destino;

    /**
     * @var int|null
     *
     * @ORM\Column(name="kmrecorrido", type="integer", nullable=false)
     * @Assert\Range(
     *      min = 1,
     *      minMessage = "Los kilómetros recorridos deben ser mayor o igual a  {{ limit }} km",
     * )
     */
    private $kmrecorrido;

    /**
     * @var string|null
     *
     * @ORM\Column(name="descripcion", type="text", nullable=false)
     */
    private $descripcion;

    /**
     * @var \Tipoactividad
     *
     * @ORM\ManyToOne(targetEntity="Tipoactividad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipoactividad", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $tipoactividad;

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
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *      min = 1,
     *      minMessage = "Los litros consumidos deben ser mayor o igual a  {{ limit }}",
     * )
     */
    private $litrosconsumidos;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range(
     *      min = 0.1,
     *      minMessage = "El importe debe ser mayor o igual a  {{ limit }}",
     * )
     */
    private $importe;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(?string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getFechasalida(): ?\DateTimeInterface
    {
        return $this->fechasalida;
    }

    public function setFechasalida(?\DateTimeInterface $fechasalida): self
    {
        $this->fechasalida = $fechasalida;

        return $this;
    }

    public function getFechallegada(): ?\DateTimeInterface
    {
        return $this->fechallegada;
    }

    public function setFechallegada(?\DateTimeInterface $fechallegada): self
    {
        $this->fechallegada = $fechallegada;

        return $this;
    }

    public function getOrigen(): ?string
    {
        return $this->origen;
    }

    public function setOrigen(?string $origen): self
    {
        $this->origen = $origen;

        return $this;
    }

    public function getDestino(): ?string
    {
        return $this->destino;
    }

    public function setDestino(?string $destino): self
    {
        $this->destino = $destino;

        return $this;
    }

    public function getKmrecorrido(): ?int
    {
        return $this->kmrecorrido;
    }

    public function setKmrecorrido(?int $kmrecorrido): self
    {
        $this->kmrecorrido = $kmrecorrido;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getTipoactividad(): ?Tipoactividad
    {
        return $this->tipoactividad;
    }

    public function setTipoactividad(?Tipoactividad $tipoactividad): self
    {
        $this->tipoactividad = $tipoactividad;

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

    public function getLitrosconsumidos(): ?int
    {
        return $this->litrosconsumidos;
    }

    public function setLitrosconsumidos(int $litrosconsumidos): self
    {
        $this->litrosconsumidos = $litrosconsumidos;

        return $this;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if(null==$this->getTipoactividad())
            $context->buildViolation('Seleccione el tipo de actividad')
                ->atPath('tipoactividad')
                ->addViolation();

        $path=$this->getId()!= null ? null : 'vehiculo';
        if(null==$this->getVehiculo())
            $context->buildViolation('Seleccione el vehículo')
                ->atPath($path)
                ->addViolation();

        elseif(0!=$this->getVehiculo()->getEstado())
            $context->buildViolation('Seleccione el vehículo activo')
                ->atPath($path)
                ->addViolation();
        elseif(null==$this->getVehiculo()->getResponsable())
            $context->buildViolation('Seleccione el vehículo con responsable')
                ->atPath($path)
                ->addViolation();
        elseif(!$this->getVehiculo()->getResponsable()->getActivo())
            $context->buildViolation('Seleccione el vehículo con responsable activo')
                ->atPath($path)
                ->addViolation();

        if($this->getFechasalida()>=$this->getFechallegada())
            $context->buildViolation('Compruebe las fecha de salida y llegada')
                ->atPath('fechasalida')
                ->addViolation();

        if(null==$this->getId() && $this->getLitrosconsumidos()>$this->getVehiculo()->getLitrosentanque())
            $context->buildViolation('Seleccione el vehículo con suficiente combustible')
                ->atPath('vehiculo')
                ->addViolation();

        $hoy=new \DateTime('today');

        $anno=$hoy->format('y');
        if($this->getFechasalida()->format('y')!=$anno)
            $context->buildViolation('Seleccione una fecha dentro del año actual')
                ->atPath('fechasalida')
                ->addViolation();
        $mes=$hoy->format('m');
        if($this->getFechasalida()->format('m')!=$mes)
            $context->buildViolation('Seleccione una fecha dentro del mes actual')
                ->atPath('fechasalida')
                ->addViolation();
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
