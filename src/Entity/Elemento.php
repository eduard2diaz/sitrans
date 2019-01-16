<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @UniqueEntity(fields={"codigo","partida"})
 * @UniqueEntity(fields={"nombre","partida"})
 */
class Elemento
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
     *      minMessage = "El código del elemento debe ser igual o superior a {{ limit }}",
     * )
     */
    private $codigo;

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

    public function getPartida(): ?Partida
    {
        return $this->partida;
    }

    public function setPartida(?Partida $partida): self
    {
        $this->partida = $partida;

        return $this;
    }

    public function __toString()
    {
        return "{$this->getCodigo()}-{$this->getNombre()}";
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
            if((substr($this->getCodigo(),0,strlen($this->getPartida()->getCodigo()))!=$this->getPartida()->getCodigo() ) || ($this->getCodigo()==$this->getPartida()->getCodigo()))
                $context->buildViolation('Compruebe el código')
                    ->atPath('codigo')
                    ->addViolation();
    }
}
