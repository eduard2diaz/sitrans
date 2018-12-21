<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
/**
 * @ORM\Entity
 */
class Subelemento
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @var \Partida
     *
     * @ORM\ManyToOne(targetEntity="Partida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="partida", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $partida;

    /**
     * @var \Elemento
     *
     * @ORM\ManyToOne(targetEntity="Elemento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="elemento", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $elemento;

    /**
     * @ORM\Column(type="integer")
     */
    private $codigo;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\PlanefectivoCuenta", mappedBy="subelemento")
     */
    private $planefectivos;

    public function __construct()
    {
        $this->planefectivos = new ArrayCollection();
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

    public function getElemento(): ?Elemento
    {
        return $this->elemento;
    }

    public function setElemento(?Elemento $elemento): self
    {
        $this->elemento = $elemento;

        return $this;
    }

    public function getCodigo(): ?int
    {
        return $this->codigo;
    }

    public function setCodigo(int $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPartida()
    {
        return $this->partida;
    }

    /**
     * @param mixed $partida
     */
    public function setPartida($partida): void
    {
        $this->partida = $partida;
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
            $planefectivo->addSubelemento($this);
        }

        return $this;
    }

    public function removePlanefectivo(PlanefectivoCuenta $planefectivo): self
    {
        if ($this->planefectivos->contains($planefectivo)) {
            $this->planefectivos->removeElement($planefectivo);
            $planefectivo->removeSubelemento($this);
        }

        return $this;
    }

    public function __toString(){
        return $this->getNombre().' - '.$this->getCodigo();
    }
    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if(null==$this->getPartida())
            $context->buildViolation('Seleccione una partida')
                ->atPath('partida')
                ->addViolation();
        else
            if(null==$this->getElemento())
                $context->buildViolation('Seleccione un elemento')
                    ->atPath('elemento')
                    ->addViolation();
        else
            if($this->getElemento()->getPartida()->getId()!=$this->getPartida()->getId())
                $context->buildViolation('El elemento seleccionado no pertenece a dicha partida')
                    ->atPath('elemento')
                    ->addViolation();
            else
                if(substr($this->getCodigo(),0,strlen(4))!=substr($this->getElemento()->getCodigo(),0,strlen(4)))
                    $context->buildViolation('Compruebe el código')
                        ->atPath('codigo')
                        ->addViolation();
    }
}
