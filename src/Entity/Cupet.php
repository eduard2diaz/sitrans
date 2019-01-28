<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Cupet
 *
 * @ORM\Table(name="cupet")
 * @ORM\Entity
 * @UniqueEntity(fields={"nombre"})
 */
class Cupet
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="cupet_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nombre", type="string", nullable=false)
     */
    private $nombre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="direccion", type="string", nullable=false)
     */
    private $direccion;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="enfuncionamiento", type="boolean", nullable=true)
     */
    private $enfuncionamiento;

    /**
     * @var \Provincia
     *
     * @ORM\ManyToOne(targetEntity="Provincia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="provincia", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $provincia;

    /**
     * @var \Institucion
     *
     * @ORM\ManyToOne(targetEntity="Municipio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="municipio", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $municipio;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(?string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getEnfuncionamiento(): ?bool
    {
        return $this->enfuncionamiento;
    }

    public function setEnfuncionamiento(?bool $enfuncionamiento): self
    {
        $this->enfuncionamiento = $enfuncionamiento;

        return $this;
    }

    public function getProvincia(): ?Provincia
    {
        return $this->provincia;
    }

    public function setProvincia(?Provincia $provincia): self
    {
        $this->provincia = $provincia;

        return $this;
    }

    public function getMunicipio(): ?Municipio
    {
        return $this->municipio;
    }

    public function setMunicipio(?Municipio $municipio): self
    {
        $this->municipio = $municipio;

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
        if (null==$this->getProvincia())
            $context->buildViolation('Seleccione una provincia')
                ->atPath('provincia')
                ->addViolation();
        elseif (null==$this->getMunicipio())
            $context->buildViolation('Seleccione  un municipio')
                ->atPath('municipio')
                ->addViolation();
        elseif ($this->getMunicipio()->getProvincia()->getId()!=$this->getProvincia()->getId())
            $context->buildViolation('Seleccione  un municipio que pertenezca a la provincia seleccionada')
                ->atPath('municipio')
                ->addViolation();

    }
}
