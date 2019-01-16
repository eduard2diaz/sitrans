<?php

namespace App\Form;

use App\Entity\RangoTarifaKw;
use App\Entity\TarifaKw;
use App\Form\Transformer\DatetoStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\RangoTarifaKwType;

class TarifaKwType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha', TextType::class, array('attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))
            ->add('rangoTarifaKws', CollectionType::class, array(
                'entry_type' => RangoTarifaKwType::class,
                'allow_add'=>true,
                'allow_delete'=>true,
                'by_reference'   => true,
                'prototype_data' => new RangoTarifaKw(),
                'label'          => ' ',

            ))
        ;

        $builder->get('fecha')
            ->addModelTransformer(new DatetoStringTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TarifaKw::class,
        ]);
    }
}
