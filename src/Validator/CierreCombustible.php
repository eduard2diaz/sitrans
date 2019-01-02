<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CierreCombustible extends Constraint
{
    public $message = 'Ya la tarjeta %tarjeta% tiene cierre en este mes';
    public $service = 'cierrecombustible.validator';
    public $repositoryMethod = 'findBy';
    public $fecha;
    public $foreign;
    public $errorPath = 'tarjeta';
    public $ignoreNull = true;

    public function getRequiredOptions()
    {
        return ['fecha','foreign'];
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
