<?php

namespace App\Validator;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PeriodValidator extends ConstraintValidator
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {

        /* @var $constraint App\Validator\Period */
        $pa = PropertyAccess::createPropertyAccessor();

        if (!$constraint instanceof Period) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\Period');
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

        if (!is_string($constraint->from)) {
            throw new UnexpectedTypeException($constraint->from, 'string');
        } else
            if (!$class->hasField($constraint->from) && !$class->hasAssociation($constraint->from))
                throw new ConstraintDefinitionException(sprintf('The field "%s" is not mapped by Doctrine, so it cannot be validated for uniqueness.', $constraint->from));


        $fechaInicio = $pa->getValue($value, $constraint->from);


        if (!is_string($constraint->to)) {
            throw new UnexpectedTypeException($constraint->to, 'string');
        } else
            if (!$class->hasField($constraint->to) && !$class->hasAssociation($constraint->to))
                throw new ConstraintDefinitionException(sprintf('The field "%s" is not mapped by Doctrine, so it cannot be validated for uniqueness.', $constraint->to));


        $fechaFin = $pa->getValue($value, $constraint->to);
        $foreign = $pa->getValue($value, $constraint->foreign);

        $id = $pa->getValue($value, 'id');

        $parameters = [
            'fechainicio' => $fechaInicio,
            'fechafin' => $fechaFin,
            $constraint->foreign => $foreign,
        ];

        $entity=$repository->getClassName();
        if (!$id) {
            $cadena = "SELECT COUNT(r) FROM ".$entity." r JOIN r.".$constraint->foreign." f WHERE f.id= :".$constraint->foreign." AND ((:fechainicio <= r.".$constraint->from." AND :fechafin>=r.".$constraint->from.") OR (:fechainicio >= r.".$constraint->from." AND :fechafin<=r.".$constraint->to.") OR( :fechainicio<=r.".$constraint->to." AND :fechafin>=r.".$constraint->to."))";
        } else {
            $cadena = "SELECT COUNT(r) FROM ".$entity." r JOIN r.".$constraint->foreign." f WHERE f.id= :".$constraint->foreign." AND r.id!= :id  AND((:fechainicio <= r.".$constraint->from." AND :fechafin>=r.".$constraint->from.") OR (:fechainicio >= r.".$constraint->from." AND :fechafin<=r.".$constraint->to.") OR( :fechainicio<=r.".$constraint->to." AND :fechafin>=r.".$constraint->to."))";
            $parameters['id'] = $id;
        }

        $consulta = $em->createQuery($cadena);
        $consulta->setParameters($parameters);
        $result = $consulta->getResult();
        if ($result[0][1] > 0) {
            $this->context->buildViolation($constraint->message)
                ->setTranslationDomain('messages')
                ->setParameter('%from%', $fechaInicio->format('d-m-Y'))
                ->setParameter('%to%',  $fechaFin->format('d-m-Y'))
                ->atPath($constraint->from)
                ->addViolation();

        }
        //REALIZO LA COMPRACION CONTRA OTRAS ENTIDADES
        else{

            $entities=[
                    ['nombre'=>'App\Entity\Hojaruta','foreign'=>'vehiculo','from'=>'fechasalida','to'=>'fechallegada', 'error'=>'Para el período indicado el vehículo tiene una hoja de ruta'],
                    ['nombre'=>'App\Entity\Mantenimiento','foreign'=>'vehiculo','from'=>'fechainicio','to'=>'fechafin', 'error'=>'Para el período indicado el vehículo tiene un mantenimiento'],
                    ['nombre'=>'App\Entity\Reparacion','foreign'=>'vehiculo','from'=>'fechainicio','to'=>'fechafin', 'error'=>'Para el período indicado el vehículo tiene una reparación'],
                    ['nombre'=>'App\Entity\Pruebalitro','foreign'=>'vehiculo','from'=>'fechainicio','to'=>'fechafin', 'error'=>'Para el período indicado el vehículo tiene una prueba de litro'],
                    ['nombre'=>'App\Entity\Somaton','foreign'=>'vehiculo','from'=>'fechainicio','to'=>'fechafin', 'error'=>'Para el período indicado el vehículo tiene un somatón'],
                ];
            foreach ($entities as $entidad){
                if($entity==$entidad['nombre'])
                    continue;

                $cadena = "SELECT COUNT(r) FROM ".$entidad['nombre']." r JOIN r.".$entidad['foreign']." f WHERE f.id= :".$entidad['foreign']." AND ((:fechainicio <= r.".$entidad['from']." AND :fechafin>=r.".$entidad['from'].") OR (:fechainicio >= r.".$entidad['from']." AND :fechafin<=r.".$entidad['to'].") OR( :fechainicio<=r.".$entidad['to']." AND :fechafin>=r.".$entidad['to']."))";
                $consulta = $em->createQuery($cadena);
                $parameters = [
                    'fechainicio' => $fechaInicio,
                    'fechafin' => $fechaFin,
                    $entidad['foreign'] => $foreign,
                ];
                $consulta->setParameters($parameters);
                $result = $consulta->getResult();
                if ($result[0][1] > 0) {
                    $this->context->buildViolation($entidad['error'])
                        ->setParameter('%from%', $fechaInicio->format('d-m-Y'))
                        ->setParameter('%to%', $fechaFin->format('d-m-Y'))
                        ->atPath($constraint->from)
                        ->addViolation();
                    break;
                }

            }
        }
    //FIN DE LA COMPROBACION CONTRA OTRAS ENTIDADDES

    }


}
