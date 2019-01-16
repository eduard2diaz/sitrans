<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Centrocosto extends Constraint
{
    public $message = 'Ya existe un centro de costo con nombre %nombre%';
    public $service = 'centrocosto.validator';
    public $em = null;
    public $repositoryMethod = 'findBy';
    public $nombre;
    public $codigo;
    public $errorPath = 'nombre';
    public $ignoreNull = true;

    public function getRequiredOptions()
    {
        return ['nombre','codigo'];
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
        return 'nombre';
    }

}
