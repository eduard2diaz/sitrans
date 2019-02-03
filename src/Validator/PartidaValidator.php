<?php

namespace App\Validator;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PartidaValidator extends ConstraintValidator
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {

        /* @var $constraint App\Validator\Partida */
        $pa = PropertyAccess::createPropertyAccessor();

        if (!$constraint instanceof Partida) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\Partida');
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

        /*
         *Este validador de manera general se encarga de comprobar que no existan 2 partidas con el mismo nombre o
         * código en la misma institución, además que exista una y solo una partida para cada tipo de partida por
         * institución
         */
        $institucion = $pa->getValue($value, $constraint->cuenta)->getInstitucion()->getId();
        $nombre = $pa->getValue($value, $constraint->nombre);
        $codigo = $pa->getValue($value, $constraint->codigo);
        $tipopartida = $pa->getValue($value, $constraint->tipopartida);
        $id = $pa->getValue($value, 'id');
        $entity=$repository->getClassName();
        $array=[
            ['campo'=>$constraint->nombre,'valor'=>$nombre],
            ['campo'=>$constraint->codigo,'valor'=>$codigo],
            ['campo'=>$constraint->tipopartida,'valor'=>$tipopartida],
        ];

        foreach($array as $value){
            $parameters = [
                'valor' => $value['valor'],
                'institucion' => $institucion,
            ];

            if (!$id) {
                $cadena = "SELECT COUNT(p) FROM App:Partida p JOIN p.cuenta c JOIN c.institucion i WHERE i.id= :institucion AND p.".$value['campo']. "= :valor";
            } else {
                $cadena = "SELECT COUNT(p) FROM App:Partida p JOIN p.cuenta c JOIN c.institucion i WHERE i.id= :institucion AND p.".$value['campo']. "= :valor AND p.id!= :id";
                $parameters['id'] = $id;
            }

            $consulta = $em->createQuery($cadena);
            $consulta->setParameters($parameters);
            $result = $consulta->getResult();
            if ($result[0][1] > 0) {
                $translate=$value['campo'];
                if($value['campo']=='codigo')
                    $translate='código';
                elseif ($value['campo']=='tipopartida')
                    $translate='el tipo de partida ';
                $this->context->buildViolation("Ya existe una partida con ".$translate. " %nombre%")
                    ->setTranslationDomain('messages')
                    ->setParameter('%nombre%',  $value['valor'])
                    ->atPath($value['campo'])
                    ->addViolation();
                break;
            }
        }
    }
}
