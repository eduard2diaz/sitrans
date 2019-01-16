<?php

namespace App\Validator;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class TarjetaValidator extends ConstraintValidator
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {

        /* @var $constraint App\Validator\Tarjeta */
        $pa = PropertyAccess::createPropertyAccessor();

        if (!$constraint instanceof Tarjeta) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\Tarjeta');
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

        $institucion = $pa->getValue($value, $constraint->tipotarjeta)->getInstitucion()->getId();
        $codigo = $pa->getValue($value, $constraint->codigo);
        $id = $pa->getValue($value, 'id');
        $entity=$repository->getClassName();

            $parameters = [
                'codigo' => $codigo,
                'institucion' => $institucion,
            ];

            if (!$id) {
                $cadena = "SELECT COUNT(t) FROM App:Tarjeta t JOIN t.tipotarjeta tt join tt.institucion i WHERE i.id= :institucion AND t.codigo= :codigo";
            } else {
                $cadena = "SELECT COUNT(t) FROM App:Tarjeta t JOIN t.tipotarjeta tt join tt.institucion i WHERE i.id= :institucion AND t.codigo= :codigo AND t.id!= :id";
                $parameters['id'] = $id;
            }

            $consulta = $em->createQuery($cadena);
            $consulta->setParameters($parameters);
            $result = $consulta->getResult();
            if ($result[0][1] > 0) {
                $this->context->buildViolation('Ya existe una tarjeta con código %codigo%')
                    ->setParameter('%codigo%',  $codigo)
                    ->atPath('codigo')
                    ->addViolation();

        }
    }
}
