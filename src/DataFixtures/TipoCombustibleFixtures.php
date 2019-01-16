<?php

namespace App\DataFixtures;

use App\Entity\Tipocombustible;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/*
 * Clase que carga los tipos de combustibles disponibles
 */
class TipoCombustibleFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $tc=['Gasolina regular B90','Diesel','Gas'];
        foreach ($tc as $value){
            $t = new Tipocombustible();
            $t->setNombre($value);
            $manager->persist($t);
        }
        $manager->flush();

    }

    public function getOrder()
    {
        return 1;
    }
}
