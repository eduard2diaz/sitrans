<?php

namespace App\DataFixtures;

use App\Entity\Provincia;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/*
 * Clase que carga todas las provincias de Cuba
 */
class ProvinciaFixtures extends Fixture  implements OrderedFixtureInterface{

    public function load(ObjectManager $manager) {
        $provincias = array(
            array('nombre' => 'Guantánamo'),
            array('nombre' => 'Santiago de Cuba'),
            array('nombre' => 'Holguín'),
            array('nombre' => 'Granma'),
            array('nombre' => 'Las Tunas'),
            array('nombre' => 'Camaguey'),
            array('nombre' => 'Ciego de Ávila'),
            array('nombre' => 'Sancti Spiritus'),
            array('nombre' => 'Villa Clara'),
            array('nombre' => 'Cienfuegos'),
            array('nombre' => 'Matanzas'),
            array('nombre' => 'Mayabeque'),
            array('nombre' => 'La Habana'),
            array('nombre' => 'Artemisa'),
            array('nombre' => 'Pinar del Rio'),
            array('nombre' => 'Isla de la Juventud'),
           
        );
        foreach ($provincias as $provincia) {
            $entidad = new Provincia();
            $entidad->setNombre($provincia['nombre']);
            $manager->persist($entidad);
        }
        $manager->flush();
    }

    public function getOrder() {
        return 2;
    }

}

?>
