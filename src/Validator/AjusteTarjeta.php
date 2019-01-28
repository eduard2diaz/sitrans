<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AjusteTarjeta extends Constraint
{
    public $message = 'Ya existe un área con nombre %nombre%';
    public $service = 'ajustetarjeta.validator';
    public $em = null;
    public $repositoryMethod = 'findBy';
    public $foreign;
    public $errorPath = 'tarjeta';
    public $ignoreNull = true;

    public function getRequiredOptions()
    {
        return ['foreign'];
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
        return 'foreign';
    }

}
