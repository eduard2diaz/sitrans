<?php

namespace App\DataFixtures;

use App\Entity\RangoTarifaKw;
use App\Entity\TarifaKw;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TarifaKwFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $rangos=[
            ['inicio'=>0,'fin'=>100,'precio'=>0.09],
            ['inicio'=>101,'fin'=>150,'precio'=>0.30],
            ['inicio'=>151,'fin'=>200,'precio'=>0.40],
            ['inicio'=>201,'fin'=>250,'precio'=>0.60],
            ['inicio'=>251,'fin'=>300,'precio'=>0.80],
            ['inicio'=>301,'fin'=>350,'precio'=>1.50],
            ['inicio'=>351,'fin'=>500,'precio'=>1.80],
            ['inicio'=>501,'fin'=>1000,'precio'=>2.00],
            ['inicio'=>1001,'fin'=>5000,'precio'=>3.00],
            ['inicio'=>5001,'fin'=>null,'precio'=>5.00],
        ];

        $tarifa=new TarifaKw();
        $tarifa->setFecha(new \DateTime('2015-12-01'));
        $manager->persist($tarifa);
        foreach ($rangos as $value){
            $rango = new RangoTarifaKw();
            $rango->setInicio($value['inicio']);
            $rango->setFin($value['fin']);
            $rango->setValor($value['precio']);
            $rango->setTarifas($tarifa);
            $manager->persist($rango);
        }
        $manager->flush();
    }
}
