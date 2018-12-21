<?php

namespace App\EventSubscriber;

use App\Entity\Hojaruta;
use App\Entity\Vehiculo;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;

class HojarutaSubscriber implements EventSubscriber
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
        if ($entity instanceof Hojaruta){
            $entity->getVehiculo()->setLitrosentanque($entity->getVehiculo()->getLitrosentanque()-$entity->getLitrosconsumidos());
            $args->getEntityManager()->persist($entity->getVehiculo());
            $args->getEntityManager()->flush();
            $this->getServiceContainer()->get('traza.service')->persistTrazaVehiculo($entity);
        }

    }

    public function preRemove(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if ($entity instanceof Hojaruta){
            $entity->getVehiculo()->setLitrosentanque($entity->getVehiculo()->getLitrosentanque()+$entity->getLitrosconsumidos());
            $args->getEntityManager()->persist($entity->getVehiculo());
            $args->getEntityManager()->flush();
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
