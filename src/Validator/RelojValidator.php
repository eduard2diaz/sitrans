<?php

namespace App\Validator;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Debug\Debug;

class RelojValidator extends ConstraintValidator
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {

        /* @var $constraint App\Validator\Reloj */
        $pa = PropertyAccess::createPropertyAccessor();

        if (!$constraint instanceof Reloj) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\Reloj');
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



        $area = $pa->getValue($value, 'area');
        $activo = $pa->getValue($value, 'activo');
        $id = $pa->getValue($value, 'id');



        if(true==$activo){
            $lista=[];
            if(null!=$id)
                $lista[]=$id;

        $consulta=$em->createQuery('SELECT COUNT(r.id) as contador FROM App:Reloj r JOIN r.area a WHERE  a.id= :area AND r.activo= TRUE AND r.id NOT IN (:lista)');
        $consulta->setParameters(['area'=>$area,'lista'=>$lista]);
        $relojes=$consulta->getResult();

            if ($relojes[0]['contador']>0)
                $this->context->buildViolation($constraint->message)
                    ->atPath('activo')
                    //A un campo disabled no se le puede agregar un error
                    ->addViolation();
        }


    }


}
