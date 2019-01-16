<?php

namespace App\Form;

use App\Entity\Responsable;
use App\Form\Subscriber\AddAreaFieldSubscriber;
use App\Form\Subscriber\AddTarjetaFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class ResponsableType extends AbstractType
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
        $data = $options['data'];
        $institucion = $options['institucion'];
        if (!$data->getId())
            $id = null;
        else
            $id = $data->getId();


        $hijas=$this->obtenerInstitucionHijas($institucion);

        $builder
            ->add('nombre', TextType::class, array('attr' => array('autocomplete' => 'off', 'placeholder' => 'Escriba el nombre', 'class' => 'form-control input-medium', 'pattern' => '^([A-Za-záéíóúñ]{2,}((\s[A-Za-záéíóúñ]{2,})*))*$')))
            ->add('apellidos', TextType::class, array('attr' => array('autocomplete' => 'off', 'placeholder' => 'Escriba los apellidos', 'class' => 'form-control input-medium', 'pattern' => '^([A-Za-záéíóúñ]{2,}((\s[A-Za-záéíóúñ]{2,})*))*$')))
            ->add('ci', TextType::class, array('label' => 'Carnet de identidad', 'attr' => array('maxlength' => 11, 'autocomplete' => 'off', 'placeholder' => 'Escriba el carnet de identidad', 'class' => 'form-control input-medium', 'pattern' => '^[0-9]{11}$')))
            ->add('direccion', TextareaType::class, array('label' => 'Dirección particular', 'attr' => array('placeholder' => 'Escriba la dirección particular', 'class' => 'form-control input-medium')))
            ->add('institucion', null, array('placeholder'=>'Seleccione una institución','choices'=>$hijas,'required'=>true,'label' => 'Institución','attr' => array('class' => 'form-control input-medium')))
            ->add('tarjetas',null,['choices'=>[]])
            ->add('area',null,['choices'=>[]])
            ->add('activo');

        $factory=$builder->getFormFactory();
        $builder->addEventSubscriber(new AddAreaFieldSubscriber($factory));
        $builder->addEventSubscriber(new AddTarjetaFieldSubscriber($factory,$id));

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
            'data_class' => Responsable::class,
        ]);
        $resolver->setRequired(['institucion']);
    }
}
