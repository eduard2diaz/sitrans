<?php

namespace App\Validator;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/*
 * Clase que se encarga realmente de definir las condiciones necesarias estructural y semánticamente para la
 * validación de una determinada Tarifa de Kilowatts
 */
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

        if ($constraint->em) {
            $em = $this->registry->getManager($constraint->em);
            if (!$em) {
                throw new ConstraintDefinitionException(sprintf('Object manager "%s" does not exist.', $constraint->em));
            }
        } else {
            $em = $this->registry->getManagerForClass(get_class($value));

            if (!$em) {
                throw new ConstraintDefinitionException(sprintf('Unable to find the object manager associated with an entity of class "%s".', get_class($value)));
            }
        }

        $fecha = $pa->getValue($value, 'fecha');
        $cadena = "SELECT COUNT(r) FROM App:RecargaKw r WHERE r.fecha>= :fecha";
        $consulta = $em->createQuery($cadena);
        $consulta->setParameter('fecha', $fecha);
        $result = $consulta->getResult();
        if ($result[0][1] > 0)
            $this->context->buildViolation("Ya existe una recarga de kilowatts con fecha superior a %fecha%")
                ->setParameter('%fecha%', $fecha->format('d-m-Y'))
                ->atPath('fecha')
                ->addViolation();


        $total=$value->getRangoTarifaKws()->count();
        if($total==0)
            $this->context->buildViolation("Defina los rangos")
                ->atPath('fecha')
                ->addViolation();
        else {
            $array = $value->getRangoTarifaKws()->toArray();
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
