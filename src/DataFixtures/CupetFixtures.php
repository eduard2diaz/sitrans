<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Cupet;

class CupetFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $cupets=[['nombre'=>'CupCimex curva Batabano'],
            ['nombre'=>'CupCimex entrada Bejucal'],
            ['nombre'=>'Service Oro Negro San Jose'],
            ['nombre'=>'Garage CupCimex Santiago de las Vegas'],
        ];
        foreach ($cupets as $value){
            $cc = new Cupet();
            $cc->setNombre($value['nombre']);
            $cc->setEnfuncionamiento(true);
            $cc->setDireccion('PENDIENTE');
            $manager->persist($cc);
        }

        $manager->flush();
    }

}
