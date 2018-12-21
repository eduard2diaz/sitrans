<?php

namespace App\Form;

use App\Entity\Reloj;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class RelojType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id=$options['data']->getId();
        $area=$options['data']->getArea();
        $disabled=$id!=null ? true : false;
        $builder
            ->add('codigo',TextType::class,array('label'=>'Código','attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba el código','class'=>'form-control input-medium')))
          //  ->add('area',null,array('label'=>'Área','required'=>true,'disabled'=>$disabled,'attr'=>array('class'=>'form-control input-medium')))
            ->add('activo')

            ->add('area',EntityType::class, array(
                'required'=>true,
                'label'=>'Área',
                'auto_initialize'=>false,
                'class'         =>'App:Area',
                'query_builder'=>function(EntityRepository $repository) use ($id,$area)  {
                        $qb = $repository->createQueryBuilder('area');
                        $qb->distinct(true);
                        $qb->select('a.id')->from('App:Reloj', 'r');
                        $qb->join('r.area', 'a');
                        $qb->where('r.activo= TRUE');
                        $areasActivas= $qb->getQuery()->getResult();

                        $qb = $repository->createQueryBuilder('area');
                        if(!empty($areasActivas)) {
                            $qb->where('area.id NOT IN (:responsable)')->setParameter('responsable', $areasActivas);
                        }
                        if(null!=$id) {
                            $qb->orWhere('area.id =:id')->setParameter('id', $area);
                        }


                    return $qb;

                }))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reloj::class,
        ]);
    }
}
