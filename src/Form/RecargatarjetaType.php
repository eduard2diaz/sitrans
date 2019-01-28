<?php

namespace App\Form;

use App\Entity\Recargatarjeta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\Transformer\DateTimetoStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RecargatarjetaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tarjeta=$options['data']->getTarjeta();
        $institucion=$options['institucion'];

        $builder
            ->add('fecha', TextType::class, array('attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))
            ->add('cantidadefectivo', NumberType::class, array('label'=>'Cantidad de efectivo','attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))
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
            'data_class' => Recargatarjeta::class,
        ]);
        $resolver->setRequired('institucion');
    }
}
