<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Importe extends Constraint
{
    public $message = 'El importe debe ser %importe%';
    public $service = 'importe.validator';
    public $em = null;
    public $repositoryMethod = 'findBy';
    public $litros;
    public $fecha;
    public $importe;
    public $foreign;
    public $errorPath = 'importe';
    public $ignoreNull = true;

    public function getRequiredOptions()
    {
        return ['litros','fecha','importe','foreign'];
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
        return 'litros';
    }

}
