<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Validator\Importe as ImporteConstraint;

/**
 * @ORM\Entity
 * @ImporteConstraint(fecha="fecha",litros="monto",foreign="tarjeta",importe="cantefectivo")
 */
class AjusteTarjeta
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tarjeta")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tarjeta;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *      min = 1,
     *      minMessage = "Debe ajustar la tarjeta con al menos {{ limit }}litro",
     * )
     */
    private $monto;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range(
     *      min = 0.1,
     *      minMessage = "Debe ajustar la tarjeta con al menos ${{ limit }}",
     * )
     */
    private $cantefectivo;

    /**
     * @ORM\Column(type="integer")
     */
    private $tipo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    public function getId()
    {
        return $this->id;
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

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getMonto(): ?int
    {
        return $this->monto;
    }

    public function setMonto(int $monto): self
    {
        $this->monto = $monto;

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

    public function getTipo(): ?int
    {
        return $this->tipo;
    }

    public function getTipoToString()
    {
        $array=['Haber','Debe'];
        return $array[$this->tipo];
    }

    public function setTipo(int $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        $path=$this->getId() ? null : 'tarjeta';
        if(null==$this->getTarjeta())
            $context->buildViolation('Seleccione una tarjeta')
                ->atPath($path)
                ->addViolation();
        elseif(!$this->getTarjeta()->getActivo())
            $context->buildViolation('Seleccione una tarjeta activa')
                ->atPath($path)
                ->addViolation();

        if($this->getTipo()<0 || $this->getTipo()>1)
                    $context->buildViolation('Compruebe el tipo de operacion')
                        ->atPath('tipo')
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
