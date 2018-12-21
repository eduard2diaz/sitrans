<?php

namespace App\DataFixtures;

use App\Entity\Subelemento;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class SubElementoFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $subelementos=[
            ['nombre'=>'Gas CUP','codigo'=>300101,'elemento'=>300100],
            ['nombre'=>'Gas CUC','codigo'=>300102,'elemento'=>300100],
            ['nombre'=>'Gasolina Especial CUP','codigo'=>300201,'elemento'=>300200],
            ['nombre'=>'Gasolina Especial CUC','codigo'=>300202,'elemento'=>300200],
            ['nombre'=>'Gasolina Regular CUP','codigo'=>300211,'elemento'=>300200],
            ['nombre'=>'Gasolina Regular CUC','codigo'=>300212,'elemento'=>300200],
            ['nombre'=>'Gasolina Motor CUP','codigo'=>300221,'elemento'=>300200],
            ['nombre'=>'Gasolina Motor CUC','codigo'=>300222,'elemento'=>300200],
            ['nombre'=>'Diesel CUP','codigo'=>300231,'elemento'=>300200],
            ['nombre'=>'Diesel CUC','codigo'=>300232,'elemento'=>300200],
            ['nombre'=>'Fuel Oil CUP','codigo'=>300241,'elemento'=>300200],
            ['nombre'=>'Fuel Oil CUC','codigo'=>300242,'elemento'=>300200],
            ['nombre'=>'Lubricantes y aceites CUP','codigo'=>300301,'elemento'=>300300],
            ['nombre'=>'Lubricantes y aceites CUC','codigo'=>300302,'elemento'=>300300],
            ['nombre'=>'Energía Eléctrica CUP','codigo'=>400101,'elemento'=>400100],
            ['nombre'=>'Energía Eléctrica CUC','codigo'=>400102,'elemento'=>400100],
        ];
        foreach ($subelementos as $value){
            $subelemento=new Subelemento();
            $subelemento->setNombre($value['nombre']);
            $subelemento->setCodigo($value['codigo']);
            $elemento=$manager->getRepository('App:Elemento')->findOneByCodigo($value['elemento']);
            $subelemento->setElemento($elemento);
            $subelemento->setPartida($elemento->getPartida());
            $manager->persist($subelemento);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 10;
    }

}
