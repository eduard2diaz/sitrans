<?php

namespace App\Form;

use App\Entity\Cupet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CupetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',TextType::class,array('attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba el nombre','class'=>'form-control input-medium')))
            ->add('direccion',TextareaType::class,array('label'=>'Dirección particular','attr'=>array('placeholder'=>'Escriba la dirección particular','class'=>'form-control input-medium')))
            ->add('enfuncionamiento',null,array('label'=>'En funcionamiento'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cupet::class,
        ]);
    }
}
