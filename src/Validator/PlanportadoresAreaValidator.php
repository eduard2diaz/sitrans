<?php

namespace App\Validator;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\Common\Persistence\ManagerRegistry;

class PlanportadoresAreaValidator extends ConstraintValidator
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {

        /* @var $constraint App\Validator\Planportadores */
        $pa = PropertyAccess::createPropertyAccessor();

        if (!$constraint instanceof PlanportadoresArea) {
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
        $nuevasAreas = $pa->getValue($value, 'areas');
        $categoria = $pa->getValue($value, 'categoria');
        $planportadores = $pa->getValue($value, 'planportadores');

        foreach ($nuevasAreas as $area) {
            $consulta = $em->createQuery('Select COUNT(ppa.id) as contador from App:PlanportadoresArea ppa JOIN ppa.planportadores pp JOIN ppa.areas a
                                    WHERE ppa.id<> :id AND pp.id= :plan AND :area MEMBER OF ppa.areas AND ppa.categoria= :categoria');
            $consulta->setParameters(['id' => $id, 'plan' => $planportadores->getId(), 'area' => $area,'categoria'=>$categoria]);
            $planes = $consulta->getResult();
            if ($planes[0]['contador'] > 0)
                $this->context->buildViolation($constraint->message)
                    ->setParameter('%area%', $area->getNombre())
                    ->atPath('areas')
                    ->addViolation();
        }





    }


}
