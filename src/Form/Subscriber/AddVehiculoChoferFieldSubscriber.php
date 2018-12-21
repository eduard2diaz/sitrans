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
class AddVehiculoChoferFieldSubscriber  implements EventSubscriberInterface{

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
        $tipoVehiculo= is_array($data) ? $data['tipovehiculo'] : $data->getTipovehiculo();
        $chofer= is_array($data) ? $data['chofer'] : $data->getChofer();
        $this->addElements($event->getForm(), $tipoVehiculo,$chofer);
    }

    protected function addElements($form, $tipoVehiculo, $chofer) {

        $form->add($this->factory->createNamed('chofer',EntityType::class,null,array(
            'required'=>true,
            'auto_initialize'=>false,
            'class'         =>'App:Chofer',
            'query_builder'=>function(EntityRepository $repository)use($tipoVehiculo, $chofer){
                $choferesActivos = $repository->createQueryBuilder('chofer');
                $choferesActivos->select('c')->from('App:Chofer','c');
                $choferesActivos->where('c.activo = :activo')->setParameter('activo', true);
                $result=$choferesActivos->getQuery()->getResult();

                $choferes=[];

                if(is_numeric($tipoVehiculo)){
                    $tv=$repository->createQueryBuilder('tipovehiculo');
                    $tv->select('t')->from('App:Tipovehiculo','t');
                    $tv->where('t.id = :id')->setParameter('id', $tipoVehiculo);
                    $tv->setMaxResults(1);
                    $tipoVehiculo=$tv->getQuery()->getSingleResult();
                    if(!$tipoVehiculo){
                        $tipoVehiculo=[];
                    }
                }

                foreach ($result as $value){
                    $esValido=true;
                    foreach ($tipoVehiculo->getIdlicencia() as $lic){
                        if(!$value->getIdlicencia()->contains($lic)){
                            $esValido=false;
                            break;
                        }
                        if(true==$esValido)
                            $choferes[]=$value->getId();
                    }
                }

                $qb = $repository->createQueryBuilder('chofer');
                $qb->where('chofer.activo = :activo AND chofer.id  IN (:choferes)')->setParameters(['activo'=> true,'choferes'=>$choferes]);
                if(null!=$chofer)
                    $qb->orWhere('chofer.id = :id')->setParameter('id', $chofer);
//                if(count($choferes)>0)
                //$qb->andWhere('chofer.id  IN (:choferes)')->setParameter('choferes',$choferes );
                return $qb;



            }
        )));
    }

    public function preSetData(FormEvent $event) {
        $data = $event->getData();
        $form = $event->getForm();

       if(null==$data->getId()){
           $form->add('chofer',null,array('required'=>true,'choices'=>array()));
        }else
       {
           $tipoVehiculo= is_array($data) ? $data['tipovehiculo'] : $data->getTipovehiculo();
           $chofer= is_array($data) ? $data['chofer'] : $data->getChofer();
           $this->addElements($event->getForm(), $tipoVehiculo,$chofer);
       }

    }





}
