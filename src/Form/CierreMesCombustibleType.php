<?php

namespace App\Form;

use App\Entity\CierreMesCombustible;
use App\Form\template\MesType;
use App\Form\template\YearType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CierreMesCombustibleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mes',MesType::class)
            ->add('anno',YearType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CierreMesCombustible::class,
        ]);
    }
}
