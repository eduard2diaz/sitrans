<?php

namespace App\Form;

use App\Entity\Institucion;
use App\Form\Subscriber\AddMunicipioFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class InstitucionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',TextType::class,['attr'=>['class'=>'form-control']])
            ->add('activo',null,['label'=>' '])
            ->add('provincia',null,['placeholder'=>'Seleccione una provincia',])
            ->add('municipio')
            ->add('institucionpadre',null,['label'=>'Institución padre'])

        ;

        $factory=$builder->getFormFactory();
        $builder->addEventSubscriber(new AddMunicipioFieldSubscriber($factory));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Institucion::class,
        ]);
    }
}
