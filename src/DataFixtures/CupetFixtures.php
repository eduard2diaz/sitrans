<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Cupet;

class CupetFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $cupets=[['nombre'=>'CupCimex curva Batabano','municipio'=>'Batabanó','provincia'=>'Mayabeque'],
            ['nombre'=>'CupCimex entrada Bejucal','municipio'=>'Bejucal','provincia'=>'Mayabeque'],
            ['nombre'=>'Service Oro Negro San Jose','municipio'=>'San José de las Lajas','provincia'=>'Mayabeque'],
        ];


        foreach ($cupets as $value){

            $consulta=$manager->createQuery('SELECT m FROM App:Municipio m JOIN m.provincia p WHERE p.nombre = :provincia AND m.nombre= :nombre');
            $consulta->setParameters(['nombre'=>$value['municipio'],'provincia'=>$value['provincia']]);
            $consulta->setMaxResults(1);
            $municipio=$consulta->getSingleResult();
            if(!$municipio)
                continue;

            $cc = new Cupet();
            $cc->setNombre($value['nombre']);
            $cc->setEnfuncionamiento(true);
            $cc->setMunicipio($municipio);
            $cc->setProvincia($municipio->getProvincia());
            $cc->setDireccion('PENDIENTE');
            $manager->persist($cc);
        }

        $manager->flush();
    }

    public function getOrder()
    {
     return 4;
    }

}
