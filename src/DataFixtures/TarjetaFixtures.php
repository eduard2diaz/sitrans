<?php

namespace App\DataFixtures;

use App\Entity\Tarjeta;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TarjetaFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $tarjetas=[
            ['codigo'=>1963,'tipotarjeta'=>'No reserva','tipocombustible'=>'Diesel'],
            ['codigo'=>0037,'tipotarjeta'=>'No reserva','tipocombustible'=>'Diesel'],
            ['codigo'=>8006,'tipotarjeta'=>'No reserva','tipocombustible'=>'Diesel'],
            ['codigo'=>0144,'tipotarjeta'=>'No reserva','tipocombustible'=>'Gasolina regular B90'],
            ['codigo'=>8893,'tipotarjeta'=>'No reserva','tipocombustible'=>'Gasolina regular B90'],
            ['codigo'=>9098,'tipotarjeta'=>'No reserva','tipocombustible'=>'Gasolina regular B90'],
            ['codigo'=>2302,'tipotarjeta'=>'No reserva','tipocombustible'=>'Gasolina regular B90'],
            ['codigo'=>5065,'tipotarjeta'=>'No reserva','tipocombustible'=>'Gasolina regular B90'],
            ['codigo'=>7284,'tipotarjeta'=>'No reserva','tipocombustible'=>'Gasolina regular B90'],
            ['codigo'=>8746,'tipotarjeta'=>'No reserva','tipocombustible'=>'Gasolina regular B90'],
            ['codigo'=>0151,'tipotarjeta'=>'No reserva','tipocombustible'=>'Gasolina regular B90'],
            ['codigo'=>6962,'tipotarjeta'=>'No reserva','tipocombustible'=>'Gasolina regular B90'],
            ['codigo'=>6065,'tipotarjeta'=>'No reserva','tipocombustible'=>'Gasolina regular B90'],
            ];

        foreach ($tarjetas as $value){
            $tarjeta=new Tarjeta();
            $tarjeta->setCodigo($value['codigo']);
            $tipotarjeta=$manager->getRepository('App:Tipotarjeta')->findOneByNombre($value['tipotarjeta']);
            $tarjeta->setTipotarjeta($tipotarjeta);
            $tipocombustible=$manager->getRepository('App:Tipocombustible')->findOneByNombre($value['tipocombustible']);
            $tarjeta->setTipocombustible($tipocombustible);
            $tarjeta->setActivo(true);
            $manager->persist($tarjeta);
        }
        $manager->flush();
    }

    public function getOrder()
    {
     return 11;
    }
}
