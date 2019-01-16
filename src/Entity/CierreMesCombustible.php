<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @UniqueEntity(fields={"mes","anno","institucion"})
 */
class CierreMesCombustible
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
     *      min = 1,
     *      max = 12,
     * )
     */
    private $mes;

    /**
     * @ORM\Column(type="integer")
     */
    private $anno;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Institucion")
     * @ORM\JoinColumn(nullable=false)
     */
    private $institucion;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * @param mixed $mes
     */
    public function setMes($mes): void
    {
        $this->mes = $mes;
    }

    /**
     * @return mixed
     */
    public function getAnno()
    {
        return $this->anno;
    }

    /**
     * @param mixed $anno
     */
    public function setAnno($anno): void
    {
        $this->anno = $anno;
    }

    public function getMesToString()
    {
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        return $meses[$this->getMes() - 1];
    }

    /**
     * @return mixed
     */
    public function getInstitucion()
    {
        return $this->institucion;
    }

    /**
     * @param mixed $institucion
     */
    public function setInstitucion($institucion): void
    {
        $this->institucion = $institucion;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        $current_year = date('Y');
        $next_year = date('Y');
        if (date('m') == 12)
            $next_year = $current_year + 1;
        if ($this->getAnno() < $current_year || $this->getAnno() > $next_year)
            $context->buildViolation('Seleccione un año válido')
                ->atPath('anno')
                ->addViolation();

        if (null == $this->getInstitucion())
            $context->buildViolation('Seleccione una institución')
                ->atPath('institucion')
                ->addViolation();
    }
}
