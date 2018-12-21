<?php

namespace App\Form;

use App\Entity\RangoTarifaKw;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RangoTarifaKwType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('inicio',IntegerType::class,['attr'=>['class'=>'form-control']])
            ->add('fin',IntegerType::class,['required'=>false,'attr'=>['class'=>'form-control']])
            ->add('valor',NumberType::class,['attr'=>['class'=>'form-control']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RangoTarifaKw::class,
        ]);
    }
}
