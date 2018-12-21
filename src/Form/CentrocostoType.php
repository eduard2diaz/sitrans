<?php

namespace App\Form;

use App\Entity\Centrocosto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CentrocostoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',TextType::class,array('attr'=>array('placeholder'=>'Escriba el nombre','class'=>'form-control input-medium')))
            ->add('codigo',TextType::class,array('label'=>'Código','attr'=>array('placeholder'=>'Escriba el código','class'=>'form-control input-medium')))
            ->add('cuenta',null,array('required'=>true,'attr'=>array('class'=>'form-control input-medium'),
                'group_by' => function($choiceValue, $key, $value) {
                return $choiceValue->getNaturaleza()==0 ? 'Deudora' : 'Acreedora';
                },
                ))
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Centrocosto::class,
        ]);
    }
}
