<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Cuenta;

class CuentaFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $cuentas=[
            ['nombre'=>'Gastos corriente','codigo'=>'875','naturaleza'=>0,'nae'=>'7511'],
        ];
        foreach ($cuentas as $value){
            $cuenta = new Cuenta();
            $cuenta->setNombre($value['nombre']);
            $cuenta->setCodigo($value['codigo']);
            $cuenta->setNaturaleza($value['naturaleza']);
            $cuenta->setNae($value['nae']);
            $manager->persist($cuenta);
        }
        $manager->flush();
    }

    public function getOrder()
    {
     return 3;
    }
}
