<?php

namespace App\Validator;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AjusteTarjetaValidator extends ConstraintValidator
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {

        /* @var $constraint App\Validator\AjusteTarjeta */
        $pa = PropertyAccess::createPropertyAccessor();

        if (!$constraint instanceof AjusteTarjeta) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\AjusteTarjeta');
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
        /*
         *Este validador funciona similar a CentrocostoValidator con la diferencia que obtenemos la institución a traves
         * del atributo ccosto(centrocosto) que posse la entidad área y luego de tener el centro de costo obtenermos la
         * institucion a través de un atributo cuenta que posee la clase centro de costo y nuevamente a partir de dicho
         * atributo obtenemos la institución.
         */
        $class = $em->getClassMetadata(get_class($value));
        $repository = $em->getRepository(get_class($value));

        $tarjeta = $pa->getValue($value, $constraint->foreign)->getId();
        $id = $pa->getValue($value, 'id');
        $entity = $repository->getClassName();


        $this->context->buildViolation("Ya existe un área con " . $value['campo'] . " %nombre%")
            ->setParameter('%nombre%', $value['valor'])
            ->atPath($value['campo'])
            ->addViolation();


    }
}
