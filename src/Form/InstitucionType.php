<?php

namespace App\Form;

use App\Entity\Institucion;
use App\Form\Subscriber\AddMunicipioFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class InstitucionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $options['data'];
        $id = $data->getId();

        $builder
            ->add('nombre', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('activo', null, ['label' => ' '])
            ->add('provincia', null, ['placeholder' => 'Seleccione una provincia',])
            ->add('institucionpadre', EntityType::class, array(
                'label' => 'Institución padre',
                'auto_initialize' => false,
                'required' => false,
                'class' => 'App:Institucion',
                'query_builder' => function (EntityRepository $repository) use ($id) {
                    $qb = $repository->createQueryBuilder('i');
                    $qb->where('i.activo = :activo')->setParameter('activo', true);
                    if (null != $id)
                        $qb->andWhere('i.id <> :id')->setParameter('id', $id);
                    return $qb;
                }
            ));

        $factory = $builder->getFormFactory();
        $builder->addEventSubscriber(new AddMunicipioFieldSubscriber($factory));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Institucion::class,
        ]);
    }
}
