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
        $id = $options['data']->getId();
        $area = $options['data']->getArea();
        $institucion = $options['institucion'];
        $disabled = $id != null ? true : false;
        /*
         * Con el siguiente código el sistema te muestra solo las áreas de tu institución que no tienen o reloj o que
         * el mismo no está activo, en caso de ser una edición te muestra el área del reloj que estes editando este o no
         * este activo.
         */
        $builder
            ->add('codigo', TextType::class, array('label' => 'Código', 'attr' => array('autocomplete' => 'off', 'placeholder' => 'Escriba el código', 'class' => 'form-control input-medium')))
            ->add('activo')
            ->add('area', EntityType::class, array(
                'required' => true,
                'label' => 'Área',
                'auto_initialize' => false,
                'class' => 'App:Area',
                'query_builder' => function (EntityRepository $repository) use ($id, $area, $institucion) {
                    $qb = $repository->createQueryBuilder('area');
                    $qb->distinct(true);
                    $qb->select('a.id')->from('App:Reloj', 'r');
                    $qb->join('r.area', 'a');
                    $qb->join('a.ccosto', 'cc');
                    $qb->join('cc.cuenta', 'c');
                    $qb->join('c.institucion', 'i');
                    $qb->where('r.activo= TRUE');
                    $qb->andWhere('i.id= :institucion');
                    $qb->setParameter('institucion', $institucion);
                    $areasActivas = $qb->getQuery()->getResult();

                    $qb = $repository->createQueryBuilder('a');
                    $qb->join('a.ccosto', 'cc');
                    $qb->join('cc.cuenta', 'c');
                    $qb->join('c.institucion', 'i');
                    $qb->where('i.id= :institucion');
                    $qb->setParameter('institucion', $institucion);
                    if (!empty($areasActivas)) {
                        $qb->andWhere('a.id NOT IN (:responsable)')->setParameter('responsable', $areasActivas);
                    }
                    if (null != $id) {
                        $qb->orWhere('a.id= :id')->setParameter('id', $area);
                    }

                    return $qb;

                }));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reloj::class,
        ]);
        $resolver->setRequired('institucion');
    }
}
