<?php

namespace App\Form\Subscriber;

use App\Entity\Area;
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
class AddAreaFieldSubscriber  implements EventSubscriberInterface{

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
        $this->addElements($event->getForm(), $institucion);
    }

    protected function addElements($form, $institucion) {
        $form->add($this->factory->createNamed('area',EntityType::class,null,array(
            'label'=>'Área',
            'required'=>true,
            'auto_initialize'=>false,
            'class'         =>'App:Area',
            'query_builder'=>function(EntityRepository $repository)use($institucion){
                $qb=$repository->createQueryBuilder('area')
                    ->innerJoin('area.ccosto','cc')
                    ->innerJoin('cc.cuenta','c')
                    ->innerJoin('c.institucion','i');
                if($institucion instanceof Area){
                    $qb->where('i.id = :id')
                        ->setParameter('id',$institucion);
                }elseif(is_numeric($institucion)){
                    $qb->where('i.id = :id')
                        ->setParameter('id',$institucion);
                }else{
                    $qb->where('i.id = :id')
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
           $form->add('area',null,array('label'=>'Área','required'=>true,'choices'=>[]));
        }else
       {
           $institucion= is_array($data) ? $data['institucion'] : $data->getInstitucion()->getId();
           $this->addElements($event->getForm(), $institucion);
       }

    }





}
