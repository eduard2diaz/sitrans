<?php

namespace App\Validator;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/*
 *Se encarga de comprobar se cumplan las condiciones necesarias para que un vehiculo sea valido
 */
class VehiculoValidator extends ConstraintValidator
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint App\Validator\Vehiculo */
        $pa = PropertyAccess::createPropertyAccessor();

        if (!$constraint instanceof Vehiculo) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\Vehiculo');
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

        $responsable = $pa->getValue($value, 'responsable');
        $estado = $pa->getValue($value, 'estado');
        $id = $pa->getValue($value, 'id');
        $grupo= [0,1,2];
        if(!$id){
            $consulta=$em->createQuery('SELECT COUNT(v.id) as contador FROM App:Vehiculo v join v.responsable r WHERE r.id= :responsable and v.estado IN (:grupo)');
            $consulta->setParameters(['responsable'=>$responsable,'grupo'=>$grupo]);
        }else{
            $consulta=$em->createQuery('SELECT COUNT(v.id) as contador FROM App:Vehiculo v join v.responsable r WHERE r.id= :responsable and v.estado IN (:grupo) and v.id<> :id');
            $consulta->setParameters(['responsable'=>$responsable,'grupo'=>$grupo,'id'=>$id]);
        }

        $total=$consulta->getResult()[0]['contador'];
        if( (in_array($estado,$grupo)) && $total>0)
            $this->context->buildViolation($constraint->message)
                ->atPath('responsable')
                ->addViolation();


    }


}
