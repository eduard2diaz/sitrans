<?php

namespace App\Validator;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Debug\Debug;

class PlanefectivoCuentaValidator extends ConstraintValidator
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {

        /* @var $constraint App\Validator\PlanefectivoCuenta */
        $pa = PropertyAccess::createPropertyAccessor();

        if (!$constraint instanceof PlanefectivoCuenta) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\UniqueEntity');
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

        $id = $pa->getValue($value, 'id') ?? -1;
        $centrocostos = $pa->getValue($value, 'centrocosto');
        $subelementos = $pa->getValue($value, 'subelemento');
        $cuenta = $pa->getValue($value, 'cuenta');
        $planefectivo = $pa->getValue($value, 'planefectivo');

        foreach ($centrocostos as $centrocosto)
            foreach ($subelementos as $subelemento){

                $consulta=$em->createQuery('Select COUNT(pec.id) as contador from App:PlanefectivoCuenta pec JOIN pec.planefectivo pe JOIN pec.cuenta c
                                    JOIN pec.subelemento s JOIN pec.centrocosto cc WHERE pec.id<> :id AND c.id= :cuenta AND pe.id= :plan
                                    AND :subelemento MEMBER OF pec.subelemento  AND :centrocosto MEMBER OF pec.centrocosto
                                    ');
                $consulta->setParameters(['id'=>$id, 'cuenta'=>$cuenta->getId(),'plan'=>$planefectivo->getId(),
                     'subelemento'=>$subelemento,'centrocosto'=>$centrocosto

                ]);
                $planes=$consulta->getResult();
                if ($planes[0]['contador']>0)
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('%subelemento%', $subelemento->getNombre())
                        ->setParameter('%centrocosto%', $centrocosto->getNombre())
                        ->atPath('subelemento')
                        ->addViolation();
            }
    }


}
