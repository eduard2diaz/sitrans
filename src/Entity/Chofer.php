<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Chofer
 *
 * @ORM\Table(name="chofer")
 * @ORM\Entity
 * @UniqueEntity(fields={"ci"})
 */
class Chofer
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="chofer_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nombre", type="string", nullable=true)
     * @Assert\Regex("/^[A-Za-záéíóúñ]{2,}([\s][A-Za-záéíóúñ]{2,})*$/")
     */
    private $nombre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="apellido", type="string", nullable=true)
     * @Assert\Regex("/^[A-Za-záéíóúñ]{2,}([\s][A-Za-záéíóúñ]{2,})*$/")
     */
    private $apellido;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ci", type="string", nullable=true)
     * @Assert\Regex(
     *     pattern="/[\d]{11}/",
     *     message="El carnet de identidad solamente debe poseer números"
     * )
     */
    private $ci;

    /**
     * @var string|null
     *
     * @ORM\Column(name="direccion", type="string", nullable=true)
     */
    private $direccion;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Licencia", inversedBy="idchofer")
     * @ORM\JoinTable(name="chofer_licencia",
     *   joinColumns={
     *     @ORM\JoinColumn(name="idchofer", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="idlicencia", referencedColumnName="id")
     *   }
     * )
     */
    private $idlicencia;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="activo", type="boolean", nullable=true)
     */
    private $activo;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idlicencia = new \Doctrine\Common\Collections\ArrayCollection();
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

    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    public function setApellido(?string $apellido): self
    {
        $this->apellido = $apellido;

        return $this;
    }

    public function getCi(): ?string
    {
        return $this->ci;
    }

    public function setCi(?string $ci): self
    {
        $this->ci = $ci;

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

    /**
     * @return Collection|Licencia[]
     */
    public function getIdlicencia(): Collection
    {
        return $this->idlicencia;
    }

    public function addIdlicencium(Licencia $idlicencium): self
    {
        if (!$this->idlicencia->contains($idlicencium)) {
            $this->idlicencia[] = $idlicencium;
        }

        return $this;
    }

    public function removeIdlicencium(Licencia $idlicencium): self
    {
        if ($this->idlicencia->contains($idlicencium)) {
            $this->idlicencia->removeElement($idlicencium);
        }

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getActivo(): ?bool
    {
        return $this->activo;
    }

    /**
     * @param bool|null $activo
     */
    public function setActivo(?bool $activo): void
    {
        $this->activo = $activo;
    }

    public function __toString(){
        return $this->getNombre().' '.$this->getApellido();
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if($this->getIdlicencia()->isEmpty())
            $context->buildViolation('Seleccione al menos un tipo de licencia')
                ->atPath('idlicencia')
                ->addViolation();
    }

}
