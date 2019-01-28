<?php

namespace App\Form\template;

use App\Tools\Util;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MesType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults(array(
            'choices'=>Util::LISTADO_MESES,'attr'=>array('class'=>'form-control input-medium')
        ));
    }


    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mes';
    }
}
