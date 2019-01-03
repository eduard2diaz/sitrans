<?php

namespace App\Form;

use App\Entity\Responsable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class ResponsableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data=$options['data'];
        if(!$data->getId())
            $id=null;
        else
        $id=$data->getId();

        $builder

            ->add('nombre',TextType::class,array('attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba el nombre','class'=>'form-control input-medium','pattern' => '^([A-Za-záéíóúñ]{2,}((\s[A-Za-záéíóúñ]{2,})*))*$')))
            ->add('apellidos',TextType::class,array('attr'=>array('autocomplete'=>'off','placeholder'=>'Escriba los apellidos','class'=>'form-control input-medium','pattern' => '^([A-Za-záéíóúñ]{2,}((\s[A-Za-záéíóúñ]{2,})*))*$')))
            ->add('ci',TextType::class,array('label'=>'Carnet de identidad','attr'=>array('maxlength'=>11,'autocomplete'=>'off','placeholder'=>'Escriba el carnet de identidad','class'=>'form-control input-medium','pattern' => '^[0-9]{11}$')))
            ->add('direccion',TextareaType::class,array('label'=>'Dirección particular','attr'=>array('placeholder'=>'Escriba la dirección particular','class'=>'form-control input-medium')))

           // ->add('tarjetas')
            ->add('tarjetas',EntityType::class, array(
                'required'=>false,
                'multiple'=>true,
                'auto_initialize'=>false,
                'class'         =>'App:Tarjeta',
                'query_builder'=>function(EntityRepository $repository) use($id) {
                    $qb = $repository->createQueryBuilder('tarjeta');
                    $qb->distinct(true);
                    $qb->select('t')->from('App:Tarjeta','t');
                    $qb->join('t.responsable','r');
                    $result=$qb->getQuery()->getResult();

                    if(count($result)>0) {
                        $qb = $repository->createQueryBuilder('tarjeta');
                        $qb->select('t')->from('App:Tarjeta','t');
                        $qb->where('t.activo = true AND t.id NOT IN (:responsable)')->setParameter('responsable', $result);
                        $result = $qb->getQuery()->getResult();
                    }
                    else{
                        //Si ningua tarjeta tiene responsable devuelve el listado de tarjetas activas
                        $qb = $repository->createQueryBuilder('tarjeta');
                        $qb->select('t')->from('App:Tarjeta','t');
                        $qb->where('t.activo = true');
                        $result = $qb->getQuery()->getResult();
                    }

                    //Si estamos modificando un responsable, devuelveme ademas todas mis tarjetas
                   if(null!=$id) {
                        $qb = $repository->createQueryBuilder('tarjeta');
                        $qb->select('t')->from('App:Tarjeta', 't');
                        $qb->join('t.responsable', 'r');
                        $qb->where('r.id = :id');
                        $qb->setParameter('id', $id);
                        $mias = $qb->getQuery()->getResult();
                    }

                    $qb = $repository->createQueryBuilder('tarjeta');
                    $qb->where('tarjeta.activo = true AND tarjeta.id IN (:responsable)')->setParameter('responsable',$result );
                    if(null!=$id)
                        $qb->orWhere('tarjeta.id IN (:mias)')->setParameter('mias',$mias );

                    return $qb;

                }))

            ->add('area',null,array('label'=>'Área','required'=>true,'attr'=>array('class'=>'form-control input-medium')))
            ->add('activo')
        ;
    }

    private function obtenerIds($array):array{
        $result=[];
        foreach ($array as $value)
            $result[]=$value['id'];
        return $result;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Responsable::class,
        ]);
    }
}
