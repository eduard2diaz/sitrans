<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Vehiculo extends Constraint
{
    public $message = 'Un responsable puede tener un único vehículo activo';
    public $service = 'vehiculo.validator';
    public $em = null;
    public $repositoryMethod = 'findBy';
    public $errorPath = 'responsable';
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
