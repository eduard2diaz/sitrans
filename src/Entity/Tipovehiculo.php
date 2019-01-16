<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Tipovehiculo
 *
 * @ORM\Table(name="tipovehiculo")
 * @ORM\Entity
 * @UniqueEntity(fields={"nombre"})
 */
class Tipovehiculo
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tipovehiculo_id_seq", allocationSize=1, initialValue=1)
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
     * @ORM\ManyToMany(targetEntity="Licencia", inversedBy="idtipovehiculo")
     * @ORM\JoinTable(name="tipovehiculo_licencia",
     *   joinColumns={
     *     @ORM\JoinColumn(name="idtipovehiculo", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="idlicencia", referencedColumnName="id")
     *   }
     * )
     */
    private $idlicencia;

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

    public function __toString()
    {
     return $this->getNombre();
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ( $this->getIdlicencia()->isEmpty())
            $context->buildViolation('Seleccione las licencias necesarias para conducir este vehículo')
                ->atPath('idlicencia')
                ->addViolation();
    }

}
