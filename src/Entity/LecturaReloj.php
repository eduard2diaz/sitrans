<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Reloj")
     * @ORM\JoinColumn(nullable=false)
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario")
     * @ORM\JoinColumn(nullable=false)
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

        $diferencia=$this->getLectura();


        if(!$this->getReloj()->getKwrestante()<$diferencia)
            $context->buildViolation('Seleccione un reloj activo')
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
