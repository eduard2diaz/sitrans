<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PlanportadoresArea extends Constraint
{
    public $message = 'Ya existe un plan para %area% en ese mes';
    public $service = 'planportadoresarea.validator';
    public $em = null;
    public $repositoryMethod = 'findBy';
    public $mes;
    public $anno;
    public $errorPath = 'areas';
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
        return 'mes';
    }

}
