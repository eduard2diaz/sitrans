<?php

namespace App\DataFixtures;

use App\Entity\Usuario;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/*
 * Clase que el superadministrador que posee el sistema por defecto
 */
class UsuarioFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
         $usuario = new Usuario();
         $usuario->setNombre('admin');
         $usuario->setPassword('admin');
         $usuario->setUsuario('admin');
         $usuario->setApellidos('admin');
         $usuario->setCorreo('admin@unah.edu.cu');
         $usuario->setActivo(true);
         $usuario->setSalt('');

         $rol=$manager->getRepository('App:Rol')->findOneBy(['nombre'=>'ROLE_SUPERADMIN']);
            if(null!=$rol)
                $usuario->addIdrol($rol);
         $manager->persist($usuario);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        // TODO: Implement getOrder() method.
        return 2;
    }
}
