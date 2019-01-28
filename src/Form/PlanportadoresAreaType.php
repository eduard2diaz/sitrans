<?php

namespace App\Form;

use App\Entity\PlanportadoresArea;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class PlanportadoresAreaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $institucion=$options['data']->getPlanportadores()->getInstitucion()->getId();

        $builder
            ->add('valor',NumberType::class,array(
                'attr'=>array('autocomplete'=>'off','class'=>'form-control')
            ))

            ->add('areas',null,array(
                'auto_initialize'=>false,
                'label'=>'Área',
                'class'         =>'App:Area',
                'query_builder'=>function(EntityRepository $repository) use($institucion){
                    $qb=$repository->createQueryBuilder('a');
                    $qb->join('a.ccosto','cc');
                    $qb->join('cc.cuenta','c');
                    $qb->join('c.institucion','i');
                    $qb->where('i.id = :id')->setParameter('id',$institucion);
                    return $qb;
                }
            ))

            ->add('categoria',ChoiceType::class,array(
                'label'=>'Categoría',
                'choices'=>array(
                    'Combustible'=>0,
                    'Electricidad'=>1
                )
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlanportadoresArea::class,
        ]);
    }
}
