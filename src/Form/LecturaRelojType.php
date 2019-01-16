<?php

namespace App\Form;

use App\Entity\LecturaReloj;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\Transformer\DateTimetoStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class LecturaRelojType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $reloj=$options['data']->getReloj();
        $institucion=$options['institucion'];
        $disabled=$reloj!= null  ? true : false;

        $builder
            ->add('fecha', TextType::class, array('attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))
            ->add('reloj',EntityType::class,array(
                'required'=>true,
                'placeholder'=>'Seleccione un reloj',
                'auto_initialize'=>false,
                'class'         =>'App:Reloj',
                'choice_label'=>function($value){
                    return $value->getCodigo().'--'.$value->getArea()->getNombre();
                },
                'query_builder'=>function(EntityRepository $repository) use($reloj,$institucion){
                    $qb=$repository->createQueryBuilder('r');
                    $qb->join('r.area','a');
                    $qb->join('a.ccosto','cc');
                    $qb->join('cc.cuenta','c');
                    $qb->join('c.institucion','i');
                    $qb->where('r.activo = :activo AND i.id= :institucion')
                        ->setParameters(['activo'=>true,'institucion'=>$institucion]);
                    if(null!=$reloj)
                        $qb->orWhere('r.id= :id')->setParameter('id',$reloj);
                    return $qb;
                }
            ))
            ->add('lectura', IntegerType::class, array('attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))
        ;
        $builder->get('fecha')
            ->addModelTransformer(new DateTimetoStringTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LecturaReloj::class,
        ]);
        $resolver->setRequired('institucion');
    }
}
