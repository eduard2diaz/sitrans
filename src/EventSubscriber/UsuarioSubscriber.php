<?php

namespace App\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use App\Entity\Usuario;

class UsuarioSubscriber implements EventSubscriber
{
    private $encoder;

    function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }

    /**
     * @return UserPasswordEncoderInterface
     */
    public function getEncoder(): UserPasswordEncoderInterface
    {
        return $this->encoder;
    }



    public function prePersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if ($entity instanceof Usuario){
            $entity->setPassword($this->getEncoder()->encodePassword($entity,$entity->getPassword()));
        }
    }

    public function getSubscribedEvents()
    {
        return [
            'prePersist',
     //       'postPersist',
        ];
    }
}
