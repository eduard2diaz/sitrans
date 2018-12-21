<?php

namespace App\Validator;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class TarifaKwValidator extends ConstraintValidator
{
    private $registry;

    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint App\Validator\TarifaKw */
        $pa = PropertyAccess::createPropertyAccessor();

        if (!$constraint instanceof TarifaKw) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\TarifaKw');
        }




        $total=$value->getRangoTarifaKws()->count();
        if($total==0)
            $this->context->buildViolation("Defina los rangos")
                ->atPath('fecha')
                ->addViolation();
        else {
            $array = $value->getRangoTarifaKws()->toArray();
            for ($i = 0; $i < $total - 1; $i++)
                for ($j = $i + 1; $j < $total; $j++)
                    if ($array[$i]->getInicio() > $array[$j]->getInicio()) {
                        $aux = $array[$i];
                        $array[$i] = $array[$j];
                        $array[$j] = $aux;
                    }

            $continue = 0;
            $pos = 0;
            foreach ($array as $value) {
                if ($value->getInicio() != $continue) {
                    $this->context->buildViolation("Uno de los rangos debe iniciar en $continue")
                        ->atPath('fecha')
                        ->addViolation();
                    break;
                }
                elseif (($pos != $total - 1) && ($value->getFin() == null)) {
                        $this->context->buildViolation("Indique el valor final de este rango que comienza en ".$value->getInicio())
                            ->atPath('fecha')
                            ->addViolation();
                        break;
                }
                elseif (($pos == $total - 1) && ($value->getFin() != null)) {

                    $this->context->buildViolation("El último rango no debe tener un valor final")
                        ->atPath('fecha')
                        ->addViolation();
                    break;
                }
                $continue=$value->getFin()+1;
                $pos++;
            }

        }
    }


}
