<?php

namespace App\Form;

use App\Entity\Vehiculo;
use App\Form\Subscriber\AddVehiculoChoferFieldSubscriber;
use App\Form\Subscriber\AddVehiculoResponsableFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Tools\InstitucionService;

class VehiculoType extends AbstractType
{
    private $institucion_service;

    public function __construct(InstitucionService $institucion_service)
    {
        $this->institucion_service=$institucion_service;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $hijas=$this->institucion_service->obtenerArbolInstitucional();

        $builder
            ->add('matricula',TextType::class,array('label'=>'Matrícula','attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba la matrícula','class'=>'form-control input-medium')))
            ->add('marca',TextType::class,array('attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba la marca','class'=>'form-control input-medium')))
            ->add('modelo',TextType::class,array('attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba el modelo','class'=>'form-control input-medium')))
            ->add('indconsumo',IntegerType::class,array('label'=>'Índice consumo','attr'=>array('placeholder'=>'Escriba el índice de cosumo','class'=>'form-control input-medium')))
            ->add('kmsxmantenimiento',IntegerType::class,array('label'=>'Kms por mantenimiento','attr'=>array('placeholder'=>'Escriba los kms/mantenimiento','class'=>'form-control input-medium')))
            ->add('tipocombustible',null,array('label'=>'Tipo de combustible','required'=>true,'attr'=>array('class'=>'form-control input-medium')))
            ->add('tipovehiculo',null,array('placeholder'=>'Seleccione el tipo de vehículo','label'=>'Tipo de vehículo','required'=>true,'attr'=>array('class'=>'form-control input-medium')))
            ->add('estado',ChoiceType::class,[
                'choices'=>['Activo'=>0,'En mantenimiento o reparación'=>1,'Inactivo temporalmente'=>2,'Pendiente a baja'=>3,'Baja'=>4],
                'attr'=>['class'=>'form-control']
            ])
            ->add('institucion', null, array('placeholder'=>'Seleccione una institución','choices'=>$hijas,'required'=>true,'label' => 'Institución','attr' => array('class' => 'form-control input-medium')))
        ;

        $factory=$builder->getFormFactory();
        $builder->addEventSubscriber(new AddVehiculoChoferFieldSubscriber($factory));
        $builder->addEventSubscriber(new AddVehiculoResponsableFieldSubscriber($factory));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Vehiculo::class,
        ]);
    }
}
