<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Validator\PlanportadoresArea as PlanportadoresAreaConstraint;

/**
 * @ORM\Entity
 * @PlanportadoresAreaConstraint()
 */
class PlanportadoresArea
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Area", inversedBy="planportadores")
     */
    private $areas;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range(
     *      min = 1,
     *      minMessage = "Debe  el valor debe ser al menos {{ limit }}",
     * )
     */
    private $valor;

    /**
     * @ORM\Column(type="integer")
     */
    private $categoria;

    /**
     * @var \Planportadores
     *
     * @ORM\ManyToOne(targetEntity="Planportadores")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="planportadores", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $planportadores;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    public function __construct()
    {
        $this->areas = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Collection|Area[]
     */
    public function getAreas(): Collection
    {
        return $this->areas;
    }

    public function addArea(Area $area): self
    {
        if (!$this->areas->contains($area)) {
            $this->areas[] = $area;
        }

        return $this;
    }

    public function removeArea(Area $area): self
    {
        if ($this->areas->contains($area)) {
            $this->areas->removeElement($area);
        }

        return $this;
    }

    public function getValor(): ?float
    {
        return $this->valor;
    }

    public function setValor(float $valor): self
    {
        $this->valor = $valor;

        return $this;
    }

    public function getCategoria(): ?string
    {
        return $this->categoria;
    }

    public function getCategoriaToString(): ?string
    {
        $array=['Combustible','Electricidad'];
        return $array[$this->categoria];
    }


    public function setCategoria(string $categoria): self
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * @return Planportadores
     */
    public function getPlanportadores(): Planportadores
    {
        return $this->planportadores;
    }

    /**
     * @param Planportadores $planportadores
     */
    public function setPlanportadores(Planportadores $planportadores): void
    {
        $this->planportadores = $planportadores;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if($this->getCategoria()!=0 && $this->getCategoria()!=1)
            $context->buildViolation('Seleccione una categoría correcta')
                ->atPath('categoria')
                ->addViolation();

        if($this->getAreas()->isEmpty())
            $context->buildViolation('Seleccione un área')
                ->atPath('areas')
                ->addViolation();
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }
}
