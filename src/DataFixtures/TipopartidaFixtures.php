<?php

namespace App\DataFixtures;

use App\Entity\Tipopartida;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/*
 * Clase que carga los tipos de partidas disponibles, cada una hace referencia a un determinado portador energético
 */
class TipopartidaFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $partidas=['Combustible','Electricidad'];
        foreach ($partidas as $value){
            $p=new Tipopartida();
            $p->setNombre($value);
            $manager->persist($p);
        }


        $manager->flush();
    }
}
