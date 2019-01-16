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
 * @UniqueEntity(fields={"nombre","provincia","municipio"})
 */
class Institucion
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="boolean")
     */
    private $activo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Municipio", inversedBy="institucions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $municipio;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Provincia", inversedBy="institucions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $provincia;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Institucion")
     */
    private $institucionpadre;


    public function getId()
    {
        return $this->id;
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

    public function getActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): self
    {
        $this->activo = $activo;

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

    public function getProvincia(): ?Provincia
    {
        return $this->provincia;
    }

    public function setProvincia(?Provincia $provincia): self
    {
        $this->provincia = $provincia;

        return $this;
    }

    public function getInstitucionpadre(): ?self
    {
        return $this->institucionpadre;
    }

    public function setInstitucionpadre(?self $institucionpadre): self
    {
        $this->institucionpadre = $institucionpadre;

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
        if (null == $this->getProvincia())
            $context->buildViolation('Seleccione una provincia')
                ->atPath('provincia')
                ->addViolation();
        if (null == $this->getMunicipio())
            $context->buildViolation('Seleccione un municipio')
                ->atPath('municipio')
                ->addViolation();
        if (null != $this->getInstitucionpadre())
            if (!$this->getInstitucionpadre()->getActivo())
                $context->buildViolation('Seleccione una institución activa')
                    ->atPath('institucionpadre')
                    ->addViolation();
            elseif ($this->getInstitucionpadre()->getId() == $this->getId())
                $context->buildViolation('Una institución no puede ser padre de si misma')
                    ->atPath('institucionpadre')
                    ->addViolation();
            else {
                $hijo = $this->cicloInfinito($this->getId(), $this->getInstitucionpadre());
                if (null != $hijo)
                    $context->buildViolation('Referencia circular: Esta institución es padre de ' . $hijo)
                        ->atPath('institucionpadre')
                        ->addViolation();
            }


    }

    private function cicloInfinito($current, Institucion $padre)
    {
        if ($padre->getInstitucionpadre() != null) {
            if ($padre->getInstitucionpadre()->getId() == $current)
                return $padre->getNombre();
            else
                return $this->cicloInfinito($current, $padre->getInstitucionpadre());
        }
        return null;
    }
}
