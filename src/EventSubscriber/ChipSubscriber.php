<?php

namespace App\EventSubscriber;

use App\Entity\Chip;
use App\Entity\Vehiculo;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use App\Entity\Tarjeta;

class ChipSubscriber implements EventSubscriber
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
        if ($entity instanceof Chip){
            $entity->getTarjeta()->setCantefectivo($entity->getTarjeta()->getCantefectivo()-$entity->getImporte());
            $entity->getTarjeta()->setCantlitros($entity->getTarjeta()->getCantlitros()-$entity->getLitrosextraidos());

            $manager=$args->getEntityManager();
            $vehiculo=$this->getServiceContainer()->get('traza.service')->findVehiculo($entity->getTarjeta()->getId());
            if($vehiculo instanceof Vehiculo) {
                $vehiculo->setLitrosentanque($vehiculo->getLitrosentanque() + $entity->getLitrosextraidos());
                $manager->persist($vehiculo);
                $manager->flush();
            }
            $this->getServiceContainer()->get('traza.service')->persistTrazaTarjeta($entity);

        }
    }

    public function preRemove(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if ($entity instanceof Chip){
            $manager=$args->getEntityManager();
            $traza=$this->getServiceContainer()->get('traza.service')->findTraza(get_class($entity),$entity->getId());

            $entity->getTarjeta()->setCantefectivo($entity->getTarjeta()->getCantefectivo()+$entity->getImporte());
            $entity->getTarjeta()->setCantlitros($entity->getTarjeta()->getCantlitros()+$entity->getLitrosextraidos());

            if($traza->getVehiculo() instanceof Vehiculo) {
                $traza->getVehiculo()->setLitrosentanque($traza->getVehiculo()->getLitrosentanque() - $entity->getLitrosextraidos());
                $manager->persist($traza->getVehiculo());
                $manager->flush();
            }
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
