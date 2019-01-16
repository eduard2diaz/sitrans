<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Validator\Area as AreaConstraint;

/**
 * Area
 *
 * @ORM\Table(name="area", indexes={@ORM\Index(name="IDX_D7943D68429C20AD", columns={"ccosto"})})
 * @ORM\Entity
 * @AreaConstraint(nombre="nombre",codigo="codigo",ccosto="ccosto")
 */
class Area
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"comment"=""})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="area_id_seq", allocationSize=1, initialValue=1)
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
     * @ORM\Column(name="codigo", type="string", nullable=false)
     */
    private $codigo;

    /**
     * @var \Centrocosto
     *
     * @ORM\ManyToOne(targetEntity="Centrocosto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ccosto", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $ccosto;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\PlanportadoresArea", mappedBy="areas")
     */
    private $planportadores;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $direccionparticular;


    public function __construct()
    {
        $this->planportadores = new ArrayCollection();
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

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(?string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getCcosto(): ?Centrocosto
    {
        return $this->ccosto;
    }

    public function setCcosto(?Centrocosto $ccosto): self
    {
        $this->ccosto = $ccosto;

        return $this;
    }

    /**
     * @return Collection|PlanportadoresArea[]
     */
    public function getPlanportadores(): Collection
    {
        return $this->planportadores;
    }

    public function addPlanportadore(PlanportadoresArea $planportadore): self
    {
        if (!$this->planportadores->contains($planportadore)) {
            $this->planportadores[] = $planportadore;
            $planportadore->addArea($this);
        }

        return $this;
    }

    public function removePlanportadore(PlanportadoresArea $planportadore): self
    {
        if ($this->planportadores->contains($planportadore)) {
            $this->planportadores->removeElement($planportadore);
            $planportadore->removeArea($this);
        }

        return $this;
    }

    public function getDireccionparticular(): ?string
    {
        return $this->direccionparticular;
    }

    public function setDireccionparticular(string $direccionparticular): self
    {
        $this->direccionparticular = $direccionparticular;

        return $this;
    }

    public function __toString(){
        return $this->getNombre();
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if(null==$this->getCcosto())
            $context->buildViolation('Seleccione un centro de costo')
                ->atPath('ccosto')
                ->addViolation();
    }

}
