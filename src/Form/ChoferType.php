<?php

namespace App\Form;

use App\Entity\Chofer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ChoferType extends AbstractType
{
    private $doctrine;

    /**
     * ChoferType constructor.
     * @param $doctrine
     */
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @return mixed
     */
    public function getDoctrine()
    {
        return $this->doctrine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $hijas=$this->obtenerInstitucionHijas($options['institucion']);

        $builder
            ->add('nombre',TextType::class,array('attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba el nombre','class'=>'form-control input-medium','pattern' => '^([A-Za-záéíóúñ]{2,}((\s[A-Za-záéíóúñ]{2,})*))*$')))
            ->add('apellido',TextType::class,array('label'=>'Apellidos','attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba los apellidos','class'=>'form-control input-medium','pattern' => '^([A-Za-záéíóúñ]{2,}((\s[A-Za-záéíóúñ]{2,})*))*$')))
            ->add('ci',TextType::class,array('label'=>'Carnet de identidad','attr'=>array('maxlength'=>11,'autocomplete'=>'off','placeholder'=>'Escriba el carnet de identidad','class'=>'form-control input-medium','pattern' => '^[0-9]{11}$')))
            ->add('direccion',TextareaType::class,array('label'=>'Dirección particular','attr'=>array('placeholder'=>'Escriba la dirección particular','class'=>'form-control input-medium')))
            ->add('idlicencia',null,array('label'=>'Licencia','required'=>true,'attr'=>array('class'=>'form-control input-medium')))
            ->add('institucion', null, array('choices'=>$hijas,'label' => 'Institución','attr' => array('class' => 'form-control input-medium')))
            ->add('activo')
        ;
    }

    private function obtenerInstitucionHijas($institucion){
        $em=$this->getDoctrine()->getManager();
        $current=$em->getRepository('App:Institucion')->find($institucion);
        $array=[$current];
        $instituciones=$em->createQuery('SELECT i FROM App:Institucion i JOIN i.institucionpadre p WHERE p.id= :padre')->setParameter('padre',$institucion)->getResult();
        foreach ($instituciones as $value){
            $array=array_merge($array,$this->obtenerInstitucionHijas($value->getId()));
        }
        return $array;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Chofer::class,
        ]);
        $resolver->setRequired('institucion');
    }
}
