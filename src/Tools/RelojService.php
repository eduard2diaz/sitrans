<?php
/**
 * Created by PhpStorm.
 * User: eduardo
 * Date: 20/11/18
 * Time: 11:32
 */

namespace App\Tools;

use phpDocumentor\Reflection\Types\Integer;

class RelojService
{
    private $em;

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
     * Funcionalidad que devuelve la ultima operacion que se realizo sobre un area
     * referente a las recargas y lecturas de kilowatts, se usa en el gestionar y validacion de estas entidades
     */
    public function ultimaOperacionKwArea($area,$fecha=null)
    {
        $em = $this->getEm()->getManager();
        $entities = ['RecargaKw','LecturaReloj'];
        $ultimaOperacion=null;

        if(null!=$fecha) {
            $parameters = ['area' => $area, 'fecha' => $fecha];
            $consulta = $em->createQuery("SELECT o FROM App:CierremesArea o JOIN o.area a WHERE a.id= :area AND o.fecha>=:fecha ORDER BY o.fecha DESC");
        }else{
            $parameters = ['area' => $area];
            $consulta = $em->createQuery("SELECT o FROM App:CierremesArea o JOIN o.area a WHERE a.id= :area ORDER BY o.fecha DESC");
        }
        $consulta->setParameters($parameters);
        $consulta->setMaxResults(1);
        $operacion = $consulta->getResult();
        if(!empty($operacion)){
            $operacion=$operacion[0];
            if(null!=$operacion && $operacion->getFecha()>=$fecha){
                $fecha=$operacion->getFecha();
                $ultimaOperacion=$operacion;
            }
        }

        foreach ($entities as $value) {
            if(null!=$fecha) {
                $parameters = ['area' => $area, 'fecha' => $fecha];
                $consulta = $em->createQuery("SELECT o FROM App:$value o join o.reloj r JOIN r.area a WHERE a.id= :area AND o.fecha>=:fecha ORDER BY o.fecha DESC");
            }else{
                $parameters = ['area' => $area];
                $consulta = $em->createQuery("SELECT o FROM App:$value o join o.reloj r JOIN r.area a WHERE a.id= :area ORDER BY o.fecha DESC");
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
     * Devuelve los kw restanes de una determinada area en el mes anterior
     * (SE UTILIZA PARA EL CIERRE DE AREA)
     */
    public function estadoKw($area,$anno,$mes){
        $firstday = new \DateTime("$anno-$mes-01");
        $maxday=Util::maxDays($mes,$anno);
        $lastday = new \DateTime("$anno-$mes-$maxday");
        $mes_anterior=Util::mesAnterior($mes,$anno);
        $consulta=$this->getEm()->getManager()->createQuery('SELECT ca.restante as kwrestante  from App:CierremesArea ca join ca.cierre c join ca.area a WHERE a.id= :id AND c.mes =:mes AND c.anno = :anno');
        $consulta->setParameters(['id'=>$area,'mes'=>(Integer)$mes_anterior['mes'],'anno'=>$mes_anterior['anno']]);
        $consulta->setMaxResults(1);
        $cierreanterior=$consulta->getResult();
        $cierreanterior=$cierreanterior[0]['kwrestante'] ?? 0;



        $conn = $this->getEm()->getConnection();
        //PARA HACER CONSULTAS EN SQL EN CASO DE QUE NO EXISTAN LAS MISMAS PALABRAS RESERVADAS DE SQL EN DQL , PODEMOS UTILIZAR:
        $sql = 'SELECT SUM(r.asignacion) FROM recarga_kw r join reloj re on(r.reloj=re.id) join area a on(re.area=a.id) WHERE a.id= :area  AND DATE(r.fecha)>= :finicio AND DATE(r.fecha)<= :ffin';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['area'=>$area,'finicio' => $firstday->format('Y-m-d'),'ffin'=>$lastday->format('Y-m-d')]);
        $recargas=$stmt->fetchAll();
        $recargas=$recargas[0]['sum'] ?? 0;

        $sql = 'SELECT SUM(r.lectura) FROM lectura_reloj r join reloj re on(r.reloj=re.id) join area a on(re.area=a.id) WHERE a.id= :area   AND   DATE(r.fecha)>= :finicio AND DATE(r.fecha)<= :ffin';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['area'=>$area,'finicio' => $firstday->format('Y-m-d'),'ffin'=>$lastday->format('Y-m-d')]);
        $lectura=$stmt->fetchAll();
        $lectura=(Integer)$lectura[0]['sum'] ?? 0;

        $restante=$cierreanterior+$recargas-$lectura;
        $efectivoconsumido=$this->importeKilowatts($lectura,$lastday);
        $efectivototal=$this->importeKilowatts($restante+$lectura,$lastday);
        $efectivorestante=$efectivototal-$efectivoconsumido;
        return ['consumido'=>$lectura,'restante'=>$restante,'cierreanterior'=>$cierreanterior,'efectivoconsumido'=>(String)$efectivoconsumido,'efectivorestante'=>(String)$efectivorestante];
    }

    /*
     * Funcionalidad que a partir de la fecha y el importe retorna cuanto dinero esto representa
     */
    public function importeKilowatts($importe,$fecha){
        $consulta=$this->getEm()->getManager()->createQuery('SELECT tk FROM App:TarifaKw tk WHERE tk.fecha<= :fecha ORDER BY tk.fecha DESC');
        $consulta->setParameters(['fecha'=>$fecha]);
        $consulta->setMaxResults(1);
        $tarifa=$consulta->getResult();
        if(!$tarifa)
            throw new \LogicException("No existe la tarifa");

        $array = $this->ordenarRangotarifasKw($tarifa[0]->getId());

        $suma=0;
        $total=0;
        foreach ($array as $value){
            $diferencia=$value->getFin()-$total;
            if(($diferencia>=$importe) || (null==$value->getFin())) {
                $suma += $importe * $value->getValor();
                $importe=0;
                break;
            }
            else {
                $suma +=  $diferencia* $value->getValor();
                $importe-=$diferencia;
                $total=$value->getFin();
            }
        }
        return $suma;
    }

    /*
    *Ordena Ascendentemente los rangos de una determinada tarifa  partir del valor inicial, se utiliza en la validacion
    * de las tarifas de kilowatts
    */
    public function ordenarRangotarifasKw($tarifa)
    {
        $consulta = $this->getEm()->getManager()->createQuery('SELECT tk FROM App:TarifaKw tk WHERE tk.id= :id');
        $consulta->setParameters(['id' => $tarifa]);
        $tarifa = $consulta->getSingleResult();
        if (!$tarifa)
            throw new \LogicException("No existe la tarifa");

        $total = $tarifa->getRangoTarifaKws()->count();
        $array = $tarifa->getRangoTarifaKws()->toArray();
        for ($i = 0; $i < $total - 1; $i++)
            for ($j = $i + 1; $j < $total; $j++)
                if ($array[$i]->getInicio() > $array[$j]->getInicio()) {
                    $aux = $array[$i];
                    $array[$i] = $array[$j];
                    $array[$j] = $aux;
                }

        return $array;
    }

}