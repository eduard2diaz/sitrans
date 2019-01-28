<?php

namespace App\Form\Subscriber;

use App\Entity\Cuenta;
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
class AddPlanEfectivoSubElementoFieldSubscriber  implements EventSubscriberInterface{

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
        $form->add($this->factory->createNamed('subelemento',EntityType::class,null,array(
            'auto_initialize'=>false,
            'multiple'=>true,
            'class'         =>'App:Subelemento',
            'choice_label' => function ($elemento) {
                return $elemento->getNombre()."  - ".$elemento->getCodigo();
            },
            'query_builder'=>function(EntityRepository $repository)use($cuenta){
                $qb=$repository->createQueryBuilder('subelemento')
                    ->innerJoin('subelemento.partida','p')
                    ->innerJoin('p.cuenta','c');
                if($cuenta instanceof Cuenta){
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
           $form->add('subelemento',null,array('required'=>true,'choices'=>array()));
        }else
       {
           $cuenta= is_array($data) ? $data['cuenta'] : $data->getCuenta();
           $this->addElements($event->getForm(), $cuenta);
       }

    }





}
