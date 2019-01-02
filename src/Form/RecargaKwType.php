<?php

namespace App\Form;

use App\Entity\RecargaKw;
use App\Form\Transformer\DateTimetoStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class RecargaKwType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $reloj=$options['data']->getReloj();

        $builder
            ->add('fecha', TextType::class, array('attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))
            ->add('asignacion', NumberType::class, array('label'=>'Asignación','attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))
            ->add('codigoSTS', TextType::class, array('label'=>'Código STS','attr' => array(
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
                'query_builder'=>function(EntityRepository $repository) use($reloj){
                    $qb=$repository->createQueryBuilder('r');
                    $qb->where('r.activo = :activo')->setParameter('activo',true);
                    if(null!=$reloj)
                        $qb->orWhere('r.id= :id')->setParameter('id',$reloj);
                    return $qb;
                }
            ))
            
            ->add('folio00',NumberType::class,array('help'=>'El folio 00 corresponde a los kilowatts restantes del mes anterior','label'=>'Folio 00','attr'=>array('placeholder'=>'Kilowatts restantes','class'=>'form-control input-medium','readonly'=>true)))
        ;

        $builder->get('fecha')
            ->addModelTransformer(new DateTimetoStringTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RecargaKw::class,
        ]);
    }
}
