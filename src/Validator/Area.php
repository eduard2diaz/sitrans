<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Area extends Constraint
{
    public $message = 'Ya existe un área con nombre %nombre%';
    public $service = 'area.validator';
    public $em = null;
    public $repositoryMethod = 'findBy';
    public $nombre;
    public $codigo;
    public $ccosto;
    public $errorPath = 'nombre';
    public $ignoreNull = true;

    public function getRequiredOptions()
    {
        return ['nombre','codigo','ccosto'];
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
