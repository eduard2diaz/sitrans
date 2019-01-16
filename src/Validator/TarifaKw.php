<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/*
 * Validador que es empleado por la entidad TarifaKw para comprobar si un tarifa de kilowatts determinada es correcta,
 * la validación es llevada a cabo en la clase TarifaKwValidator
 */
class TarifaKw extends Constraint
{
    public $message = 'El importe debe ser %importe%';
    public $service = 'tarifakw.validator';
    public $em = null;
    public $repositoryMethod = 'findBy';
    public $errorPath = 'importe';
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
