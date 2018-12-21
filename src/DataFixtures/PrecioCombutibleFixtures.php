<?php

namespace App\DataFixtures;

use App\Entity\PrecioCombustible;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class PrecioCombutibleFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $fecha=new \DateTime('01-11-2018');
        $precios=[
            ['tipocombustible'=>'Gasolina regular B90','importe'=>0.98,'fecha'=>$fecha],
            ['tipocombustible'=>'Diesel','importe'=>0.8,'fecha'=>$fecha],
          //  ['tipocombustible'=>'Gas','importe'=>300100,'fecha'=>$fecha],
        ];
        foreach ($precios as $value){
            $precio=new PrecioCombustible();
            $precio->setFecha($value['fecha']);
            $precio->setImporte($value['importe']);
            $tipocombustible=$manager->getRepository('App:Tipocombustible')->findOneByNombre($value['tipocombustible']);
            $precio->setTipocombustible($tipocombustible);
            $manager->persist($precio);
        }

        $manager->flush();
    }
    
    public function getOrder()
    {
     return 2;
    }
}
