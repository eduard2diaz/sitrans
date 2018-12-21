<?php

namespace App\Form;

use App\Entity\Chip;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Form\Transformer\DateTimetoStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class ChipType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isdisabled=$options['data']->getId() ? true : false;
        $tarjeta=$options['data']->getTarjeta();

        $builder
            ->add('numerocomprobante', IntegerType::class, array('label'=>'Número de comprobante','attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))
            ->add('tarjeta',EntityType::class,array(
                'disabled'=>$isdisabled,
                'auto_initialize'=>false,
                'class'         =>'App:Tarjeta',
                'query_builder'=>function(EntityRepository $repository) use($tarjeta){
                    $qb=$repository->createQueryBuilder('t');
                    $qb->join('t.responsable','r');
                    $qb->where('t.activo = :activo AND t.cantlitros > 0 AND r.activo = :activo')
                        ->setParameter('activo',true);
                    if(null!=$tarjeta)
                        $qb->orWhere('t.id= :id')->setParameter('id',$tarjeta);
                    return $qb;
                }
            ))

            ->add('idfisico', TextType::class, array('label'=>'Identificador lógico','attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))
            ->add('idlogico', TextType::class, array('label'=>'Identificador físico','attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))

            ->add('cupet',null,['required'=>true])
            ->add('fecha', TextType::class, array('attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))

            ->add('moneda', ChoiceType::class, array(
                'choices'=>['Moneda Nacional'=>0,'Divisa'=>1]
                ,'attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))
            ->add('servicio', TextType::class, array('attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))

            ->add('saldoinicial', NumberType::class, array('label'=>'Saldo inicial','attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))
            ->add('litrosextraidos', IntegerType::class, array('label'=>'Litros extraídos','attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))

            ->add('importe', NumberType::class, array('attr' => array(
                'autocomplete' => 'off',
                'readonly'=>true,
                'class' => 'form-control input-medium'
            )))
        ;

        $builder->get('fecha')
            ->addModelTransformer(new DateTimetoStringTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Chip::class,
        ]);
    }
}
