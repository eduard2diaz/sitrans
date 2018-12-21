<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Centrocosto;
class CentroCostoFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $areas=[['nombre'=>'Dirección del CAM','codigo'=>'0101','cuenta'=>'875'],
            ['nombre'=>'Asamblea','codigo'=>'0118','cuenta'=>'875'],
            ['nombre'=>'UAC','codigo'=>'011908','cuenta'=>'875'],
        ];
        foreach ($areas as $value){
            $cc = new Centrocosto();
            $cc->setNombre($value['nombre']);
            $cc->setCodigo($value['codigo']);
            $cc->setCuenta($manager->getRepository('App:Cuenta')->findOneBy(['codigo'=>$value['cuenta']]));
            $manager->persist($cc);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}
