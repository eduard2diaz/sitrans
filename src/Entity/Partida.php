<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Validator\Partida as PartidaConstraint;

/**
 * @ORM\Entity
 * @PartidaConstraint(nombre="nombre",codigo="codigo",tipopartida="tipopartida",cuenta="cuenta")
 */
class Partida
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *      min = 0,
     *      minMessage = "El código de la partida debe ser igual o superior a {{ limit }}",
     * )
     */
    private $codigo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @var \Cuenta
     *
     * @ORM\ManyToOne(targetEntity="Cuenta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cuenta", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $cuenta;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tipopartida")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tipopartida;

    public function getId()
    {
        return $this->id;
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

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
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

    public function getTipopartida(): ?Tipopartida
    {
        return $this->tipopartida;
    }

    public function setTipopartida(?Tipopartida $tipopartida): self
    {
        $this->tipopartida = $tipopartida;

        return $this;
    }

    public function __toString(){
        return "{$this->getCodigo()}-{$this->getNombre()}";
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
        if(null==$this->getTipopartida())
            $context->buildViolation('Seleccione un tipo de partida')
                ->atPath('tipopartida')
                ->addViolation();
    }
}
