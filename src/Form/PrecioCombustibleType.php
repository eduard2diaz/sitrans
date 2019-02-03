<?php

namespace App\Form;

use App\Entity\PrecioCombustible;
use App\Form\Transformer\DatetoStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PrecioCombustibleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha', TextType::class, array('label'=>'Fecha','attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))
            ->add('importe',NumberType::class,['attr'=>['autocomplete'=>'off','class'=>'form-control']])
            ->add('tipocombustible',null,['required'=>true,'label'=>'Tipo de combustible'])
        ;

        $builder->get('fecha')
            ->addModelTransformer(new DatetoStringTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PrecioCombustible::class,
        ]);
    }
}
