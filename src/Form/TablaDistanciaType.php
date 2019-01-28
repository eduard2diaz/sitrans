<?php

namespace App\Form;

use App\Entity\TablaDistancia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TablaDistanciaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('origen', TextType::class,['attr'=>['class'=>'form-control']])
            ->add('destino',TextType::class,['attr'=>['class'=>'form-control']])
            ->add('kms',NumberType::class,['label'=>'Kilómetros','attr'=>['class'=>'form-control']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TablaDistancia::class,
        ]);
    }
}
