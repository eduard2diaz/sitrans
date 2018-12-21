<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Period extends Constraint
{
    public $message = 'El periodo %from% - %to% está comprometido';
    public $service = 'entity.validator.period';
    public $em = null;
    public $repositoryMethod = 'findBy';
    public $from;
    public $foreign;
    public $to;
    public $errorPath = 'from';
    public $ignoreNull = true;

    public function getRequiredOptions()
    {
        return ['from','to','foreign'];
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
        return 'from';
    }

}
