<?php

namespace App\Form;

use App\Entity\Subelemento;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\Subscriber\AddElementoFieldSubscriber;

class SubelementoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',TextType::class,array('attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba el nombre','class'=>'form-control input-medium')))
            ->add('codigo',IntegerType::class,array('label'=>'Código','attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba el código','class'=>'form-control input-medium')))
            ->add('partida',null,array(
                'placeholder'=>'Seleccione una partida'
                ,'required'=>true,
                'choice_label' => function ($partida) {
                    return $partida->getNombre()."  - ".$partida->getCodigo();
                }
            ,'attr'=>array('class'=>'form-control input-medium')))
            ->add('elemento',null,['required'=>true])
        ;

        $factory=$builder->getFormFactory();
        $builder->addEventSubscriber(new AddElementoFieldSubscriber ($factory));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Subelemento::class,
        ]);
    }
}
