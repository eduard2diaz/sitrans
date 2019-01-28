<?php

namespace App\Form;

use App\Entity\AjusteTarjeta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\Transformer\DateTimetoStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Form\template\TarjetaType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AjusteTarjetaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isdisabled=$options['data']->getId() ? true : false;
        $tarjeta=$options['data']->getTarjeta();
        $institucion=$options['institucion'];

        $builder
            ->add('fecha', TextType::class, array('attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))
            ->add('cantefectivo', NumberType::class, array('label'=>'Cantidad de efectivo','attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))
            ->add('tipo',ChoiceType::class,array(
                'disabled'=>$isdisabled,
                'choices'=>array(
                    'Debe'=>1,'Haber'=>0
                ),'attr'=>array('class'=>'form-control input-medium')    ))

            ->add('tarjeta',EntityType::class,array(
                'required'=>true,
                'auto_initialize'=>false,
                'class'         =>'App:Tarjeta',
                'query_builder'=>function(EntityRepository $repository) use($tarjeta,$institucion){
                    $qb=$repository->createQueryBuilder('t');
                    $qb->join('t.responsable','r');
                    $qb->join('t.tipotarjeta','tt');
                    $qb->join('tt.institucion','i');
                    $qb->where('t.activo = :activo AND r.activo = :activo AND i.id= :id')->setParameters(['activo'=>true,'id'=>$institucion]);
                    if(null!=$tarjeta)
                        $qb->orWhere('t.id = :tarjeta')->setParameter('tarjeta',$tarjeta);
                    return $qb;
                }
            ))
        ;

        $builder->get('fecha')
            ->addModelTransformer(new DateTimetoStringTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AjusteTarjeta::class,
        ]);
        $resolver->setRequired(['institucion']);
    }
}
