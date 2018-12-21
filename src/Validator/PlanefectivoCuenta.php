<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PlanefectivoCuenta extends Constraint
{
    public $message = 'Para el centro de costo %centrocosto% ya existe un plan para %subelemento% en ese mes';
    public $service = 'planefectivocuenta.validator';
    public $em = null;
    public $repositoryMethod = 'findBy';
    public $mes;
    public $anno;
    public $errorPath = 'subelemento';
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
