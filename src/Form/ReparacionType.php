<?php

namespace App\Form;

use App\Entity\Reparacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\Transformer\DateTimetoStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class ReparacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data=$options['data'];
        $institucion=$options['institucion'];
        $vehiculo=$options['data']->getVehiculo();
        $disabled=false;
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
                'query_builder'=>function(EntityRepository $repository) use($vehiculo,$institucion){
                    $qb=$repository->createQueryBuilder('v');
                    $qb->join('v.responsable','r');
                    $qb->join('r.tarjetas','t');
                    $qb->join('v.institucion','i');
                    $qb->where('v.estado= 1 AND t.activo = TRUE AND r.activo = TRUE AND i= :institucion')->setParameter('institucion', $institucion);
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
            'data_class' => Reparacion::class,
        ]);
        $resolver->setRequired('institucion');
    }
}
