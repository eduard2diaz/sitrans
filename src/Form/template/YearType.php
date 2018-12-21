<?php

namespace App\Form\template;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class YearType extends AbstractType
{

   public function configureOptions(OptionsResolver $resolver)
   {
       parent::configureOptions($resolver);
       $current_year=date('Y');
       $choices=array($current_year=>$current_year);
       if (date('m') ==12){
           $choices[$current_year+1]=$current_year+1;
       }

       $resolver->setDefaults(['choices' => $choices,'label'=>'Año','attr'=>array('class'=>'form-control input-medium')]);
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
        return 'year';
    }
}
