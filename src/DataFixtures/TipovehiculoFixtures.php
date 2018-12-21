<?php

namespace App\DataFixtures;

use App\Entity\Tipovehiculo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TipovehiculoFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $tc=[['nombre'=>'Carro','licencias'=>['A']],['nombre'=>'Tractor','licencias'=>['D']]];
        foreach ($tc as $value){
            $t = new Tipovehiculo();
            $t->setNombre($value['nombre']);
            foreach ($value['licencias'] as $lic){
                $licencia=$manager->getRepository('App:Licencia')->findOneByNombre($lic);
                if(null!=$licencia)
                    $t->addIdlicencium($licencia);
            }

            $manager->persist($t);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 8;
    }


}
