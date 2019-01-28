<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @UniqueEntity(fields={"nombre","institucion"})
 * @UniqueEntity(fields={"codigo","institucion"})
 */
class Cuenta
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $codigo;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *      min = 0,
     *      max = 1,
     * )
     */
    private $naturaleza;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $nae;

    /**
     * @var \Institucion
     *
     * @ORM\ManyToOne(targetEntity="Institucion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="institucion", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $institucion;

    public function getId()
    {
        return $this->id;
    }

    public function getCodigo(): ?int
    {
        return $this->codigo;
    }

    public function setCodigo(int $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getNaturaleza(): ?int
    {
        return $this->naturaleza;
    }

    public function getNaturalezaToString(){
        return $this->naturaleza==0 ? 'Deudora' : 'Acreedora';
    }

    public function setNaturaleza(int $naturaleza): self
    {
        $this->naturaleza = $naturaleza;

        return $this;
    }

    public function getNae(): ?string
    {
        return $this->nae;
    }

    public function setNae(string $nae): self
    {
        $this->nae = $nae;

        return $this;
    }

    public function getInstitucion(): ?Institucion
    {
        return $this->institucion;
    }

    public function setInstitucion(?Institucion $institucion): self
    {
        $this->institucion = $institucion;

        return $this;
    }

    public function __toString()
    {
     return $this->getNombre();
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (null==$this->getInstitucion())
            $context->buildViolation('Seleccione una institución')
                ->atPath('institucion')
                ->addViolation();
    }
}
