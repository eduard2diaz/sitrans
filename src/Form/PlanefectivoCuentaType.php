<?php

namespace App\Form;

use App\Entity\PlanefectivoCuenta;
use App\Form\Subscriber\AddSubElementoFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class PlanefectivoCuentaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('valor',NumberType::class,array(
                'attr'=>array('autocomplete'=>'off','class'=>'form-control')
            ))
            ->add('cuenta',null,['placeholder'=>'Seleccione una cuenta','required'=>true])
            ->add('subelemento',null,['required'=>true])
            ->add('centrocosto',null,['label'=>'Centro de costo','required'=>true])
        ;

        $factory=$builder->getFormFactory();
        $builder->addEventSubscriber(new AddSubElementoFieldSubscriber($factory));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlanefectivoCuenta::class,
        ]);
    }
}
