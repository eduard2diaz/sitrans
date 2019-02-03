<?php

namespace App\Validator;

use App\Entity\Tarjeta;
use function PHPSTORM_META\type;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ImporteValidator extends ConstraintValidator
{
    private $registry;

    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint App\Validator\Importe */
        $pa = PropertyAccess::createPropertyAccessor();

        if (!$constraint instanceof Importe) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\Importe');
        }

        $fecha = $pa->getValue($value, $constraint->fecha);
        $importe = $pa->getValue($value, $constraint->importe);
        $litros = $pa->getValue($value, $constraint->litros);
        $foreign = $pa->getValue($value, $constraint->foreign);

        if($foreign instanceof Tarjeta)
            $tipocombustible=$foreign->getTipocombustible()->getId();
        elseif ($foreign instanceof \App\Entity\Vehiculo)
            $tipocombustible=$foreign->getResponsable()->getTarjetas()->first()->getTipocombustible()->getId();
        else
            throw new \Exception('Llave foranea incorrecta');

        $importeenBd=$this->registry->importeCombustible($tipocombustible,$fecha);
        if(!$importeenBd)
            throw new \Exception('No existe la tarifa en la base de datos');

        $cantidad=$importe/$importeenBd[0]['importe'];
        if(strval($cantidad)!=strval($litros))
            $this->context->buildViolation($constraint->message)
                ->setParameter('%litro%', $cantidad)
                ->atPath($constraint->litros)
                ->addViolation();
    }


}
