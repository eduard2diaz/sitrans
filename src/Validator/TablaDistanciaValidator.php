<?php

namespace App\Validator;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/*
 *Este validador funciona similar Unique entity pero a diferencia de este compara sin tener en cuenta el orden de
 * los parametros, de esta forma valida que no existan 2 tablas distancias con un mismo par origen-destino
 * sin importar el orden de estos, es decir, sin importar cual es el origen y cual es el destino, SE UTILIZA
 * SOLO PARA LA VALIDACION DE LAS TABLAS DE DISTANCIAS
 */
class TablaDistanciaValidator extends ConstraintValidator
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {

        /* @var $constraint App\Validator\TablaDistancia */
        $pa = PropertyAccess::createPropertyAccessor();

        if (!$constraint instanceof TablaDistancia) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\TablaDistancia');
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

        $origen = $pa->getValue($value, $constraint->origen);
        $id = $pa->getValue($value, 'id');
        $destino = $pa->getValue($value, $constraint->destino);
        $entity = $repository->getClassName();

        $cadena = "SELECT t FROM App:TablaDistancia t WHERE (t.origen= :origen AND t.destino= :destino) or (t.origen= :destino AND t.destino= :origen)";
        $consulta = $em->createQuery($cadena);
        $consulta->setParameters(['origen' => $origen, 'destino' => $destino]);
        $consulta->setMaxResults(1);
        $result = $consulta->getResult();
        if (!empty($result) && $result[0]->getId() != $id) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%origen%', $origen)
                ->setParameter('%destino%', $destino)
                ->atPath($constraint->errorPath)
                ->addViolation();
        }
    }
}
