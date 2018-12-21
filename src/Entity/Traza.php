<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Traza
 *
 * @ORM\Table(name="traza", indexes={@ORM\Index(name="IDX_AD36B8A0C9FA1603", columns={"vehiculo"}), @ORM\Index(name="IDX_AD36B8A01F7E3FEE", columns={"chofer"}), @ORM\Index(name="IDX_AD36B8A052520D07", columns={"responsable"}), @ORM\Index(name="IDX_AD36B8A0D7943D68", columns={"area"})})
 * @ORM\Entity
 */
class Traza
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="traza_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=true)
     */
    private $fecha;

    /**
     * @var int|null
     *
     * @ORM\Column(name="identificador", type="integer", nullable=true)
     */
    private $identificador;

    /**
     * @var string|null
     *
     * @ORM\Column(name="entity", type="string", nullable=true)
     */
    private $entity;

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
     * @var \Area
     *
     * @ORM\ManyToOne(targetEntity="Area")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="area", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $area;

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
     * @ORM\Column(type="float", nullable=true)
     */
    private $indice_consumo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $combustibleentanque;

    public function __construct()
    {
        $this->setFecha(new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdentificador(): ?int
    {
        return $this->identificador;
    }

    public function setIdentificador(?int $identificador): self
    {
        $this->identificador = $identificador;

        return $this;
    }

    public function getEntity(): ?string
    {
        return $this->entity;
    }

    public function setEntity(?string $entity): self
    {
        $this->entity = $entity;

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

    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function setArea(?Area $area): self
    {
        $this->area = $area;

        return $this;
    }

    /**
     * @return \Tarjeta
     */
    public function getTarjeta(): Tarjeta
    {
        return $this->tarjeta;
    }

    /**
     * @param \Tarjeta $tarjeta
     */
    public function setTarjeta(Tarjeta $tarjeta): void
    {
        $this->tarjeta = $tarjeta;
    }

    public function getIndiceConsumo(): ?int
    {
        return $this->indice_consumo;
    }

    public function setIndiceConsumo(?int $indice_consumo): self
    {
        $this->indice_consumo = $indice_consumo;

        return $this;
    }

    public function getCombustibleentanque(): ?int
    {
        return $this->combustibleentanque;
    }

    public function setCombustibleentanque(?int $combustibleentanque): self
    {
        $this->combustibleentanque = $combustibleentanque;

        return $this;
    }

}
