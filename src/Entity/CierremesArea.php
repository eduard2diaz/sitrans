<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @UniqueEntity(fields={"cierre","area"},errorPath="area",message="Ya existe un cierre para dicha área")
 */
class CierremesArea
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
     * @var \CierremesKw
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\CierremesKw")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cierre", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $cierre;

    /**
     * @var \Area
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Area")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="area", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $area;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range(
     *      min = 0,
     *      minMessage = "La cantidad de kilowatts restantes debe ser mayor o igual a {{ limit }} kw",
     * )
     */
    private $restante;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range(
     *      min = 0,
     *      minMessage = "La cantidad kilowatts consumido debe ser mayor o igual a {{ limit }} kw",
     * )
     */
    private $consumido;

    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $usuario;

    /**
     * @ORM\Column(type="float")
     */
    private $efectivoconsumido;

    /**
     * @ORM\Column(type="float")
     */
    private $efectivorestante;

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

    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function setArea(?Area $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function getRestante(): ?float
    {
        return $this->restante;
    }

    public function setRestante(float $restante): self
    {
        $this->restante = $restante;

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

    public function getConsumido(): ?float
    {
        return $this->consumido;
    }

    public function setConsumido(float $consumido): self
    {
        $this->consumido = $consumido;

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

    public function getEfectivoconsumido(): ?float
    {
        return $this->efectivoconsumido;
    }

    public function setEfectivoconsumido(float $efectivoconsumido): self
    {
        $this->efectivoconsumido = $efectivoconsumido;

        return $this;
    }

    public function getEfectivorestante(): ?float
    {
        return $this->efectivorestante;
    }

    public function setEfectivorestante(float $efectivorestante): self
    {
        $this->efectivorestante = $efectivorestante;

        return $this;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (null == $this->getArea())
            $context->buildViolation('Seleccione al menos un tipo de licencia')
                ->atPath('area')
                ->addViolation();
        if (null == $this->getCierre())
            $context->buildViolation('Seleccione un cierre')->addViolation();
        if (null == $this->getUsuario())
            $context->buildViolation('Seleccione un usuario')->addViolation();
    }
}
