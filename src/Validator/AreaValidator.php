<?php

namespace App\Validator;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
/*
 * Validador que comprueba que no existan 2 o mas areas en una misma institucion las cuales posean el mismo nombre o
 * codigo
 */
class AreaValidator extends ConstraintValidator
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {

        /* @var $constraint App\Validator\Area */
        $pa = PropertyAccess::createPropertyAccessor();

        if (!$constraint instanceof Area) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\Area');
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

        $ccosto = $pa->getValue($value, $constraint->ccosto)->getId();
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
                'ccosto' => $ccosto,
            ];

            if (!$id) {
                $cadena = "SELECT COUNT(a) FROM App:Area a JOIN a.ccosto c WHERE c.id= :ccosto AND a." . $value['campo'] . "= :valor";
            } else {
                $cadena = "SELECT COUNT(a) FROM App:Area a JOIN a.ccosto c WHERE c.id= :ccosto AND a." . $value['campo'] . "= :valor AND a.id!= :id";
                $parameters['id'] = $id;
            }

            $consulta = $em->createQuery($cadena);
            $consulta->setParameters($parameters);
            $result = $consulta->getResult();
            if ($result[0][1] > 0) {
                $this->context->buildViolation("Ya existe un área con " . ($value['campo']=='codigo' ? 'código' : $value['campo']). " %nombre%")
                    ->setParameter('%nombre%', $value['valor'])
                    ->atPath($value['campo'])
                    ->addViolation();
            }

        }


    }
}
