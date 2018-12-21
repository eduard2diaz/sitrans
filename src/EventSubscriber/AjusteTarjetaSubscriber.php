<?php

namespace App\EventSubscriber;

use App\Entity\AjusteTarjeta;
use App\Entity\Vehiculo;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;

class AjusteTarjetaSubscriber implements EventSubscriber
{
    private $serviceContainer;

    function __construct(ContainerInterface $serviceContainer) {
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * @return mixed
     */
    public function getServiceContainer() {
        return $this->serviceContainer;
    }

    public function postPersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if ($entity instanceof AjusteTarjeta){
            $multiplo=$entity->getTipo()==1 ? 1 : -1;
            $entity->getTarjeta()->setCantefectivo($entity->getTarjeta()->getCantefectivo()+$entity->getCantefectivo()*$multiplo);
            $entity->getTarjeta()->setCantlitros($entity->getTarjeta()->getCantlitros()+$entity->getMonto()*$multiplo);
            $this->getServiceContainer()->get('traza.service')->persistTrazaTarjeta($entity);
        }
    }

    public function preRemove(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if ($entity instanceof AjusteTarjeta){
            $multiplo=$entity->getTipo()==1 ? -1 : 1;
            $entity->getTarjeta()->setCantefectivo($entity->getTarjeta()->getCantefectivo()+$entity->getCantefectivo()*$multiplo);
            $entity->getTarjeta()->setCantlitros($entity->getTarjeta()->getCantlitros()+$entity->getMonto()*$multiplo);
            $traza=$this->getServiceContainer()->get('traza.service')->findTraza(get_class($entity),$entity->getId());
            $this->getServiceContainer()->get('traza.service')->removeTraza($traza);
        }
    }



    public function getSubscribedEvents()
    {
        return [
            'postPersist',
            'preRemove',
        ];
    }


}
