<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\TarifaKw as TarifaKwConstraint;

/**
 * @ORM\Entity
 * @TarifaKwConstraint()
 */
class TarifaKw
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RangoTarifaKw", mappedBy="tarifas", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    private $rangoTarifaKws;

    public function __construct()
    {
        $this->rangoTarifaKws = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * @return Collection|RangoTarifaKw[]
     */
    public function getRangoTarifaKws(): Collection
    {
        return $this->rangoTarifaKws;
    }

    public function addRangoTarifaKw(RangoTarifaKw $rangoTarifaKw): self
    {
        if (!$this->rangoTarifaKws->contains($rangoTarifaKw)) {
            $this->rangoTarifaKws[] = $rangoTarifaKw;
            $rangoTarifaKw->setTarifas($this);
        }

        return $this;
    }

    public function removeRangoTarifaKw(RangoTarifaKw $rangoTarifaKw): self
    {
        if ($this->rangoTarifaKws->contains($rangoTarifaKw)) {
            $this->rangoTarifaKws->removeElement($rangoTarifaKw);
            // set the owning side to null (unless already changed)
            if ($rangoTarifaKw->getTarifas() === $this) {
                $rangoTarifaKw->setTarifas(null);
            }
        }

        return $this;
    }

    public function setRangoTarifaKw( $rangoTarifaKws)
    {
        $this->rangoTarifaKws = $rangoTarifaKws;
        foreach ($rangoTarifaKws as $address) {
            $address->setTarifas($this);
        }
    }

    public function __toString()
    {
     return $this->getFecha()->format('d-m-Y');
    }


}
