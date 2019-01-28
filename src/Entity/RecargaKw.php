<?php

namespace App\Entity;

use App\Validator\CierreKw;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Validator\CierreKw as CierreKwConstraint;

/**
 * @ORM\Entity
 * @CierreKwConstraint(foreign="reloj",fecha="fecha")
 */
class RecargaKw
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

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
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $codigoSTS;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range(
     *      min = 1,
     *      minMessage = "La asignación debe ser igual o superior a {{ limit }} Kw",
     * )
     */
    private $asignacion;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range(
     *      min = 0,
     *      minMessage = "Los Kw restantes deben ser igual o superior a {{ limit }} Kw",)
     *
     */
    private $folio00;

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

    /**
     * @return \Reloj
     */
    public function getReloj(): ?Reloj
    {
        return $this->reloj;
    }

    /**
     * @param \Reloj $reloj
     */
    public function setReloj(Reloj $reloj): void
    {
        $this->reloj = $reloj;
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

    public function getCodigoSTS(): ?string
    {
        return $this->codigoSTS;
    }

    public function setCodigoSTS(string $codigoSTS): self
    {
        $this->codigoSTS = $codigoSTS;

        return $this;
    }

    public function getAsignacion(): ?float
    {
        return $this->asignacion;
    }

    public function setAsignacion(float $asignacion): self
    {
        $this->asignacion = $asignacion;

        return $this;
    }

    public function getFolio00(): ?float
    {
        return $this->folio00;
    }

    public function setFolio00(?float $folio00): self
    {
        $this->folio00 = $folio00;

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
        $path = $this->getId() == null ? 'reloj' : null;
        if (null == $this->getReloj())
            $context->buildViolation('Seleccione un reloj')
                ->atPath($path)
                ->addViolation();
        if (!$this->getReloj()->getActivo())
            $context->buildViolation('Seleccione un reloj activo')
                ->atPath($path)
                ->addViolation();

        $hoy = new \DateTime('today');

        $anno = $hoy->format('y');
        if ($this->getFecha()->format('y') != $anno)
            $context->buildViolation('Seleccione una fecha dentro del año actual')
                ->atPath('fecha')
                ->addViolation();
        $mes = $hoy->format('m');
        if ($this->getFecha()->format('m') != $mes)
            $context->buildViolation('Seleccione una fecha dentro del mes actual')
                ->atPath('fecha')
                ->addViolation();

        if (null == $this->getUsuario())
            $context->buildViolation('Seleccione un usuario')->addViolation();
    }


}
