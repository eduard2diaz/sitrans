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
     * Funcionalidad que devuelve la ultima operacin que se realizo sobre la tarjeta
     */
    public function ultimaOperacionTarjeta($tarjeta,$fecha=null)
    {
        $em = $this->getEm()->getManager();
        $entities = ['CierreMesTarjeta','Recargatarjeta', 'AjusteTarjeta', 'Chip'];
        $ultimaOperacion=null;
        if(null!=$fecha) {
            $parameters = ['tarjeta' => $tarjeta, 'fecha' => $fecha];
            $consulta = $em->createQuery("SELECT h FROM App:Hojaruta h join h.vehiculo v join v.responsable r join r.tarjetas t WHERE t.id= :tarjeta AND h.fechasalida>= :fecha ORDER BY h.fechasalida DESC");
            $max=1;
        }else{
            $parameters = ['tarjeta' => $tarjeta];
            $consulta = $em->createQuery("SELECT h FROM App:Hojaruta h join h.vehiculo v join v.responsable r join r.tarjetas t WHERE t.id= :tarjeta ORDER BY h.fechasalida DESC");
        }
        $consulta->setParameters($parameters);
        $consulta->setMaxResults(1);
        $operacion = $consulta->getResult();

        if(!empty($operacion)) {
            $operacion = $operacion[0];
            if (null != $operacion && $operacion->getFechasalida() >= $fecha) {
                $fecha = $operacion->getFechasalida();
                $ultimaOperacion = $operacion;
            }
        }

        foreach ($entities as $value) {
            if(null!=$fecha) {
                $parameters = ['tarjeta' => $tarjeta, 'fecha' => $fecha];
                $consulta = $em->createQuery("SELECT r FROM App:$value r join r.tarjeta t WHERE t.id= :tarjeta AND r.fecha> :fecha ORDER BY r.fecha DESC");
            }else{
                $parameters = ['tarjeta' => $tarjeta];
                $consulta = $em->createQuery("SELECT r FROM App:$value r join r.tarjeta t WHERE t.id= :tarjeta ORDER BY r.fecha DESC");
            }
            $consulta->setParameters($parameters);
            $consulta->setMaxResults(1);
            $operacion = $consulta->getResult();
            if(empty($operacion))
                continue;
            $operacion=$operacion[0];
            if(null!=$operacion && $operacion->getFecha()>=$fecha){
                $fecha=$operacion->getFecha();
                $ultimaOperacion=$operacion;
            }

        }

        return $ultimaOperacion;
    }

    /*
     * Funcionalidad que devuelve el importe o precio de un determinado tipo de combustible en una fecha dada,
     * se utiliza en la captacion de los chips etcetera
    */
    public function importeCombustible($tipocombustible, $fecha)
    {
        $consulta = $this->getEm()->getManager()->createQuery('SELECT pc.importe FROM App:PrecioCombustible pc JOIN pc.tipocombustible tc WHERE tc.id= :id AND pc.fecha<= :fecha ORDER BY pc.fecha DESC');
        $consulta->setParameters(['id' => $tipocombustible, 'fecha' => $fecha]);
        $consulta->setMaxResults(1);
        return $consulta->getResult();
    }

     /*
     * Devuelve los litros y efectivo consumido y restanes de una determinada tarjeta
     * para un determinado cierre de tarjeta
     */
    public function estadoCombustible($tarjeta,$anno,$mes){
        $firstday = new \DateTime("01-$mes-$anno");
        $maxday=Util::maxDays($mes,$anno);
        $lastday = new \DateTime("$maxday-$mes-$anno");

        $conn = $this->getEm()->getConnection();
        //PARA HACER CONSULTAS EN SQL EN CASO DE QUE NO EXISTAN LAS MISMAS PALABRAS RESERVADAS DE SQL EN DQL , PODEMOS UTILIZAR:
        $recargas=['litros'=>0,'efectivo'=>0];
        $sql = 'SELECT r.cantidadefectivo,r.fecha,tc.id as tipocombustible FROM recargatarjeta r join tarjeta t on(r.tarjeta=t.id) join tipocombustible tc on(t.tipocombustible=tc.id) WHERE t.id= :id AND DATE(r.fecha)>= :finicio AND DATE(r.fecha)<= :ffin';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id'=>$tarjeta,'finicio' => $firstday->format('Y-m-d'),'ffin'=>$lastday->format('Y-m-d')]);
        $recargasList=$stmt->fetchAll();
        foreach ($recargasList as $value){
            $recargas['efectivo']+=$value['cantidadefectivo'];
            $tarifa=$this->importeCombustible($value['tipocombustible'],$value['fecha']);
            $recargas['litros']=$recargas['litros']+$value['cantidadefectivo']/$tarifa[0]['importe'];
        }

        $ajustes=['litros'=>0,'efectivo'=>0];
        $sql = 'SELECT a.cantefectivo,a.fecha,a.tipo, tc.id as tipocombustible FROM ajuste_tarjeta a join tarjeta t on(a.tarjeta=t.id) join tipocombustible tc on(t.tipocombustible=tc.id) WHERE t.id= :id AND DATE(a.fecha)>= :finicio AND DATE(a.fecha)<= :ffin';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id'=>$tarjeta,'finicio' => $firstday->format('Y-m-d'),'ffin'=>$lastday->format('Y-m-d')]);
        $ajustesList=$stmt->fetchAll();
        foreach ($ajustesList as $value){
            if($value['tipo']==1){
                $ajustes['efectivo']+=$value['cantefectivo'];
                $tarifa=$this->importeCombustible($value['tipocombustible'],$value['fecha']);
                $ajustes['litros']=$ajustes['litros']+$value['cantefectivo']/$tarifa[0]['importe'];
            }
            elseif($value['tipo']==0){
                $ajustes['efectivo']-=$value['cantefectivo'];
                $tarifa=$this->importeCombustible($value['tipocombustible'],$value['fecha']);
                $ajustes['litros']=$ajustes['litros']-$value['cantefectivo']/$tarifa[0]['importe'];
            }


        }

        $sql = 'SELECT SUM(c.litrosextraidos) as litros, SUM(c.importe) as efectivo FROM chip c join tarjeta t on(c.tarjeta=t.id) WHERE  t.id= :id AND DATE(c.fecha)>= :finicio AND DATE(c.fecha)<= :ffin';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id'=>$tarjeta,'finicio' => $firstday->format('Y-m-d'),'ffin'=>$lastday->format('Y-m-d')]);
        $consumido=$stmt->fetchAll();
        if(!$consumido[0]['litros']) {
            $consumido[0]['litros'] = 0;
            $consumido[0]['efectivo'] = 0;
        }

        return [
            'consumido'=>$consumido,
            'restante'=>[
                'litros'=>$recargas['litros']+$ajustes['litros']-$consumido[0]['litros'],
                'efectivo'=>$recargas['efectivo']+$ajustes['efectivo']-$consumido[0]['efectivo']
            ]
        ];
    }
}