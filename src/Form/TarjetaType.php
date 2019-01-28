<?php

namespace App\Form;

use App\Entity\Tarjeta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class TarjetaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $institucion=$options['institucion'];
        $disabled=$options['data']->getId()==null ? false : true;
        $builder
            ->add('codigo',TextType::class,array('label'=>'Código','attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba el nombre','class'=>'form-control input-medium')))
            ->add('tipocombustible',null,array('label'=>'Tipo de combustible','disabled'=>$disabled,'required'=>true,'attr'=>array('class'=>'form-control input-medium')))
            ->add('activo')
            ->add('tipotarjeta',EntityType::class,array(
                'label'=>'Tipo de tarjeta',
                'auto_initialize'=>false,
                'required'=>true,
                'class'         =>'App:Tipotarjeta',
                'query_builder'=>function(EntityRepository $repository) use($institucion){
                    $qb=$repository->createQueryBuilder('t');
                    $qb->join('t.institucion','i');
                    $qb->where('i.id = :institucion')->setParameter('institucion',$institucion);
                    return $qb;
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tarjeta::class,
        ]);
        $resolver->setRequired(['institucion']);
    }
}
