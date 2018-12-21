<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Responsable extends Constraint
{
    public $message = 'Usted no puede ser responsable de un vehiculo si tiene 2 o mas tarjetas';
    public $service = 'responsable.validator';
    public $em = null;
    public $repositoryMethod = 'findBy';
    public $errorPath = 'tarjetas';
    public $ignoreNull = true;

    public function getRequiredOptions()
    {
        return [];
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
        return '';
    }

}
