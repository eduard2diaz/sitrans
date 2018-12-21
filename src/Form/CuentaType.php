<?php

namespace App\Form;

use App\Entity\Cuenta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CuentaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',TextType::class,array('attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba el nombre','class'=>'form-control input-medium')))
            ->add('codigo',IntegerType::class,array('label'=>'Código','attr'=>array('placeholder'=>'Escriba el código','class'=>'form-control input-medium')))
            ->add('naturaleza',ChoiceType::class,array('required'=>true,
                'choices'=>['Acreedora'=>1,'Deudora'=>0],
                'attr'=>array(
                'class'=>'form-control input-medium')))
            ->add('nae',TextType::class,array('label'=>'NAE','attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba el NAE','class'=>'form-control input-medium')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cuenta::class,
        ]);
    }
}
