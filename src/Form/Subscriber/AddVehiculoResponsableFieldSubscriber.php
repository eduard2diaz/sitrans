<?php

namespace App\Form\Subscriber;

use App\Entity\Tipovehiculo;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\PropertyAccess\PropertyAccess;
use App\Entity\Chofer;
use App\Entity\Elemento;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Description of AddCargoFieldSubscriber
 *
 * @author eduardo
 */
class AddVehiculoResponsableFieldSubscriber  implements EventSubscriberInterface{

    private $factory;
    /**
     * AddTarjetaFieldSubscriber constructor.
     */
    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
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
        $responsable= is_array($data) ? $data['responsable'] : $data->getResponsable()->getId();
        $this->addElements($event->getForm(), $responsable,$institucion);
    }

    protected function addElements($form, $responsable, $institucion) {
        $form->add($this->factory->createNamed('responsable',EntityType::class,null, array(
            'required'=>false,
            'auto_initialize'=>false,
            'class'         =>'App:Responsable',
            'query_builder'=>function(EntityRepository $repository) use($responsable,$institucion){
                $res = $repository->createQueryBuilder('responsable');
                $res->select('r')->from('App:Responsable','r');
                $res->join('r.tarjetas','t');
                $res->join('t.tipotarjeta','tt');
                $res->join('tt.institucion','i');
                $res->where('t.activo = TRUE AND r.activo = TRUE AND i.id= :institucion')->setParameter('institucion', $institucion);
                $responsables=$res->getQuery()->getResult();

                $result=[];
                foreach ($responsables as $value)
                    if($value->getTarjetas()->count()==1)
                        $result[]=$value;

                $qb = $repository->createQueryBuilder('responsable');
                $qb->where('responsable.id IN (:responsable)')->setParameter('responsable',$result);
                if(null!=$responsable)
                    $qb->orWhere('responsable.id =:id')->setParameter('id',$responsable);
                return $qb;
            })));
    }

    public function preSetData(FormEvent $event) {
        $data = $event->getData();
        $form = $event->getForm();

       if(null==$data->getId()){
           $form->add('responsable',null,array('required'=>false,'choices'=>array()));
        }else
       {
           $institucion= is_array($data) ? $data['institucion'] : $data->getInstitucion()->getId();
           $responsable= is_array($data) ? $data['responsable'] : $data->getResponsable()!= null ? $data->getResponsable()->getId() : null;
           $this->addElements($event->getForm(), $responsable,$institucion);
       }

    }





}
