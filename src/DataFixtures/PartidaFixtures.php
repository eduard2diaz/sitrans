<?php

namespace App\DataFixtures;

use App\Entity\Partida;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class PartidaFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $partidas=[
            ['nombre'=>'Energía','codigo'=>40,'cuenta'=>'Gastos corriente','tipopartida'=>'Electricidad'],
            ['nombre'=>'Combustible y Lubricante','codigo'=>30,'cuenta'=>'Gastos corriente','tipopartida'=>'Combustible'],
        ];
        foreach ($partidas as $value){
            $partida=new Partida();
            $partida->setNombre($value['nombre']);
            $partida->setCodigo($value['codigo']);
            $cuenta=$manager->getRepository('App:Cuenta')->findOneByNombre($value['cuenta']);
            $partida->setCuenta($cuenta);
            $tipopartida=$manager->getRepository('App:Tipopartida')->findOneByNombre($value['tipopartida']);
            $partida->setTipopartida($tipopartida);
            $manager->persist($partida);
        }

        $manager->flush();
    }

    public function getOrder()
    {
       return 6;
    }
}
