<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Tarjeta extends Constraint
{
    public $message = 'Ya existe una tarjeta con código %codigo%';
    public $service = 'tarjeta.validator';
    public $em = null;
    public $repositoryMethod = 'findBy';
    public $codigo;
    public $tipotarjeta;
    public $errorPath = 'codigo';
    public $ignoreNull = true;

    public function getRequiredOptions()
    {
        return ['codigo','tipotarjeta'];
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
        return 'codigo';
    }

}
