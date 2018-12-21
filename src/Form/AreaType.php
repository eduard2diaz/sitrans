<?php

namespace App\Form;

use App\Entity\Area;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class AreaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',TextType::class,array('attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba el nombre','class'=>'form-control input-medium')))
            ->add('codigo',TextType::class,array('label'=>'Código','attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba el código','class'=>'form-control input-medium')))
            ->add('ccosto',null,array('label'=>'Centro de costo','required'=>true,'attr'=>array('class'=>'form-control input-medium')))
            ->add('direccionparticular',TextareaType::class,array('label'=>'Dirección particular','attr'=>array('placeholder'=>'Escriba la dirección particular','class'=>'form-control input-medium')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Area::class,
        ]);
    }
}
