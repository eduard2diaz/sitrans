<?php

namespace App\Validator;

use App\Entity\Reloj;
use App\Entity\Tarjeta;
use App\Entity\Vehiculo;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CierreCombustibleValidator extends ConstraintValidator
{
    private $registry;

    public function __construct($registry)
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
        $mes = $fecha->format('m');
        $anno = $fecha->format('Y');

        if($foreign instanceof Vehiculo)
            $foreign=$foreign->getResponsable()->getTarjetas()->first();
        elseif (!$foreign instanceof  Tarjeta)
            throw new \Exception('Llave foranea incorrecta');

        $tarjeta=  $foreign->getId();
        $cierre = $this->registry->existeCierreCombustible($anno,$mes,$tarjeta);

        if(null!=$cierre)
            $this->context->buildViolation($constraint->message)
                ->setParameter('%tarjeta%', $foreign->getCodigo())
                ->atPath('tarjeta')
                ->addViolation();
    }


}
