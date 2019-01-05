<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Municipio;

class MunicipioFixtures extends Fixture implements OrderedFixtureInterface {

    public function load(ObjectManager $manager) {
        //Generando el usuario administrador
        $provincias = array(
            array('nombre' => 'Guantánamo', 'municipios' => array(
                    'Niceto Pérez', 'Caimanera', 'Guantánamo', 'El Salvador',
                    'Manuel Tames', 'Yateras', 'San Antonio del Sur', 'Imías',
                    'Baracoa', 'Maisí'
                )),
            array('nombre' => 'Santiago de Cuba', 'municipios' => array(
                    'Guama', 'Tercer Frente', 'Santiago de Cuba', 'Palma Soriano',
                    'Contramaestre', 'Mella', 'San Luis', 'Songo la Maya', 'Segundo Frente'
                )),
            array('nombre' => 'Holguín', 'municipios' => array(
                    'Buenaventura', 'Cacocum', 'Holguin', 'Gibara',
                    'Urbano Noris', 'Báguanos', 'Rafael Freyre', 'Báguanos',
                    'Cueto', 'Mayarí', 'Banes', 'Frank País', 'Sagua de Tánamo', 'Moa'
                )),
            array('nombre' => 'Granma', 'municipios' => array(
                    'Niquero', 'Pilón', 'Media Luna', 'Campechuela', 'Bartolomé Masó', 'Manzanillo',
                    'Yara', 'Buey Arriba', 'Guisa', 'Bayamo', 'Río Cauto', 'Jiguaní', 'Cauto Cristo'
                )),
            array('nombre' => 'Las Tunas', 'municipios' => array(
                    'Amancio Rodriguez', 'Colombia', 'Jobabo', 'Las Tunas', 'Calixto García', 'Manatí',
                    'Puerto Grande', 'Jesús Menéndez'
                )),
            array('nombre' => 'Camaguey', 'municipios' => array(
                    'Carlos Manuel de Céspedes', 'Florida', 'Vertientes', 'Santa Cruz', 'Najasa', 'Jimaguayú',
                    'Camaguey', 'Cubitas', 'Esmeralda', 'Minas', 'Sibanicú', 'Guáimaro', 'Nuevitas'
                )),
            array('nombre' => 'Ciego de Ávila', 'municipios' => array(
                    'Chambas', 'Florencia', 'Majagua', 'Venezuela', 'Ciego de Avila', 'Ciro Redondo',
                    'Morón', 'Baraguá', 'Primero de Enero', 'Bolivia'
                )),
            array('nombre' => 'Sancti Spiritus', 'municipios' => array(
                    'Trinidad', 'Fomento', 'Sancti Spiritus', 'La Sierpe', 'Cabaiguán', 'Taguasco', 'Jatibonico', 'Yaguajay'
                )),
            array('nombre' => 'Villa Clara', 'municipios' => array(
                    'Corralillo', 'Quemado de Guines', 'Santo Domingo', 'Sagua la Grande', 'Cifuentes',
                    'Ranchuelo', 'Santa Clara', 'Camajuaní', 'Encrucijada', 'Caibarién', 'Remedios',
                    'Placetas', 'Manicaragua'
                )),
            array('nombre' => 'Cienfuegos', 'municipios' => array(
                    'Aguada de Pasajeros', 'Rodas', 'Abreus', 'Lajas', 'Palmira', 'Cruces', 'Cienfuegos',
                    'Cumanayagua'
                )),
            array('nombre' => 'Matanzas', 'municipios' => array(
                    'Ciénaga de Zapata', 'Unión de Reyes', 'Limonar', 'Matanzas', 'Varadero', 'Cárdenas',
                    'Jovellanos', 'Jaguey Grande', 'Pedro Betancourt', 'Calimete', 'Los Arabos',
                    'Colon', 'Perico', 'Martí'
                )),
            array('nombre' => 'Mayabeque', 'municipios' => array(
                    'Bejucal', 'San José de las Lajas', 'Jaruco', 'Santa Cruz del Norte', 'Madruga', 'Nueva Paz', 'San Nicolás de Bari',
                    'Güines', 'Melena del Sur', 'Batabanó', 'Quivicán'
                )),
            array('nombre' => 'La Habana', 'municipios' => array(
                    'Playa', 'La Lisa', 'Boyeros', 'Marianao', 'Arroyo Naranjo', 'Cerro', 'Plaza de la Revolución',
                    'Diez de Octubre', 'Cotorro', 'San Miguel del Padrón', 'Habana Vieja', 'Regla', 'Guanabacoa',
                    'Habana del Este'
                )),
            array('nombre' => 'Artemisa', 'municipios' => array(
                    'Bahía Honda', 'Mariel', 'Guanajay', 'Caimito', 'Bauta', 'San Antonio de los Baños', 'Güira de Melena', 'Alquizar', 'Artemisa', 'Candelaria', 'San Cristóbal'
                )),
            array('nombre' => 'Pinar del Rio', 'municipios' => array(
                    'Sandino', 'Mantua', 'Guane', 'San Juan y Martínez', 'San Luis', 'Minas de Matahambre',
                    'Pinar del Rio', 'Viñales', 'La palma', 'Consolación del Sur', 'Los Palacios', 'San Cristobal', 'Bahía Honda', 'Candelaria'
                )),
            array('nombre' => 'Isla de la Juventud', 'municipios' => array(
                    'Isla de la Juventud'
                )),
        );
        foreach ($provincias as $value) {
            $provincia = $manager->getRepository('App:Provincia')->findOneBy(array(
                'nombre' => $value['nombre']
            ));
            foreach ($value['municipios'] as  $v) {
            $mun = new Municipio();
            $mun->setNombre($v);
            $mun->setProvincia($provincia);
            $manager->persist($mun);
        }
        }
        
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder() {
        return 3;   // TODO: Implement getOrder() method.
    }

}

?>
