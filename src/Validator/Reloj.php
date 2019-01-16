<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Reloj extends Constraint
{
    public $message = 'Ya existe un reloj activo en dicha área';
    public $service = 'reloj.validator';
    public $em = null;
    public $repositoryMethod = 'findBy';
    public $errorPath = 'area';
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
