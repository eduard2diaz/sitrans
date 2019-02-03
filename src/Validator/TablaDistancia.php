<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class TablaDistancia extends Constraint
{
    public $message = 'Ya existe un tabla de distancia con origen %origen% y destino %destino% o viceversa';
    public $service = 'tabladistancia.validator';
    public $em = null;
    public $repositoryMethod = 'findBy';
    public $origen;
    public $destino;
    public $errorPath = 'origen';
    public $ignoreNull = true;

    public function getRequiredOptions()
    {
        return ['origen','destino'];
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
        return 'origen';
    }

}
