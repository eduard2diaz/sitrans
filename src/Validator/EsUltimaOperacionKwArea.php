<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EsUltimaOperacionKwArea extends Constraint
{
    public $message = 'Ya el reloj %reloj% tiene una operación con fecha superior';
    public $service = 'esultimaoperacionkwarea.validator';
    public $repositoryMethod = 'findBy';
    public $fecha;
    public $foreign;
    public $errorPath = 'reloj';
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
