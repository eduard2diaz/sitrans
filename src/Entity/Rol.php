<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;

/**
 * Rol
 *
 * @ORM\Table(name="rol")
 * @ORM\Entity
 */
class Rol extends Role
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="rol_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nombre", type="string", nullable=true)
     */
    private $nombre;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Usuario", mappedBy="idrol")
     */
    private $idusuario;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idusuario = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Collection|Usuario[]
     */
    public function getIdusuario(): Collection
    {
        return $this->idusuario;
    }

    public function addIdusuario(Usuario $idusuario): self
    {
        if (!$this->idusuario->contains($idusuario)) {
            $this->idusuario[] = $idusuario;
            $idusuario->addIdrol($this);
        }

        return $this;
    }

    public function removeIdusuario(Usuario $idusuario): self
    {
        if ($this->idusuario->contains($idusuario)) {
            $this->idusuario->removeElement($idusuario);
            $idusuario->removeIdrol($this);
        }

        return $this;
    }

    public function __toString()
    {
        $value=$this->getNombre();
        if($value=='ROLE_CAJERO')
            $value='Cajero/a';
        if($value=='ROLE_JEFETRANSPORTE')
            $value='Jefe/a de Transporte';
        if($value=='ROLE_ADMIN')
            $value='Administrador/a';
        if($value=='ROLE_SUPER')
            $value='Super administrador/a';
     return $value;
    }

}
