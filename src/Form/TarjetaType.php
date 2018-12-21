<?php

namespace App\Form;

use App\Entity\Tarjeta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class TarjetaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigo',TextType::class,array('label'=>'Código','attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba el nombre','class'=>'form-control input-medium')))
            ->add('tipotarjeta',null,array('label'=>'Tipo de tarjeta','required'=>true,'attr'=>array('class'=>'form-control input-medium')))
            ->add('tipocombustible',null,array('label'=>'Tipo de combustible','required'=>true,'attr'=>array('class'=>'form-control input-medium')))
            ->add('activo')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tarjeta::class,
        ]);
    }
}
