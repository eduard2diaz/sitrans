<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Licencia
 *
 * @ORM\Table(name="licencia")
 * @ORM\Entity
 * @UniqueEntity(fields={"nombre"})
 */
class Licencia
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="licencia_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nombre", type="string", nullable=false)
     */
    private $nombre;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Tipovehiculo", mappedBy="idlicencia")
     */
    private $idtipovehiculo;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Chofer", mappedBy="idlicencia")
     */
    private $idchofer;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idtipovehiculo = new \Doctrine\Common\Collections\ArrayCollection();
        $this->idchofer = new \Doctrine\Common\Collections\ArrayCollection();
    }

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

    /**
     * @return Collection|Tipovehiculo[]
     */
    public function getIdtipovehiculo(): Collection
    {
        return $this->idtipovehiculo;
    }

    public function addIdtipovehiculo(Tipovehiculo $idtipovehiculo): self
    {
        if (!$this->idtipovehiculo->contains($idtipovehiculo)) {
            $this->idtipovehiculo[] = $idtipovehiculo;
            $idtipovehiculo->addIdlicencium($this);
        }

        return $this;
    }

    public function removeIdtipovehiculo(Tipovehiculo $idtipovehiculo): self
    {
        if ($this->idtipovehiculo->contains($idtipovehiculo)) {
            $this->idtipovehiculo->removeElement($idtipovehiculo);
            $idtipovehiculo->removeIdlicencium($this);
        }

        return $this;
    }

    /**
     * @return Collection|Chofer[]
     */
    public function getIdchofer(): Collection
    {
        return $this->idchofer;
    }

    public function addIdchofer(Chofer $idchofer): self
    {
        if (!$this->idchofer->contains($idchofer)) {
            $this->idchofer[] = $idchofer;
            $idchofer->addIdlicencium($this);
        }

        return $this;
    }

    public function removeIdchofer(Chofer $idchofer): self
    {
        if ($this->idchofer->contains($idchofer)) {
            $this->idchofer->removeElement($idchofer);
            $idchofer->removeIdlicencium($this);
        }

        return $this;
    }

    public function __toString()
    {
     return $this->getNombre();
    }

}
