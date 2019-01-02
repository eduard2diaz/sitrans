<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Validator\Importe as ImporteConstraint;
use App\Validator\CierreCombustible as CierreCombustibleConstraint;

/**
 * Recargatarjeta
 *
 * @ORM\Table(name="recargatarjeta", indexes={@ORM\Index(name="IDX_E2F3E177AE90B786", columns={"tarjeta"})})
 * @ORM\Entity
 * @ImporteConstraint(fecha="fecha",litros="cantidadlitros",foreign="tarjeta",importe="cantidadefectivo")
 * @CierreCombustibleConstraint(foreign="tarjeta",fecha="fecha")
 */
class Recargatarjeta
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="recargatarjeta_id_seq", allocationSize=1, initialValue=1)
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
     * @ORM\Column(name="cantidadlitros", type="integer", nullable=true)
     * @Assert\Range(
     *      min = 1,
     *      minMessage = "Debe recargar la tarjeta con al menos {{ limit }}litro",
     * )
     */
    private $cantidadlitros;

    /**
     * @var float|null
     *
     * @ORM\Column(name="cantidadefectivo", type="float", precision=10, scale=0, nullable=true)
     * @Assert\Range(
     *      min = 0.1,
     *      minMessage = "El precio de la recarga debe ser igual o superior a {{ limit }}",
     * )
     */
    private $cantidadefectivo;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

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

    public function getCantidadlitros(): ?int
    {
        return $this->cantidadlitros;
    }

    public function setCantidadlitros(?int $cantidadlitros): self
    {
        $this->cantidadlitros = $cantidadlitros;

        return $this;
    }

    public function getCantidadefectivo(): ?float
    {
        return $this->cantidadefectivo;
    }

    public function setCantidadefectivo(?float $cantidadefectivo): self
    {
        $this->cantidadefectivo = $cantidadefectivo;

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

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        $path=$this->getId()!= null ? null : 'tarjeta';
        if(null==$this->getTarjeta())
            $context->buildViolation('Seleccione una tarjeta')
                ->atPath($path)
                ->addViolation();
        elseif(!$this->getTarjeta()->getActivo())
                $context->buildViolation('Seleccione una tarjeta activa')
                    ->atPath($path)
                    ->addViolation();
        elseif(!$this->getTarjeta()->getResponsable())
            $context->buildViolation('No se puede recargar una tarjeta que no posee responsable')
                ->atPath($path)
                ->addViolation();
        elseif(!$this->getTarjeta()->getResponsable()->getActivo())
            $context->buildViolation('No se puede recargar una tarjeta que no posee responsable activo')
                ->atPath($path)
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
