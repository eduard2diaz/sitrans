<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Area;

class AreaFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $areas=[['nombre'=>'Dirección del CAM','codigo'=>'0201','direccion'=>'Calle 8 Esq 7','centroc'=>'0101'],
            ['nombre'=>'Asamblea','codigo'=>'0101','direccion'=>'Calle 13 Esq 6','centroc'=>'0118'],
            ['nombre'=>'UAC','codigo'=>'022002','direccion'=>'Calle 8 Esq 7','centroc'=>'011908'],
            ];
        foreach ($areas as $value){
            $area = new Area();
            $area->setNombre($value['nombre']);
            $area->setCodigo($value['codigo']);
            $area->setDireccionparticular($value['direccion']);
            $area->setCcosto($manager->getRepository('App:Centrocosto')->findOneBy(['codigo'=>$value['centroc']]));
            $manager->persist($area);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 7;
    }
}
