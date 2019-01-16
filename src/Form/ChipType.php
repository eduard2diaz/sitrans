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
        $tarjeta=$options['data']->getTarjeta();
        $institucion=$options['institucion'];

        $builder
            ->add('numerocomprobante', IntegerType::class, array('label'=>'Número de comprobante','attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))

            ->add('tarjeta',EntityType::class,array(
                'required'=>true,
                'auto_initialize'=>false,
                'class'         =>'App:Tarjeta',
                'query_builder'=>function(EntityRepository $repository) use($tarjeta,$institucion){
                    $qb=$repository->createQueryBuilder('t');
                    $qb->join('t.responsable','r');
                    $qb->join('t.tipotarjeta','tt');
                    $qb->join('tt.institucion','i');
                    $qb->where('t.activo = :activo AND t.cantlitros > 0 AND r.activo = :activo AND i.id= :id')->setParameters(['activo'=>true,'id'=>$institucion]);
                    if(null!=$tarjeta)
                        $qb->orWhere('t.id = :tarjeta')->setParameter('tarjeta',$tarjeta);
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
        $resolver->setRequired('institucion');
    }
}
