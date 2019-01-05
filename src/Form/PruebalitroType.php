<?php

namespace App\Form;

use App\Entity\Pruebalitro;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\Transformer\DateTimetoStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class PruebalitroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data=$options['data'];
        $vehiculo=$options['data']->getVehiculo();
        $disabled=false;
        if($data->getId()!=null)
            $disabled=true;

        $builder
            ->add('fecha', TextType::class, array('label'=>'Fecha',
                'attr' => array(
                'autocomplete' => 'off',
                'disabled'=>$disabled,
                'class' => 'form-control input-medium'
            )))
            ->add('kmsrecorrido', NumberType::class, array(
                'disabled'=>$disabled,
                'label'=>'Kilómetros recorridos'
            ,'attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium',
                'min'=>0.5
            )))

            ->add('vehiculo',EntityType::class,array(
                'label'=>'Vehículo',
                'auto_initialize'=>false,
                'class'         =>'App:Vehiculo',
                'disabled'=>$disabled,
                'query_builder'=>function(EntityRepository $repository) use($vehiculo){
                    $qb=$repository->createQueryBuilder('v');
                    $qb->join('v.responsable','r');
                    $qb->join('r.tarjetas','t');
                    $qb->where('v.estado= 0 AND t.activo = :activo AND r.activo = :activo')->setParameters(['activo'=>true]);
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
            'data_class' => Pruebalitro::class,
        ]);
    }
}
