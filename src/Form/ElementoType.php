<?php

namespace App\Form;

use App\Entity\Elemento;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ElementoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',TextType::class,array('attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba el nombre','class'=>'form-control input-medium')))
            ->add('codigo',IntegerType::class,array('label'=>'Código','attr'=>array('placeholder'=>'Escriba el código','class'=>'form-control input-medium')))
            ->add('partida',null,array('required'=>true,
                'choice_label' => function ($partida) {
                    return $partida->getNombre()."  - ".$partida->getCodigo();
                }
            ,'attr'=>array('class'=>'form-control input-medium')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Elemento::class,
        ]);
    }
}
