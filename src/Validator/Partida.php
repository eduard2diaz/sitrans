<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Partida extends Constraint
{
    public $message = 'Ya existe una partida con nombre %nombre%';
    public $service = 'partida.validator';
    public $em = null;
    public $repositoryMethod = 'findBy';
    public $nombre;
    public $codigo;
    public $tipopartida;
    public $cuenta;
    public $errorPath = 'nombre';
    public $ignoreNull = true;

    public function getRequiredOptions()
    {
        return ['nombre','codigo','tipopartida','cuenta'];
    }

    /**
     * The validator must be defined as a service with this name.
     *
     * @return string
     */
    public function validatedBy()
    {
        return $this->service;
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function getDefaultOption()
    {
        return 'nombre';
    }

}
