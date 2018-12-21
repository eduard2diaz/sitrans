<?php

namespace App\DataFixtures;

use App\Entity\Reloj;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class RelojFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $relojes = [
            ['activo'=>true,'codigo'=>'24000193458','area'=>'Dirección del CAM']
        ];

        foreach ($relojes as $value){
            $reloj=new Reloj();
            $reloj->setActivo($value['activo']);
            $area=$manager->getRepository('App:Area')->findOneBy(['nombre'=>$value['area']]);
            if(!$area)
                continue;
            $reloj->setArea($area);
            $reloj->setcodigo($value['codigo']);
            $manager->persist($reloj);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 8;
    }
}
