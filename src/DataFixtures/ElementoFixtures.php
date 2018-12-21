<?php

namespace App\DataFixtures;

use App\Entity\Elemento;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ElementoFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $elementos=[
            ['nombre'=>'Gas','codigo'=>300100,'partida'=>30],
            ['nombre'=>'Combustible','codigo'=>300200,'partida'=>30],
            ['nombre'=>'Lubricante y aceite','codigo'=>300300,'partida'=>30],
            ['nombre'=>'Energía Eléctrica','codigo'=>400100,'partida'=>40],
        ];
        foreach ($elementos as $value){
            $elemento=new Elemento();
            $elemento->setNombre($value['nombre']);
            $elemento->setCodigo($value['codigo']);
            $partida=$manager->getRepository('App:Partida')->findOneByCodigo($value['partida']);
            $elemento->setPartida($partida);
            $manager->persist($elemento);
        }

        $manager->flush();
    }

    public function getOrder()
    {
     return 9;
    }
}
