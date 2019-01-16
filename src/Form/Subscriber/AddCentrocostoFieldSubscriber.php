<?php

namespace App\Form\Subscriber;

use App\Entity\Centrocosto;
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
class AddCentrocostoFieldSubscriber  implements EventSubscriberInterface{

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
        $cuenta= is_array($data) ? $data['cuenta'] : $data->getCuenta();
        $this->addElements($event->getForm(), $cuenta);
    }

    protected function addElements($form, $cuenta) {
        $form->add($this->factory->createNamed('centrocosto',EntityType::class,null,array(
            'label'=>'Centro de costo',
            'required'=>true,
            'auto_initialize'=>false,
            'multiple'=>true,
            'class'         =>'App:Centrocosto',
            'query_builder'=>function(EntityRepository $repository)use($cuenta){
                $qb=$repository->createQueryBuilder('centrocosto')
                    ->innerJoin('centrocosto.cuenta','c');
                if($cuenta instanceof Centrocosto){
                    $qb->where('c.id = :id')
                        ->setParameter('id',$cuenta);
                }elseif(is_numeric($cuenta)){
                    $qb->where('c.id = :id')
                        ->setParameter('id',$cuenta);
                }else{
                    $qb->where('c.id = :id')
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
           $form->add('centrocosto',null,array('label'=>'Centro de costo','required'=>true,'choices'=>array()));
        }else
       {
           $cuenta= is_array($data) ? $data['cuenta'] : $data->getCuenta();
           $this->addElements($event->getForm(), $cuenta);
       }

    }





}
