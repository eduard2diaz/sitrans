<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Validator\Tarjeta as TarjetaConstraint;

/**
 * Tarjeta
 *
 * @ORM\Table(name="tarjeta", indexes={@ORM\Index(name="IDX_AE90B786C21AA61D", columns={"tipotarjeta"}), @ORM\Index(name="IDX_AE90B786892BFD1A", columns={"tipocombustible"})})
 * @ORM\Entity
 * @TarjetaConstraint(codigo="codigo",tipotarjeta="tipotarjeta")
 */
class Tarjeta
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tarjeta_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codigo", type="string", nullable=true)
     */
    private $codigo;

    /**
     * @var \Tipotarjeta
     *
     * @ORM\ManyToOne(targetEntity="Tipotarjeta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipotarjeta", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $tipotarjeta;

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
     * @var bool|null
     *
     * @ORM\Column(name="activo", type="boolean", nullable=true)
     */
    private $activo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Responsable", inversedBy="tarjetas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="responsable", referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private $responsable;

    /**
     * @ORM\Column(type="float")
     */
    private $cantefectivo;

    public function __construct()
    {
        $this->cantefectivo=0;
        $this->setActivo(true);
    }

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

    public function getTipotarjeta(): ?Tipotarjeta
    {
        return $this->tipotarjeta;
    }

    public function setTipotarjeta(?Tipotarjeta $tipotarjeta): self
    {
        $this->tipotarjeta = $tipotarjeta;

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
     * @return bool|null
     */
    public function getActivo(): ?bool
    {
        return $this->activo;
    }

    /**
     * @param bool|null $activo
     */
    public function setActivo(?bool $activo): void
    {
        $this->activo = $activo;
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

    public function getCantefectivo(): ?float
    {
        return $this->cantefectivo;
    }

    public function setCantefectivo(float $cantefectivo): self
    {
        $this->cantefectivo = $cantefectivo;

        return $this;
    }

    public function __toString(){
        return $this->getCodigo();
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        /*
         * Detalles de validacion:
         * 1.Una tarjeta puede o no tener responsable.
         * 2.Si la tarjeta esta activa y tiene un responsable este tiene que estar activo
         * OJO: una tarjeta solo se desactiva cuando se pierde o cuando deja de estar en funcionamiento, por tanto al
         * desactivar una tarjeta el responsable no sufre cambios ni el vehiculo a su cargo
         * */
        if(null==$this->getTipocombustible())
            $context->buildViolation('Seleccione el tipo de combustible')
                ->atPath('tipocombustible')
                ->addViolation();

        if(null==$this->getTipotarjeta())
            $context->buildViolation('Seleccione el tipo de tarjeta')
                ->atPath('tipotarjeta')
                ->addViolation();

        if(true==$this->getActivo() && null!=$this->getResponsable() && false==$this->getResponsable()->getActivo())
            $context->buildViolation('Seleccione un responsable activo')
                ->addViolation();
    }
}
