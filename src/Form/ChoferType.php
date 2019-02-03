<?php

namespace App\Form;

use App\Entity\Chofer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Tools\InstitucionService;

class ChoferType extends AbstractType
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
            ->add('nombre',TextType::class,array('attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba el nombre','class'=>'form-control input-medium','pattern' => '^([A-Za-záéíóúñ]{2,}((\s[A-Za-záéíóúñ]{2,})*))*$')))
            ->add('apellido',TextType::class,array('label'=>'Apellidos','attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba los apellidos','class'=>'form-control input-medium','pattern' => '^([A-Za-záéíóúñ]{2,}((\s[A-Za-záéíóúñ]{2,})*))*$')))
            ->add('ci',TextType::class,array('label'=>'Carnet de identidad','attr'=>array('maxlength'=>11,'autocomplete'=>'off','placeholder'=>'Escriba el carnet de identidad','class'=>'form-control input-medium','pattern' => '^[0-9]{11}$')))
            ->add('direccion',TextareaType::class,array('label'=>'Dirección particular','attr'=>array('placeholder'=>'Escriba la dirección particular','class'=>'form-control input-medium')))
            ->add('idlicencia',null,array('label'=>'Licencia','required'=>true,'attr'=>array('class'=>'form-control input-medium')))
            ->add('institucion', null, array('required'=>true,'choices'=>$hijas,'label' => 'Institución','attr' => array('class' => 'form-control input-medium')))
            ->add('activo')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Chofer::class,
        ]);
    }
}
