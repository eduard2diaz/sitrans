<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity)
 * @UniqueEntity(fields={"nombre"})
 */
class Provincia
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
     * @ORM\OneToMany(targetEntity="App\Entity\Municipio", mappedBy="provincia")
     */
    private $municipios;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Institucion", mappedBy="provincia")
     */
    private $institucions;

    public function __construct()
    {
        $this->municipios = new ArrayCollection();
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

    /**
     * @return Collection|Municipio[]
     */
    public function getMunicipios(): Collection
    {
        return $this->municipios;
    }

    public function addMunicipio(Municipio $municipio): self
    {
        if (!$this->municipios->contains($municipio)) {
            $this->municipios[] = $municipio;
            $municipio->setProvincia($this);
        }

        return $this;
    }

    public function removeMunicipio(Municipio $municipio): self
    {
        if ($this->municipios->contains($municipio)) {
            $this->municipios->removeElement($municipio);
            // set the owning side to null (unless already changed)
            if ($municipio->getProvincia() === $this) {
                $municipio->setProvincia(null);
            }
        }

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
            $institucion->setProvincia($this);
        }

        return $this;
    }

    public function removeInstitucion(Institucion $institucion): self
    {
        if ($this->institucions->contains($institucion)) {
            $this->institucions->removeElement($institucion);
            // set the owning side to null (unless already changed)
            if ($institucion->getProvincia() === $this) {
                $institucion->setProvincia(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getNombre();
    }
}
