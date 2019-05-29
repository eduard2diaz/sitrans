<?php

namespace App\Validator;

use App\Entity\Tarjeta;
use App\Tools\TarjetaService;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/*
 *Este validator permite garantizar que no se pueda realizar un gestionar con una determinada entidad si la ya ese mes
 * cerro.
 */
class EsUltimaOperacionTarjetaValidator extends ConstraintValidator
{
    private $registry;

    public function __construct(TarjetaService $registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {
        /**
         * @var $constraint App\Validator\CierreKw
         */
        $pa = PropertyAccess::createPropertyAccessor();

        $foreign = $pa->getValue($value, $constraint->foreign);
        $fecha = $pa->getValue($value, $constraint->fecha);
        $id = $pa->getValue($value, 'id');
        if ($foreign instanceof  \App\Entity\Vehiculo)
            $foreign=$foreign->getResponsable()->getTarjetas()->first();
        elseif (!$foreign instanceof  Tarjeta)
            throw new \Exception('Llave foranea incorrecta');

            if(null!=$this->registry->ultimaOperacionTarjeta($foreign->getId(),$fecha)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('%tarjeta%', $foreign->getCodigo())
                    ->atPath('tarjeta')
                    ->addViolation();
        }


    }


}
