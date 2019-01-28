<?php

namespace App\Form;

use App\Entity\Subelemento;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\Subscriber\AddSubElementoElementoFieldSubscriber;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class SubelementoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $institucion=$options['institucion'];

        $builder
            ->add('nombre',TextType::class,array('attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba el nombre','class'=>'form-control input-medium')))
            ->add('codigo',IntegerType::class,array('label'=>'Código','attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba el código','class'=>'form-control input-medium')))
            ->add('partida',EntityType::class,array(
                'placeholder'=>'Seleccione una partida',
                'auto_initialize'=>false,
                'required'=>true,
                'class'         =>'App:Partida',
                'query_builder'=>function(EntityRepository $repository) use($institucion){
                    $qb=$repository->createQueryBuilder('p');
                    $qb->join('p.cuenta','c');
                    $qb->join('c.institucion','i');
                    $qb->where('i.id = :id')->setParameter('id',$institucion);
                    return $qb;
                },
            ))
        ;

        $factory=$builder->getFormFactory();
        $builder->addEventSubscriber(new AddSubElementoElementoFieldSubscriber ($factory));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Subelemento::class,
        ]);
        $resolver->setRequired('institucion');
    }
}
