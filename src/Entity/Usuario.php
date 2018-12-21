<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Usuario
 *
 * @ORM\Table(name="usuario")
 * @ORM\Entity(repositoryClass="App\Repository\UsuarioRepository")
 * @UniqueEntity(fields={"usuario"})
 * @UniqueEntity(fields={"correo"})
 */
class Usuario implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="usuario_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="usuario", type="string", nullable=true)
     */
    private $usuario;

    /**
     * @var string|null
     *
     * @ORM\Column(name="password", type="string", nullable=true)
     */
    private $password;

    /**
     * @var string|null
     *
     * @ORM\Column(name="salt", type="string", nullable=true)
     */
    private $salt;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="activo", type="boolean", nullable=true)
     */
    private $activo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nombre", type="string", nullable=false)
     * @Assert\Regex("/^[A-Za-záéíóúñ]{2,}([\s][A-Za-záéíóúñ]{2,})*$/")
     */
    private $nombre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="apellidos", type="string", nullable=false)
     * @Assert\Regex("/^[A-Za-záéíóúñ]{2,}([\s][A-Za-záéíóúñ]{2,})*$/")
     */
    private $apellidos;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Rol", inversedBy="idusuario")
     * @ORM\JoinTable(name="usuario_rol",
     *   joinColumns={
     *     @ORM\JoinColumn(name="idusuario", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="idrol", referencedColumnName="id")
     *   }
     * )
     */
    private $idrol;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     strict = true
     * )
     */
    private $correo;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idrol = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): ?string
    {
        return $this->usuario;
    }

    public function setUsuario(?string $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(?string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(?bool $activo): self
    {
        $this->activo = $activo;

        return $this;
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

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(?string $apellidos): self
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    /**
     * @return Collection|Rol[]
     */
    public function getIdrol(): Collection
    {
        return $this->idrol;
    }

    public function addIdrol(Rol $idrol): self
    {
        if (!$this->idrol->contains($idrol)) {
            $this->idrol[] = $idrol;
        }

        return $this;
    }

    public function removeIdrol(Rol $idrol): self
    {
        if ($this->idrol->contains($idrol)) {
            $this->idrol->removeElement($idrol);
        }

        return $this;
    }

    public function eraseCredentials()
    {
    }

    public function getUsername()
    {
        return $this->getUsuario();
    }

    public function getRoles()
    {
     $array=[];
     foreach ($this->getIdrol()->toArray() as $value)
         $array[]=$value->getNombre();

     return $array;
    }

    public function getCorreo(): ?string
    {
        return $this->correo;
    }

    public function setCorreo(string $correo): self
    {
        $this->correo = $correo;

        return $this;
    }

    public function __toString()
    {
     return $this->getNombre().' '.$this->getApellidos();
    }
}
