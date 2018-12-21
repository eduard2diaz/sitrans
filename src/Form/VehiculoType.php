<?php

namespace App\Form;

use App\Entity\Vehiculo;
use App\Form\Subscriber\AddVehiculoChoferFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class VehiculoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $responsable=$options['data']->getResponsable();
        $builder
            ->add('matricula',TextType::class,array('label'=>'Matrícula','attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba la matrícula','class'=>'form-control input-medium')))
            ->add('marca',TextType::class,array('attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba la marca','class'=>'form-control input-medium')))
            ->add('modelo',TextType::class,array('attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba el modelo','class'=>'form-control input-medium')))
            ->add('indconsumo',IntegerType::class,array('label'=>'Índice consumo','attr'=>array('placeholder'=>'Escriba el índice de cosumo','class'=>'form-control input-medium')))
            ->add('kmsxmantenimiento',IntegerType::class,array('label'=>'Kms por mantenimiento','attr'=>array('placeholder'=>'Escriba los kms/mantenimiento','class'=>'form-control input-medium')))
            ->add('tipocombustible',null,array('label'=>'Tipo de combustible','required'=>true,'attr'=>array('class'=>'form-control input-medium')))
            ->add('tipovehiculo',null,array('placeholder'=>'Seleccione el tipo de vehículo','label'=>'Tipo de vehículo','required'=>true,'attr'=>array('class'=>'form-control input-medium')))
            //->add('chofer',null,array('required'=>true,'attr'=>array('class'=>'form-control input-medium')))
            //->add('responsable',null,array('required'=>true,'attr'=>array('class'=>'form-control input-medium')))
            ->add('estado',ChoiceType::class,[
                'choices'=>['Activo'=>0,'En mantenimiento o reparación'=>1,'Inactivos temporalmente'=>2,'Pendiente a baja'=>3,'Baja'=>4],
                'attr'=>['class'=>'form-control']
            ])
            ->add('responsable',EntityType::class, array(
                'required'=>true,
                'auto_initialize'=>false,
                'class'         =>'App:Responsable',
                'query_builder'=>function(EntityRepository $repository) use($responsable){
                    $res = $repository->createQueryBuilder('responsable');
                    $res->select('r')->from('App:Responsable','r');
                    $res->join('r.tarjetas','t');
                    $res->where('t.activo = :activo')->setParameter('activo', true);
                    $res->where('r.activo = :activo')->setParameter('activo', true);
                    $responsables=$res->getQuery()->getResult();

                    $result=[];
                    foreach ($responsables as $value)
                        if($value->getTarjetas()->count()==1)
                            $result[]=$value;

                    $qb = $repository->createQueryBuilder('responsable');
                    $qb->where('responsable.id IN (:responsable)')->setParameter('responsable',$result);
                    if(null!=$responsable)
                        $qb->orWhere('responsable.id =:id')->setParameter('id',$responsable);
                    return $qb;
                }))

        ;

        $factory=$builder->getFormFactory();
        $builder->addEventSubscriber(new AddVehiculoChoferFieldSubscriber($factory));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Vehiculo::class,
        ]);
    }
}
