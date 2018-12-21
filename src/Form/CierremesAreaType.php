<?php

namespace App\Form;

use App\Entity\CierremesArea;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\Transformer\DateTimetoStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class CierremesAreaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $cierre=$options['data']->getCierre()->getId();
        $area=null==$options['data']->getArea() ? null : $options['data']->getArea()->getId();
        $readonly=$options['data']->getId()==null ? false : true;
        $builder
            ->add('fecha', TextType::class, array('attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))

            ->add('restante', NumberType::class, array('label'=>'Kilowatts restantes','attr' => array(
                'autocomplete' => 'off',
                'readonly'=>$readonly,
                'class' => 'form-control input-medium'
            )))
            ->add('consumido', NumberType::class, array('label'=>'Kilowatts consumidos','attr' => array(
                'autocomplete' => 'off',
                'readonly'=>$readonly,
                'class' => 'form-control input-medium'
            )))
            ->add('efectivoconsumido', NumberType::class, array('label'=>'Efectivo consumido','attr' => array(
                'autocomplete' => 'off',
                'readonly'=>$readonly,
                'class' => 'form-control input-medium'
            )))
            ->add('efectivorestante', NumberType::class, array('label'=>'Efectivo restante','attr' => array(
                'autocomplete' => 'off',
                'readonly'=>$readonly,
                'class' => 'form-control input-medium'
            )))

            ->add('area',EntityType::class,array(
                'label'=>'Área',
                'required'=>true,
                'placeholder'=>'Seleccione un área',
                'auto_initialize'=>false,
                'class'         =>'App:Area',
                'query_builder'=>function(EntityRepository $repository) use($cierre,$area){
                    $res = $repository->createQueryBuilder('area');
                    $res->select('ca')->from('App:CierremesArea','ca');
                    $res->join('ca.cierre','c');
                    $res->where('c.id = :id')->setParameter('id', $cierre);
                    $cierres=$res->getQuery()->getResult();

                    $areasconcierre=[];
                    foreach ($cierres as $value){
                        if($area!=null && $area==$value->getArea()->getId())
                            continue;
                        $areasconcierre[]=$value->getArea()->getId();
                    }

                    $qb=$repository->createQueryBuilder('a');
                    if(!empty($areasconcierre))
                        $qb->Where('a.id NOT IN (:areas)')->setParameter('areas',$areasconcierre);
                    return $qb;
                }
            ))

        ;

        $builder->get('fecha')
            ->addModelTransformer(new DateTimetoStringTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CierremesArea::class,
        ]);
    }
}
