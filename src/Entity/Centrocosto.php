<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
/**
 * Centrocosto
 *
 * @ORM\Table(name="centrocosto")
 * @ORM\Entity
 * @UniqueEntity(fields={"nombre"})
 * @UniqueEntity(fields={"codigo"})
 */
class Centrocosto
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="centrocosto_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nombre", type="string", nullable=false, unique=true)
     */
    private $nombre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codigo", type="string", nullable=false, unique=true)
     */
    private $codigo;

    /**
     * @var \Cuenta
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Cuenta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cuenta", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $cuenta;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\PlanefectivoCuenta", mappedBy="centrocosto")
     */
    private $planefectivos;

    public function __construct()
    {
        $this->planefectivos = new ArrayCollection();
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
     * @return null|string
     */
    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    /**
     * @param null|string $codigo
     */
    public function setCodigo(?string $codigo): void
    {
        $this->codigo = $codigo;
    }

    public function __toString()
    {
     return $this->getNombre();
    }

    public function getCuenta(): ?Cuenta
    {
        return $this->cuenta;
    }

    public function setCuenta(?Cuenta $cuenta): self
    {
        $this->cuenta = $cuenta;

        return $this;
    }

    /**
     * @return Collection|PlanefectivoCuenta[]
     */
    public function getPlanefectivos(): Collection
    {
        return $this->planefectivos;
    }

    public function addPlanefectivo(PlanefectivoCuenta $planefectivo): self
    {
        if (!$this->planefectivos->contains($planefectivo)) {
            $this->planefectivos[] = $planefectivo;
            $planefectivo->addCentrocosto($this);
        }

        return $this;
    }

    public function removePlanefectivo(PlanefectivoCuenta $planefectivo): self
    {
        if ($this->planefectivos->contains($planefectivo)) {
            $this->planefectivos->removeElement($planefectivo);
            $planefectivo->removeCentrocosto($this);
        }

        return $this;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if(null==$this->getCuenta())
            $context->buildViolation('Seleccione una cuenta')
                ->atPath('cuenta')
                ->addViolation();
    }
}
