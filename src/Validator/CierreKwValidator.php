<?php

namespace App\Validator;

use App\Entity\Reloj;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CierreKwValidator extends ConstraintValidator
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

        if(!$foreign instanceof Reloj)
            throw new \Exception('Llave foranea incorrecta');

        $area=  $foreign->getArea()->getId();
        $cierre = $this->registry->existeCierreKilowatts($anno,$mes,$area);

        if(null!=$cierre)
            $this->context->buildViolation($constraint->message)
                ->setParameter('%reloj%', $foreign->getCodigo())
                ->atPath('reloj')
                ->addViolation();
    }


}
