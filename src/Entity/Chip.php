<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Validator\Importe as ImporteConstraint;

/**
 * Chip
 *
 * @ORM\Table(name="chip", indexes={@ORM\Index(name="IDX_AA29BCBBAE90B786", columns={"tarjeta"}), @ORM\Index(name="IDX_AA29BCBB5F42E8B9", columns={"cupet"})})
 * @ORM\Entity
 * @ImporteConstraint(fecha="fecha",litros="litrosextraidos",foreign="tarjeta",importe="importe")
 */
class Chip
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="chip_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="numerocomprobante", type="string", nullable=true)
     */
    private $numerocomprobante;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=false)
     */
    private $fecha;

    /**
     * @var int|null
     *
     * @ORM\Column(name="idfisico", type="integer", nullable=false)
     */
    private $idfisico;

    /**
     * @var int|null
     *
     * @ORM\Column(name="idlogico", type="integer", nullable=false)
     */
    private $idlogico;

    /**
     * @var integer|null
     *
     * @ORM\Column(name="moneda", type="integer", nullable=false)
     */
    private $moneda;

    /**
     * @var string|null
     *
     * @ORM\Column(name="servicio", type="string", nullable=false)
     */
    private $servicio;

    /**
     * @var float|null
     *
     * @ORM\Column(name="saldoinicial", type="float", precision=10, scale=0, nullable=false)
     * @Assert\Range(
     *      min = 1,
     *      minMessage = "El saldo inicial debe ser igual o superior a {{ limit }}",
     * )
     */
    private $saldoinicial;

    /**
     * @var int|null
     *
     * @ORM\Column(name="litrosextraidos", type="integer", nullable=false)
     * @Assert\Range(
     *      min = 1,
     *      minMessage = "La cantidad de litros debe ser igual o superior a {{ limit }}",
     * )
     */
    private $litrosextraidos;

    /**
     * @var float|null
     *
     * @ORM\Column(name="importe", type="float", precision=10, scale=0, nullable=false)
     * @Assert\Range(
     *      min = 0.1,
     *      minMessage = "El importe debe ser igual o superior a {{ limit }}",
     * )
     */
    private $importe;

    /**
     * @var \Tarjeta
     *
     * @ORM\ManyToOne(targetEntity="Tarjeta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tarjeta", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $tarjeta;

    /**
     * @var \Cupet
     *
     * @ORM\ManyToOne(targetEntity="Cupet")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cupet", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $cupet;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumerocomprobante(): ?string
    {
        return $this->numerocomprobante;
    }

    public function setNumerocomprobante(?string $numerocomprobante): self
    {
        $this->numerocomprobante = $numerocomprobante;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(?\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getIdfisico(): ?int
    {
        return $this->idfisico;
    }

    public function setIdfisico(?int $idfisico): self
    {
        $this->idfisico = $idfisico;

        return $this;
    }

    public function getIdlogico(): ?int
    {
        return $this->idlogico;
    }

    public function setIdlogico(?int $idlogico): self
    {
        $this->idlogico = $idlogico;

        return $this;
    }

    public function getMoneda(): ?int
    {
        return $this->moneda;
    }

    public function getMonedaToString()
    {
        $array=['Moneda Nacional','Divisa'];
        return $array[$this->moneda];
    }

    public function setMoneda(?int $moneda): self
    {
        $this->moneda = $moneda;

        return $this;
    }

    public function getServicio(): ?string
    {
        return $this->servicio;
    }

    public function setServicio(?string $servicio): self
    {
        $this->servicio = $servicio;

        return $this;
    }

    public function getSaldoinicial(): ?float
    {
        return $this->saldoinicial;
    }

    public function setSaldoinicial(?float $saldoinicial): self
    {
        $this->saldoinicial = $saldoinicial;

        return $this;
    }

    public function getLitrosextraidos(): ?int
    {
        return $this->litrosextraidos;
    }

    public function setLitrosextraidos(?int $litrosextraidos): self
    {
        $this->litrosextraidos = $litrosextraidos;

        return $this;
    }

    public function getImporte(): ?float
    {
        return $this->importe;
    }

    public function setImporte(?float $importe): self
    {
        $this->importe = $importe;

        return $this;
    }

    public function getTarjeta(): ?Tarjeta
    {
        return $this->tarjeta;
    }

    public function setTarjeta(?Tarjeta $tarjeta): self
    {
        $this->tarjeta = $tarjeta;

        return $this;
    }

    public function getCupet(): ?Cupet
    {
        return $this->cupet;
    }

    public function setCupet(?Cupet $cupet): self
    {
        $this->cupet = $cupet;

        return $this;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        $path=$this->getId()!=null ? null : 'tarjeta';
        if(null==$this->getTarjeta())
            $context->buildViolation('Seleccione la tarjeta')
                ->atPath($path)
                ->addViolation();
        else
            if(!$this->getTarjeta()->getActivo())
                $context->buildViolation('Seleccione una tarjeta activa')
                    ->atPath($path)
                    ->addViolation();
            else
            if(null==$this->getTarjeta()->getResponsable())
                $context->buildViolation('Seleccione una tarjeta que posea responsable')
                    ->atPath($path)
                    ->addViolation();
            elseif(!$this->getTarjeta()->getResponsable()->getActivo())
                $context->buildViolation('Seleccione una tarjeta que posea responsable activo')
                    ->atPath($path)
                    ->addViolation();

        if(null==$this->getCupet())
            $context->buildViolation('Seleccione el cupet')
                ->atPath('cupet')
                ->addViolation();

        if(0!=$this->getMoneda() && 1!=$this->getMoneda())
            $context->buildViolation('Seleccione el tipo de moneda')
                ->atPath('moneda')
                ->addViolation();

        if($this->getLitrosextraidos()>$this->getTarjeta()->getCantlitros())
            $context->buildViolation('La tarjeta seleccionada no posee la cantidad indicada de litros')
                ->atPath('litrosextraidos')
                ->addViolation();

        if($this->getLitrosextraidos()>$this->getSaldoinicial())
            $context->buildViolation('No puede extraer más combustible que el que posee la tarjeta')
                ->atPath('litrosextraidos')
                ->addViolation();

        if($this->getImporte()>$this->getTarjeta()->getCantefectivo())
            $context->buildViolation('La tarjeta seleccionada no posee la cantidad indicada de efectivo')
                ->atPath('importe')
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
