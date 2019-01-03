<?php

namespace App\Form;

use App\Entity\Hojaruta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\Transformer\DateTimetoStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class HojarutaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data=$options['data'];
        $vehiculo=$options['data']->getVehiculo();
        $fsalida=$options['data']->getFechaSalida();
        $fllegada=$options['data']->getFechaLlegada();
        $disabled=false;
        if($data->getId()!=null)
            $disabled=true;

        $builder
            ->add('codigo', TextType::class, array('label'=>'Código','attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))
            ->add('fechasalida', TextType::class, array('label'=>'Fecha de salida',
                'empty_data'=>$fsalida!=null ? $fsalida->format('d-m-Y') : null,
                'attr' => array(
                'autocomplete' => 'off',
                'disabled'=>$disabled,
                'class' => 'form-control input-medium'
            )))
            ->add('fechallegada', TextType::class, array('label'=>'Fecha de llegada',
                'empty_data'=>$fllegada!=null ? $fllegada->format('d-m-Y') : null,
                'attr' => array(
                'autocomplete' => 'off',
                'disabled'=>$disabled,
                'class' => 'form-control input-medium'
            )))
            ->add('origen', TextType::class, array('attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))
            ->add('destino', TextType::class, array('attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))
            ->add('kmrecorrido', IntegerType::class, array(
                'disabled'=>$disabled,
                'label'=>'Kilómetros recorridos'
            ,'attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))

            ->add('litrosconsumidos', IntegerType::class, array(
                'disabled'=>$disabled,
                'label'=>'Litros consumidos'
            ,'attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))

            ->add('importe', NumberType::class, array(
                'disabled'=>$disabled
            ,'attr' => array(
                'autocomplete' => 'off',
                'readonly' => true,
                'class' => 'form-control input-medium'
            )))

            ->add('descripcion', TextareaType::class, array('label'=>'Descripción','attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))
            ->add('tipoactividad',null,array('label'=>'Tipo de actividad', 'required'=>true))
            ->add('vehiculo',EntityType::class,array(
                'label'=>'Vehículo',
                'auto_initialize'=>false,
                'class'         =>'App:Vehiculo',
                'disabled'=>$disabled,
                'query_builder'=>function(EntityRepository $repository) use($vehiculo){
                    $qb=$repository->createQueryBuilder('v');
                    $qb->join('v.responsable','r');
                    $qb->join('r.tarjetas','t');
                    $qb->where('v.estado= 0 AND t.activo = :activo AND r.activo = :activo AND (v.litrosentanque  > :cantidad)')->setParameters(['activo'=>true,'cantidad'=>0]);
                    if(null!=$vehiculo)
                        $qb->orWhere('v.id= :id')->setParameter('id',$vehiculo);
                    return $qb;
                }
            ))
        ;

        $builder->get('fechasalida')
            ->addModelTransformer(new DateTimetoStringTransformer());
        $builder->get('fechallegada')
            ->addModelTransformer(new DateTimetoStringTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Hojaruta::class,
        ]);
    }
}
