<?php

namespace App\EventSubscriber;

use App\Entity\Recargatarjeta;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use App\Entity\Tarjeta;

class RecargaTarjetaSubscriber implements EventSubscriber
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
        if ($entity instanceof Recargatarjeta){
            $entity->getTarjeta()->setCantefectivo($entity->getTarjeta()->getCantefectivo()+$entity->getCantidadefectivo());
            $this->getServiceContainer()->get('traza.service')->persistTrazaTarjeta($entity);
        }
    }

    public function preRemove(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if ($entity instanceof Recargatarjeta){
            $entity->getTarjeta()->setCantefectivo($entity->getTarjeta()->getCantefectivo()-$entity->getCantidadefectivo());
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
