<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Validator\EsUltimaOperacionKwArea as UltimaOperacionConstraint;

/**
 * @ORM\Entity
 * @UltimaOperacionConstraint(foreign="reloj",fecha="fecha")
 */
class LecturaReloj
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
     * @var \Reloj
     *
     * @ORM\ManyToOne(targetEntity="Reloj")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="reloj", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $reloj;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range(
     *      min = 0,
     *      minMessage = "La lectura debe ser igual o superior a {{ limit }} Kw",
     * )
     */
    private $lectura;

    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
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

    public function getReloj(): ?Reloj
    {
        return $this->reloj;
    }

    public function setReloj(?Reloj $reloj): self
    {
        $this->reloj = $reloj;

        return $this;
    }

    public function getLectura(): ?float
    {
        return $this->lectura;
    }

    public function setLectura(float $lectura): self
    {
        $this->lectura = $lectura;

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

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        $path=$this->getId()==null ? 'reloj' : null;
        if(null==$this->getReloj())
            $context->buildViolation('Seleccione un reloj')
                ->atPath($path)
                ->addViolation();
        if(!$this->getReloj()->getActivo())
            $context->buildViolation('Seleccione un reloj activo')
                ->atPath($path)
                ->addViolation();

        if($this->getReloj()->getKwrestante()<$this->getLectura())
            $context->buildViolation('El reloj seleccionado no tiene suficientes kilowatts')
                ->atPath($path)
                ->addViolation();

        if (null == $this->getUsuario())
            $context->buildViolation('Seleccione un usuario')->addViolation();
    }

}
