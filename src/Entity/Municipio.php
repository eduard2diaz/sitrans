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
 * @UniqueEntity(fields={"nombre","provincia"})
 */
class Municipio
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex("/^[A-Za-záéíóúñ]{2,}([\s][A-Za-záéíóúñ]{2,})*$/")
     */
    private $nombre;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Provincia", inversedBy="municipios")
     */
    private $provincia;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Institucion", mappedBy="municipio")
     */
    private $institucions;

    public function __construct()
    {
        $this->institucions = new ArrayCollection();
    }

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

    public function getProvincia(): ?Provincia
    {
        return $this->provincia;
    }

    public function setProvincia(?Provincia $provincia): self
    {
        $this->provincia = $provincia;

        return $this;
    }

    /**
     * @return Collection|Institucion[]
     */
    public function getInstitucions(): Collection
    {
        return $this->institucions;
    }

    public function addInstitucion(Institucion $institucion): self
    {
        if (!$this->institucions->contains($institucion)) {
            $this->institucions[] = $institucion;
            $institucion->setMunicipio($this);
        }

        return $this;
    }

    public function removeInstitucion(Institucion $institucion): self
    {
        if ($this->institucions->contains($institucion)) {
            $this->institucions->removeElement($institucion);
            // set the owning side to null (unless already changed)
            if ($institucion->getMunicipio() === $this) {
                $institucion->setMunicipio(null);
            }
        }

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
    }
}
