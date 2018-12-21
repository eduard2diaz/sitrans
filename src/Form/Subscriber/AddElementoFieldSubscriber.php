<?php

namespace App\Form\Subscriber;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\PropertyAccess\PropertyAccess;
use App\Entity\Partida;
use App\Entity\Elemento;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Description of AddCargoFieldSubscriber
 *
 * @author eduardo
 */
class AddElementoFieldSubscriber  implements EventSubscriberInterface{

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
        $partida= is_array($data) ? $data['partida'] : $data->getPartida();
        $this->addElements($event->getForm(), $partida);
    }

    protected function addElements($form, $partida) {
        $form->add($this->factory->createNamed('elemento',EntityType::class,null,array(
            'auto_initialize'=>false,
            'class'         =>'App:Elemento',
            'choice_label' => function ($elemento) {
                return $elemento->getNombre()."  - ".$elemento->getCodigo();
            },
            'query_builder'=>function(EntityRepository $repository)use($partida){
                $qb=$repository->createQueryBuilder('elemento')
                    ->innerJoin('elemento.partida','p');
                if($partida instanceof Partida){
                    $qb->where('p.id = :id')
                        ->setParameter('id',$partida);
                }elseif(is_numeric($partida)){
                    $qb->where('p.id = :id')
                        ->setParameter('id',$partida);
                }else{
                    $qb->where('p.id = :id')
                        ->setParameter('id',null);
                }
                return $qb;
            }
        )));
    }

    public function preSetData(FormEvent $event) {
        $data = $event->getData();
        $form = $event->getForm();

       if(null==$data->getId()){
           $form->add('elemento',null,array('required'=>true,'choices'=>array()));
        }else
       {
           $partida= is_array($data) ? $data['partida'] : $data->getPartida();
           $this->addElements($event->getForm(), $partida);
       }

    }





}
