<?php

namespace App\Form;

use App\Entity\PlanportadoresArea;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class PlanportadoresAreaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('valor',NumberType::class,array(
                'attr'=>array('autocomplete'=>'off','class'=>'form-control')
            ))

            ->add('areas',null,array(
                'label'=>'Áreas',
                'required'=>true
            ))

            ->add('categoria',ChoiceType::class,array(
                'label'=>'Categoría',
                'choices'=>array(
                    'Combustible'=>0,
                    'Electricidad'=>1
                )
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlanportadoresArea::class,
        ]);
    }
}
