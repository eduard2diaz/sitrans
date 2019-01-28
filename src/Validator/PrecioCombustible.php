<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PrecioCombustible extends Constraint
{
    public $message = 'Para ese tipo de combustible ya existe una recarga de tarjeta con fecha igual o superior a %fecha%';
    public $service = 'preciocombustible.validator';
    public $em = null;
    public $repositoryMethod = 'findBy';
    public $fecha;
    public $tipocombustible;
    public $errorPath = 'tipocombustible';
    public $ignoreNull = true;

    public function getRequiredOptions()
    {
        return ['fecha','tipocombustible'];
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
        return 'fecha';
    }

}
