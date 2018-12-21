<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Licencia;
class LicenciaFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $licencias=['A-1','A','B','C-1','C','D-1','D','E','F'];
        foreach ($licencias as $value){
            $l = new Licencia();
            $l->setNombre($value);
            $manager->persist($l);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }
}
