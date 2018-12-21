<?php

namespace App\DataFixtures;

use App\Entity\Tipotarjeta;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TipoTarjetaFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $tc=['Reserva','No reserva'];
        foreach ($tc as $value){
            $t = new Tipotarjeta();
            $t->setNombre($value);
            $manager->persist($t);
        }
        $manager->flush();
    }
}
