<?php

namespace App\Form;

use App\Entity\CierreMesTarjeta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\Transformer\DateTimetoStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class CierreMesTarjetaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $cierre=$options['data']->getCierre()->getId();
        $tarjeta=null==$options['data']->getTarjeta() ? null : $options['data']->getTarjeta()->getId();
        //$readonly=$options['data']->getId()==null ? false : true;
        $readonly=true;
        $builder
            ->add('fecha', TextType::class, array('attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))
            ->add('restantecombustible', IntegerType::class, array('label'=>'Combustible restante','attr' => array(
                'autocomplete' => 'off',
                'readonly'=>$readonly,
                'class' => 'form-control input-medium'
            )))
            ->add('combustibleconsumido', IntegerType::class, array('label'=>'Combustible consumido','attr' => array(
                'autocomplete' => 'off',
                'readonly'=>$readonly,
                'class' => 'form-control input-medium'
            )))
            ->add('restanteefectivo', NumberType::class, array('label'=>'Efectivo restante','attr' => array(
                'autocomplete' => 'off',
                'readonly'=>$readonly,
                'class' => 'form-control input-medium'
            )))
            ->add('efectivoconsumido', NumberType::class, array('label'=>'Efectivo consumido','attr' => array(
                'autocomplete' => 'off',
                'readonly'=>$readonly,
                'class' => 'form-control input-medium'
            )))
            ->add('tarjeta',EntityType::class,array(
                'placeholder'=>'Seleccione una tarjeta',
                'auto_initialize'=>false,
                'class'         =>'App:Tarjeta',
                'query_builder'=>function(EntityRepository $repository) use($cierre,$tarjeta){
                    $res = $repository->createQueryBuilder('tarjeta');
                    $res->select('ct')->from('App:CierreMesTarjeta','ct');
                    $res->join('ct.cierre','c');
                    $res->where('c.id = :id')->setParameter('id', $cierre);
                    $cierres=$res->getQuery()->getResult();

                    $tarjetasconcierre=[];
                    foreach ($cierres as $value){
                        if($tarjeta!=null && $tarjeta==$value->getTarjeta()->getId())
                            continue;
                        $tarjetasconcierre[]=$value->getTarjeta()->getId();
                    }

                    $qb=$repository->createQueryBuilder('t');
                    $qb->where('t.activo = :activo')->setParameter('activo',true);
                    if(!empty($tarjetasconcierre))
                    $qb->andWhere('t.id NOT IN (:tarjetas)')->setParameter('tarjetas',$tarjetasconcierre);
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
            'data_class' => CierreMesTarjeta::class,
        ]);
    }
}
