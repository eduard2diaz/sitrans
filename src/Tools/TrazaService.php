<?php

namespace App\Tools;
use App\Entity\Chip;
use App\Entity\Traza;

class TrazaService
{
    private $em;

    /**
     * EnergiaService constructor.
     * @param $em
     */
    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * @return mixed
     */
    public function getEm()
    {
        return $this->em;
    }

    public function persistTrazaVehiculo($entity){
        $manager=$this->getEm()->getManager();
        $traza=new Traza();
        $traza->setIdentificador($entity->getId());
        $traza->setEntity(get_class($entity));
        $traza->setChofer($entity->getVehiculo()->getChofer());
        $traza->setVehiculo($entity->getVehiculo());
        $traza->setIndiceConsumo($entity->getVehiculo()->getIndconsumo());
        $traza->setArea($entity->getVehiculo()->getResponsable()->getArea());
        $traza->setResponsable($entity->getVehiculo()->getResponsable());
        $traza->setTarjeta($entity->getVehiculo()->getResponsable()->getTarjetas()->first());
        $traza->setEfectivo($entity->getVehiculo()->getResponsable()->getTarjetas()->first()->getCantefectivo());
        $traza->setCombustibleentanque($entity->getVehiculo()->getLitrosentanque());
        $traza->setIndiceConsumo($entity->getVehiculo()->getIndconsumo());

        $manager->persist($traza);
        $manager->flush();
    }

    public function persistTrazaTarjeta($entity){
        $manager=$this->getEm()->getManager();
        $traza=new Traza();
        $traza->setIdentificador($entity->getId());
        $traza->setEntity(get_class($entity));
        $responsable=$entity->getTarjeta()->getResponsable();
        if(!$responsable)
            throw new \LogicException('No se pueden hacer gestiones con tarjetas que no tienen responsable');

        $vehiculo=$manager->getRepository('App:Vehiculo')->findOneByResponsable($responsable);

        $chofer=null;
        $indiceconsumo=null;
        $combustibleentanque=null;

        if(!$vehiculo)
            $vehiculo=null;
        else {
            $chofer = $vehiculo->getChofer();
            $indiceconsumo=$vehiculo->getIndconsumo();
            $combustibleentanque=$vehiculo->getLitrosentanque();
        }

        $traza->setVehiculo($vehiculo);
        $traza->setArea($responsable->getArea());
        $traza->setChofer($chofer);
        $traza->setResponsable($responsable);
        $traza->setTarjeta($entity->getTarjeta());
        $traza->setCombustibleentanque($combustibleentanque);
        $traza->setIndiceConsumo($indiceconsumo);
        $traza->setEfectivo($entity->getTarjeta()->getCantefectivo());
        $manager->persist($traza);
        $manager->flush();
    }

    public function findVehiculo($tarjeta_id){
        $manager=$this->getEm()->getManager();
        $consulta=$manager->createQuery('SELECT v FROM App:Vehiculo v JOIN v.responsable r JOIN r.tarjetas t WHERE t.id = :tarjeta_id');
        $consulta->setParameter('tarjeta_id',$tarjeta_id);
        $consulta->setMaxResults(1);
        $vehiculo=$consulta->getResult();
        if(!$vehiculo)
            return;

        return $vehiculo[0];
    }

    public function findTraza($entity_class,$id_object){
        $manager=$this->getEm()->getManager();
        return $manager->getRepository('App:Traza')->findOneBy(['entity'=>$entity_class,'identificador'=>$id_object]);
    }

    public function removeTraza($entity){
        $manager=$this->getEm()->getManager();
        if(null!=$entity) {
            $manager->remove($entity);
            $manager->flush();
        }

    }

    /*
     *Funcionalidad que devuelve la cantidad de efectivo que poseeia una tarjeta en una fecha X, se utiliza para
     * la generacion de chips
     */
    public function cantidadEfectivoTarjetaenFechaX($tarjeta,$fecha=null)
    {
        $manager=$this->getEm()->getManager();
        $tarjeta_obj=$manager->getRepository('App:Tarjeta')->find($tarjeta);
        if(!$tarjeta_obj)
            throw new \Exception('La tarjeta no existe');

        if(null==$fecha)
            return $tarjeta_obj->getCantefectivo();

        $consulta=$manager->createQuery('SELECT t.efectivo FROM App:Traza t WHERE t.tarjeta = :tarjeta AND t.fecha <= :fecha ORDER BY t.fecha DESC');
        $consulta->setParameters(['tarjeta'=>$tarjeta,'fecha'=>$fecha]);
        $consulta->setMaxResults(1);
        $traza=$consulta->getResult();
        return $traza[0]['efectivo'] ?? null;

    }

}