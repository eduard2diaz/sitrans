<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Tipoactividad;
class TipoActividadFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $tactividades=['Administración','Implementación y soporte','Negocios','Servicios Generales'];
        foreach ($tactividades as $value){
            $t = new Tipoactividad();
            $t->setNombre($value);
            $manager->persist($t);
        }
        $manager->flush();
    }
}