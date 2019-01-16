<?php

namespace App\Form;

use App\Entity\PlanefectivoCuenta;
use App\Form\Subscriber\AddSubElementoFieldSubscriber;
use App\Form\Subscriber\AddCentrocostoFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class PlanefectivoCuentaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $institucion=$options['institucion'];
        $builder
            ->add('valor',NumberType::class,array(
                'attr'=>array('autocomplete'=>'off','class'=>'form-control')
            ))
            ->add('cuenta',EntityType::class,array(
                'placeholder'=>'Seleccione una cuenta',
                'auto_initialize'=>false,
                'required'=>true,
                'class'         =>'App:Cuenta',
                'query_builder'=>function(EntityRepository $repository) use($institucion){
                    $qb=$repository->createQueryBuilder('c');
                    $qb->join('c.institucion','i');
                    $qb->where('i.id = :id')->setParameter('id',$institucion);
                    return $qb;
                },
            ))
            ->add('subelemento',null,['required'=>true])
            ->add('centrocosto',null,['label'=>'Centro de costo','required'=>true])
        ;

        $factory=$builder->getFormFactory();
        $builder->addEventSubscriber(new AddSubElementoFieldSubscriber($factory));
        $builder->addEventSubscriber(new AddCentrocostoFieldSubscriber($factory));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlanefectivoCuenta::class,
        ]);
        $resolver->setRequired('institucion');
    }
}
