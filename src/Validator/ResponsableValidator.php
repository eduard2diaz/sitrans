<?php

namespace App\Validator;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Debug\Debug;

class ResponsableValidator extends ConstraintValidator
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {

        /* @var $constraint App\Validator\Responsable */
        $pa = PropertyAccess::createPropertyAccessor();

        if (!$constraint instanceof Responsable) {
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

        $id = $pa->getValue($value, 'id');
        if(null!=$id){
        $responsable=$em->getRepository('App:Responsable')->find($id);
        if(count($responsable->getTarjetas()->toArray())>1){
            $vehiculo=$em->getRepository('App:Vehiculo')->findByResponsable($responsable);
            if(null!=$vehiculo)
                $this->context->buildViolation($constraint->message)
                    ->atPath('tarjetas')
                    ->addViolation();
        }
    }

    }
}
