<?php

namespace App\DataFixtures;

use App\Entity\Rol;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class RolesFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $roles=['ROLE_CAJERO','ROLE_JEFETRANSPORTE','ROLE_ADMIN','ROLE_SUPERADMIN','ROLE_ELECTRICIDAD'];
        foreach ($roles as $value){
            $rol=new Rol();
            $rol->setNombre($value);
            $manager->persist($rol);
        }


        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;// TODO: Implement getOrder() method.
    }
}
