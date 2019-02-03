<?php

namespace App\Validator;

use App\Entity\Reloj;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/*
 * Validador creado para obligar al "Responsable de Electricidad" a realizar las recargas, lecturas y cierres de forma
 * cronologica
 */
class EsUltimaOperacionKwAreaValidator extends ConstraintValidator
{
    private $registry;

    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {
        /**
         * @var $constraint App\Validator\EsUltimaOoperacionKwArea
         */
        $pa = PropertyAccess::createPropertyAccessor();

        $foreign = $pa->getValue($value, $constraint->foreign);
        $fecha = $pa->getValue($value, $constraint->fecha);

        if(!$foreign instanceof Reloj)
            throw new \Exception('Llave foranea incorrecta');

        $area=  $foreign->getArea()->getId();
        if(null!= $this->registry->ultimaOperacionKwArea($area,$fecha))
            $this->context->buildViolation($constraint->message)
                ->setParameter('%reloj%', $foreign->getCodigo())
                ->atPath('reloj')
                ->addViolation();
    }


}
