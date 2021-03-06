<?php

namespace App\Form;

use App\Entity\Partida;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class PartidaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $institucion=$options['institucion'];

        $builder
            ->add('nombre',TextType::class,array('attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba el nombre','class'=>'form-control input-medium')))
            ->add('codigo',IntegerType::class,array('label'=>'Código','attr'=>array('placeholder'=>'Escriba el código','class'=>'form-control input-medium')))
            ->add('cuenta',EntityType::class,array(
                'auto_initialize'=>false,
                'class'         =>'App:Cuenta',
                'query_builder'=>function(EntityRepository $repository) use($institucion){
                    $qb=$repository->createQueryBuilder('c');
                    $qb->join('c.institucion','i');
                    $qb->where('i.id = :id')->setParameter('id',$institucion);
                    return $qb;
                }
            ))
            ->add('tipopartida',null,array('label'=>'Tipo de partida','required'=>true))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Partida::class,
        ]);
        $resolver->setRequired('institucion');
    }
}
