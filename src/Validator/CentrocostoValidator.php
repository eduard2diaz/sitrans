<?php

namespace App\Validator;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/*
 * En este validator lo que hacemos es comprobar que para una misma institución no existan 2 centros de costos
 * con el mismo nombre o código, no se pudo realizar dicha comprobación utilizando el constraint UniqueEntity
 * pues la clase centro de costo no tiene un atributo institución, sino que se identifica a la institución a
 * través del atributo cuenta que si posee esta clase, y vale destacar que la clase cuenta si posee el atributo
 * institución que requerimos para llevar a cabo la validación
 */

class CentrocostoValidator extends ConstraintValidator
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {

        /* @var $constraint App\Validator\Centrocosto */
        $pa = PropertyAccess::createPropertyAccessor();

        if (!$constraint instanceof Centrocosto) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\Centrocosto');
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

        $institucion = $pa->getValue($value, 'cuenta')->getInstitucion()->getId();
        $nombre = $pa->getValue($value, $constraint->nombre);
        $codigo = $pa->getValue($value, $constraint->codigo);
        $id = $pa->getValue($value, 'id');
        $entity = $repository->getClassName();
        $array = [
            ['campo' => $constraint->nombre, 'valor' => $nombre],
            ['campo' => $constraint->codigo, 'valor' => $codigo],
        ];

        foreach ($array as $value) {
            $parameters = [
                'valor' => $value['valor'],
                'institucion' => $institucion,
            ];

            if (!$id) {
                $cadena = "SELECT COUNT(cc) FROM App:Centrocosto cc JOIN cc.cuenta c join c.institucion i WHERE i.id= :institucion AND cc." . $value['campo'] . "= :valor";
            } else {
                $cadena = "SELECT COUNT(cc) FROM App:Centrocosto cc JOIN cc.cuenta c join c.institucion i WHERE i.id= :institucion AND cc." . $value['campo'] . "= :valor AND cc.id!= :id";
                $parameters['id'] = $id;
            }

            $consulta = $em->createQuery($cadena);
            $consulta->setParameters($parameters);
            $result = $consulta->getResult();
            if ($result[0][1] > 0) {
                $this->context->buildViolation("Ya existe un centro de costo con " . $value['campo'] . " %nombre%")
                    ->setParameter('%nombre%', $value['valor'])
                    ->atPath($value['campo'])
                    ->addViolation();
                break;
            }

        }


    }
}
