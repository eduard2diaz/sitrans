<?php

namespace App\Form;

use App\Entity\Mantenimiento;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\Transformer\DateTimetoStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class MantenimientoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data=$options['data'];
        $disabled=false;
        $vehiculo=$data->getVehiculo();
        if($data->getId()!=null)
            $disabled=true;

        $builder
            ->add('fechainicio', TextType::class, array('label'=>'Fecha de inicio','attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))
            ->add('fechafin', TextType::class, array('label'=>'Fecha de fin','attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))

            ->add('descripcion', TextareaType::class, array('label'=>'Descripción','attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
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
                    $qb->where('v.estado= 1 AND t.activo = :activo AND r.activo = :activo')->setParameter('activo', true);
                    if(null!=$vehiculo)
                        $qb->orWhere('v.id= :vehiculo')->setParameter('vehiculo',$vehiculo);

                    return $qb;
                }
            ))
        ;

        $builder->get('fechainicio')
            ->addModelTransformer(new DateTimetoStringTransformer());
        $builder->get('fechafin')
            ->addModelTransformer(new DateTimetoStringTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Mantenimiento::class,
        ]);
    }
}
