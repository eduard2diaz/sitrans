<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Validator\Responsable as ResponsableConstraint;
/**
 * Responsable
 *
 * @ORM\Table(name="responsable", indexes={ @ORM\Index(name="IDX_52520D07D7943D68", columns={"area"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"ci"})
 * @ResponsableConstraint()
 */
class Responsable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="responsable_id_seq", allocationSize=1, initialValue=1)
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
     * @ORM\Column(name="apellidos", type="string", nullable=true)
     * @Assert\Regex("/^[A-Za-záéíóúñ]{2,}([\s][A-Za-záéíóúñ]{2,})*$/")
     */
    private $apellidos;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ci", type="string", nullable=true)
     * @Assert\Regex(
     *     pattern="/[\d]{11}/",
     *     message="El carnet de identidad debe poseer 11 dígitos"
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
     * @var \Area
     *
     * @ORM\ManyToOne(targetEntity="Area")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="area", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $area;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="activo", type="boolean", nullable=true)
     */
    private $activo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Tarjeta", mappedBy="responsable")
     */
    private $tarjetas;

    public function __construct()
    {
        $this->tarjetas = new ArrayCollection();
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

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(?string $apellidos): self
    {
        $this->apellidos = $apellidos;

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

    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function setArea(?Area $area): self
    {
        $this->area = $area;

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

    /**
     * @return Collection|Tarjeta[]
     */
    public function getTarjetas(): Collection
    {
        return $this->tarjetas;
    }

    public function addTarjeta(Tarjeta $tarjeta): self
    {

        if (!$this->tarjetas->contains($tarjeta)) {
            $this->tarjetas[] = $tarjeta;
            $tarjeta->setResponsable($this);
        }

        return $this;
    }

    public function removeTarjeta(Tarjeta $tarjeta): self
    {
        if ($this->tarjetas->contains($tarjeta)) {
            $this->tarjetas->removeElement($tarjeta);
            // set the owning side to null (unless already changed)
            if ($tarjeta->getResponsable() === $this) {
                $tarjeta->setResponsable(null);
            }
        }

        return $this;
    }

        public function __toString(){
         return $this->getNombre().' '.$this->getApellidos();
        }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if(null==$this->getArea())
            $context->buildViolation('Seleccione el área')
                ->atPath('area')
                ->addViolation();
        foreach ($this->getTarjetas() as $value)
            if(!$value->getActivo()){
                $context->buildViolation('Solo puede ser responsable de tarjetas activas')
                    ->atPath('tarjetas')
                    ->addViolation();
                break;
            }else
                if((null!=$value->getResponsable()) && ($this->getId()!=$value->getResponsable()->getId())){
                    $context->buildViolation('Solo puede ser responsable que no posean responsables')
                        ->atPath('tarjetas')
                        ->addViolation();
                    break;
                }
    }


}
