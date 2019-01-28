<?php

namespace App\Validator;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/*
 *Este validador verifica que se pueda registrar, modificar o eliminar un precio de combustible para un
 * determinado tipo de combustible si y solo aún no se ha hecho ninguna recarga de combustible de ese tipo
 * tarjeta.
 */

class PrecioCombustibleValidator extends ConstraintValidator
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {

        /* @var $constraint App\Validator\PrecioCombustible */
        $pa = PropertyAccess::createPropertyAccessor();

        if (!$constraint instanceof PrecioCombustible) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\PrecioCombustible');
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

        $class = $em->getClassMetadata(get_class($value));
        $repository = $em->getRepository(get_class($value));

        $tipocombustible = $pa->getValue($value, $constraint->tipocombustible)->getId();
        $fecha = $pa->getValue($value, $constraint->fecha);
        $entity = $repository->getClassName();
        $cadena = "SELECT COUNT(r) FROM App:Recargatarjeta r JOIN r.tarjeta t JOIN t.tipocombustible tt WHERE tt.id= :tipocombustible AND r.fecha>= :fecha";
        $consulta = $em->createQuery($cadena);
        $consulta->setParameters(['tipocombustible' => $tipocombustible, 'fecha' => $fecha]);
        $result = $consulta->getResult();
        if ($result[0][1] > 0)
            $this->context->buildViolation("Para ese tipo de combustible ya existe una recarga de tarjeta con fecha superior a %fecha%")
                ->setParameter('%fecha%', $fecha->format('d-m-Y'))
                ->atPath('tipocombustible')
                ->addViolation();

    }
}
