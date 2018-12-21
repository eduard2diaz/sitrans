<?php

namespace App\Form;

use App\Entity\Licencia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LicenciaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',TextType::class,array('attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba el nombre','class'=>'form-control input-medium')))
         //   ->add('idtipovehiculo')
         //   ->add('idchofer')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Licencia::class,
        ]);
    }
}
