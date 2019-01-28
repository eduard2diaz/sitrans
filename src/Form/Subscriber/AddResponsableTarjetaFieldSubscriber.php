<?php

namespace App\Form\Subscriber;

use App\Entity\Tarjeta;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Description of AddCargoFieldSubscriber
 *
 * @author eduardo
 */
class AddResponsableTarjetaFieldSubscriber  implements EventSubscriberInterface{

    private $factory;
    private $id;

    /**
     * AddTarjetaFieldSubscriber constructor.
     */
    public function __construct(FormFactoryInterface $factory,$id=null)
    {
        $this->factory = $factory;
        $this->id = $id;
    }

    public static function getSubscribedEvents() {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',

        );
    }

    /**
     * Cuando el usuario llene los datos del formulario y haga el envío del mismo,
     * este método será ejecutado.
     */
    public function preSubmit(FormEvent $event) {
        $data = $event->getData();
        if(null===$data){
            return;
        }
        $institucion= is_array($data) ? $data['institucion'] : $data->getInstitucion()->getId();
        $responsable= $this->id;

        $this->addElements($event->getForm(), $institucion,$responsable);
    }

    protected function addElements($form, $institucion,$responsable) {

        $form->add($this->factory->createNamed('tarjetas', EntityType::class,null, array(
            'required' => false,
            'multiple' => true,
            'auto_initialize' => false,
            'class' => 'App:Tarjeta',
            'query_builder' => function (EntityRepository $repository) use ($responsable, $institucion) {
                $qb = $repository->createQueryBuilder('tarjeta');
                $qb->distinct(true);
                $qb->select('t')->from('App:Tarjeta', 't');
                $qb->join('t.responsable', 'r');
                $qb->join('t.tipotarjeta', 'tt');
                $qb->join('tt.institucion', 'i');
                $qb->where('i.id= :institucion')->setParameter('institucion', $institucion);
                $result = $qb->getQuery()->getResult();

                if (count($result) > 0) {
                    $qb = $repository->createQueryBuilder('tarjeta');
                    $qb->select('t')->from('App:Tarjeta', 't');
                    $qb->join('t.tipotarjeta', 'tt');
                    $qb->join('tt.institucion', 'i');
                    $qb->where('t.activo = true AND i.id= :institucion AND t.id NOT IN (:responsable)')
                        ->setParameters(['responsable' => $result, 'institucion' => $institucion]);
                    $result = $qb->getQuery()->getResult();
                } else {
                    //Si ningua tarjeta tiene responsable devuelve el listado de tarjetas activas
                    $qb = $repository->createQueryBuilder('tarjeta');
                    $qb->select('t')->from('App:Tarjeta', 't');
                    $qb->join('t.tipotarjeta', 'tt');
                    $qb->join('tt.institucion', 'i');
                    $qb->where('t.activo = true AND i.id= :institucion ')
                        ->setParameter('institucion', $institucion);
                    $result = $qb->getQuery()->getResult();
                }

                //Si estamos modificando un responsable, devuelveme ademas todas mis tarjetas
                if (null != $responsable) {
                    $qb = $repository->createQueryBuilder('tarjeta');
                    $qb->select('t')->from('App:Tarjeta', 't');
                    $qb->join('t.responsable', 'r');
                    $qb->join('t.tipotarjeta', 'tt');
                    $qb->join('tt.institucion', 'i');
                    $qb->where('r.id = :id AND i.id= :institucion');
                    $qb->setParameters(['id' => $responsable, 'institucion' => $institucion]);
                    $mias = $qb->getQuery()->getResult();
                }

                $parameters=['responsable' => $result, 'institucion' => $institucion];
                $qb = $repository->createQueryBuilder('tarjeta');
                $qb->join('tarjeta.tipotarjeta', 'tt');
                $qb->join('tt.institucion', 'i');
                $qb->where('tarjeta.activo = true AND i.id= :institucion AND tarjeta.id IN (:responsable)');
                if (null != $responsable) {
                    $qb->orWhere('i.id= :institucion AND tarjeta.id IN (:mias)');
                    $parameters['mias']=$mias;
                }
                $qb->setParameters($parameters);

                return $qb;

            })));


    }

    public function preSetData(FormEvent $event) {
        $data = $event->getData();
        $form = $event->getForm();

       if(null==$data->getId()){
           $form->add('tarjetas',null,array('label'=>'Tarjetas','required'=>true,'choices'=>[]));
        }else
       {
           $institucion= is_array($data) ? $data['institucion'] : $data->getInstitucion()->getId();
           $responsable= $this->id;
           $this->addElements($event->getForm(), $institucion,$responsable);
       }

    }





}
