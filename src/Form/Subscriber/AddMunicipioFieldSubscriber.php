<?php

namespace App\Form\Subscriber;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\PropertyAccess\PropertyAccess;
use App\Entity\Provincia;
use App\Entity\Municipio;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Description of AddCargoFieldSubscriber
 *
 * @author eduardo
 */
class AddMunicipioFieldSubscriber  implements EventSubscriberInterface{

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
        $provincia= is_array($data) ? $data['provincia'] : $data->getProvincia();
        $this->addElements($event->getForm(), $provincia);
    }

    protected function addElements($form, $provincia) {
        $form->add($this->factory->createNamed('municipio',EntityType::class,null,array(
            'auto_initialize'=>false,
            'class'         =>'App:Municipio',
            'choice_label' => function ($elemento) {
                return $elemento->getNombre();
            },
            'query_builder'=>function(EntityRepository $repository)use($provincia){
                $qb=$repository->createQueryBuilder('municipio')
                    ->innerJoin('municipio.provincia','p');
                if($provincia instanceof Provincia){
                    $qb->where('p.id = :id')
                        ->setParameter('id',$provincia);
                }elseif(is_numeric($provincia)){
                    $qb->where('p.id = :id')
                        ->setParameter('id',$provincia);
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
           $form->add('municipio',null,array('required'=>true,'choices'=>array()));
        }else
       {
           $provincia= is_array($data) ? $data['provincia'] : $data->getProvincia();
           $this->addElements($event->getForm(), $provincia);
       }

    }





}
