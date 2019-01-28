<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @UniqueEntity(fields={"cierre","tarjeta"}, errorPath="tarjeta")
 */
class CierreMesTarjeta
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
     * @var \CierreMesCombustible
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\CierreMesCombustible")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cierre", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $cierre;

    /**
     * @var \Tarjeta
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Tarjeta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tarjeta", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $tarjeta;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *      min = 0,
     *      minMessage = "El combustible restante debe ser igual o superior a {{ limit }}",
     * )
     */
    private $restantecombustible;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range(
     *      min = 0,
     *      minMessage = "El efectivo restante debe ser igual o superior a {{ limit }}",
     * )
     */
    private $restanteefectivo;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *      min = 0,
     *      minMessage = "El combustible consumido debe ser igual o superior a {{ limit }}",
     * )
     */
    private $combustibleconsumido;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range(
     *      min = 0,
     *      minMessage = "El efectivo restante debe ser igual o superior a {{ limit }}",
     * )
     */
    private $efectivoconsumido;

    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $usuario;

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
     * @return mixed
     */
    public function getRestantecombustible()
    {
        return $this->restantecombustible;
    }

    /**
     * @param mixed $restantecombustible
     */
    public function setRestantecombustible($restantecombustible): void
    {
        $this->restantecombustible = $restantecombustible;
    }

    public function getRestanteefectivo(): ?float
    {
        return $this->restanteefectivo;
    }

    public function setRestanteefectivo(float $restanteefectivo): self
    {
        $this->restanteefectivo = $restanteefectivo;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCierre()
    {
        return $this->cierre;
    }

    /**
     * @param mixed $cierre
     */
    public function setCierre($cierre): void
    {
        $this->cierre = $cierre;
    }

    public function getCombustibleconsumido(): ?int
    {
        return $this->combustibleconsumido;
    }

    public function setCombustibleconsumido(int $combustibleconsumido): self
    {
        $this->combustibleconsumido = $combustibleconsumido;

        return $this;
    }

    public function getEfectivoconsumido(): ?float
    {
        return $this->efectivoconsumido;
    }

    public function setEfectivoconsumido(float $efectivoconsumido): self
    {
        $this->efectivoconsumido = $efectivoconsumido;

        return $this;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if(null==$this->getTarjeta())
            $context->buildViolation('Seleccione una tarjeta')
                ->atPath('tarjeta')
                ->addViolation();
        elseif(!$this->getTarjeta()->getActivo())
            $context->buildViolation('Seleccione una tarjeta activa')
                ->atPath('tarjeta')
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
