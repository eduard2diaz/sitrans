<?php
/**
 * Created by PhpStorm.
 * User: eduardo
 * Date: 20/11/18
 * Time: 11:32
 */

namespace App\Tools;

use App\Entity\Chip;
use App\Entity\Hojaruta;
use App\Tools\Util;

class TarjetaService
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

    /*
     * Funcionalidad utilizada en los gestionar de las recargas y ajustes de tarjeta, que comprueba si se ha hecho el
     * cierre mensual de una tarjeta en un determinado mes, anno
     * ESTA FUNCIONALIDAD ES CONSUMIDA POR EL VALIDADOR CierreCombustibleValidator
     */
    public function existeCierreCombustible($anno,$mes,$tarjeta)
    {
        $em = $this->getEm()->getManager();
        $consulta = $em->createQuery('SELECT ct.id FROM App:CierreMesTarjeta ct join ct.tarjeta t JOIN ct.cierre c WHERE t.id= :tarjeta AND c.mes= :mes AND c.anno= :anno');
        $consulta->setParameters(['tarjeta' => $tarjeta, 'mes' => $mes, 'anno' => $anno]);
        $consulta->setMaxResults(1);
        $cierre = $consulta->getResult();
        return empty($cierre) ? null : $cierre[0]['id'];
    }

    /*
    *Funcionalidad que devuelve true/false para saber si luego de una fecha se ha hecho alguna operacion con la tarjeta
     * SE UTILIZA EN EL VALIDADOR EsUltimaOperacionTarjeta el cual compruba que no exista en el momento en que se
     * registra la operacion ninguna operacion posterior a la fecha de la operacion
    */
   public function esUltimaOperacionTarjeta($tarjeta,$fecha){
       $em = $this->getEm()->getManager();
       $entities=['Recargatarjeta','AjusteTarjeta','Chip'];
       $parameters=['tarjeta'=> $tarjeta,'fecha'=>$fecha];

       $mes=$fecha->format('m');
       $anno=$fecha->format('Y');
       if($this->existeCierreCombustible($tarjeta,$mes,$anno))
           return false;

       foreach ($entities as $value) {
           $consulta = $em->createQuery("SELECT r.id FROM App:$value r join r.tarjeta t WHERE t.id= :tarjeta AND r.fecha>=:fecha");
           $consulta->setParameters($parameters);
           $consulta->setMaxResults(1);
           $ajuste = $consulta->getResult();
           if(!empty($ajuste)) {
               return false;
           }
       }
       return true;
   }

   /*
    * Funcionalidad que devuelve el importe o precio de un determinado tipo de combustible en una fecha dada,
    * se utiliza en la captacion de loschips etcetera

   public function importeCombustible($tipocombustible, $fecha)
   {
       $consulta = $this->getEm()->getManager()->createQuery('SELECT pc.importe FROM App:PrecioCombustible pc JOIN pc.tipocombustible tc WHERE tc.id= :id AND pc.fecha<= :fecha ORDER BY pc.fecha DESC');
       $consulta->setParameters(['id' => $tipocombustible, 'fecha' => $fecha]);
       $consulta->setMaxResults(1);
       return $consulta->getResult();
   }


   /*
    *Funcionalidad que devuelve true o false en caso de ser o no posible eliminar una recarga,
    * se utiliza en el gestionar recarga

   public function esPosibleEliminarRecarga($tarjeta,$fecha){
       $mes=$fecha->format('m');
       $anno=$fecha->format('Y');
       return !$this->existeChip($tarjeta,$fecha) && !$this->existeCierreCombustible($tarjeta,$mes,$anno);
   }

   /*
    * Funcionalidad que devuelve si una tarjeta tiene o no una recarga, se utsa para el gestionar de tarjeta

   public function existeRecargaTarjeta($tarjeta){
       $em = $this->getEm()->getManager();
       $consulta = $em->createQuery('SELECT r.id FROM App:Recargatarjeta r join r.tarjeta t WHERE t.id= :tarjeta');
       $consulta->setParameter('tarjeta',$tarjeta);
       $consulta->setMaxResults(1);
       $recarga = $consulta->getResult();
       return empty($recarga) ? null : $recarga[0]['id'];
   }

   /*
    * Funcionalidad que devuelve si una tarjeta tiene o no un ajuste, se utsa para el gestionar de tarjeta

   public function existeAjusteTarjeta($tarjeta){
       $em = $this->getEm()->getManager();
       $consulta = $em->createQuery('SELECT r.id FROM App:AjusteTarjeta r join r.tarjeta t WHERE t.id= :tarjeta');
       $consulta->setParameter('tarjeta',$tarjeta);
       $consulta->setMaxResults(1);
       $ajuste = $consulta->getResult();
       return empty($ajuste) ? null : $ajuste[0]['id'];
   }



   /*
    * Funcionalidad utilizada en los gestionar de las recargas y ajustes de tarjeta, que comprueba si se ha captado
    * algun chip para una tarjeta  a partir de una determinada fecha.

   public function existeChip($tarjeta,$fecha)
   {
       $em = $this->getEm()->getManager();
       $consulta = $em->createQuery('SELECT ch.id FROM App:Chip ch join ch.tarjeta t WHERE t.id= :tarjeta AND ch.fecha>= :fecha');
       $consulta->setParameters(['tarjeta' => $tarjeta, 'fecha' => $fecha]);
       $consulta->setMaxResults(1);
       $cierre = $consulta->getResult();
       return empty($cierre) ? null : $cierre[0]['id'];
   }


   */
}