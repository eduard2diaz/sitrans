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
use Proxies\__CG__\App\Entity\Vehiculo;

class ReporteService
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
     * Devuelve los kms recorridos por un auto en un periodo señalado a partir de su hoja de ruta
     * SE UTILIZA EN EL REPORTE "Consumo de kms y litros"
    */
    public function kmRecorridosPeriodo($firstday, $lastday, $auto_id = null)
    {
        $conn = $this->getEm()->getConnection();
        //PARA HACER CONSULTAS EN SQL EN CASO DE QUE NO EXISTAN LAS MISMAS PALABRAS RESERVADAS DE SQL EN DQL , PODEMOS UTILIZAR:
        if (!$auto_id) {
            $sql = 'SELECT v.matricula as matricula,  SUM(hr.kmrecorrido) as kms,  SUM(hr.litrosconsumidos) as litros FROM hojaruta hr join vehiculo v on(hr.vehiculo=v.id) WHERE v.estado= 0 AND DATE(hr.fechasalida)>= :finicio AND DATE(hr.fechasalida)<= :ffin GROUP BY v.matricula';
            $parameters = ['finicio' => $firstday->format('Y-m-d'), 'ffin' => $lastday->format('Y-m-d')];
        } else {
            $sql = 'SELECT SUM(hr.kmrecorrido) as kms,  SUM(hr.litrosconsumidos) as litros FROM hojaruta hr join vehiculo v on(hr.vehiculo=v.id) WHERE v.estado= 0 AND v.id= :id AND DATE(hr.fechasalida)>= :finicio AND DATE(hr.fechasalida)<= :ffin';
            $parameters = ['id' => $auto_id, 'finicio' => $firstday->format('Y-m-d'), 'ffin' => $lastday->format('Y-m-d')];
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($parameters);
        $data = $stmt->fetchAll();

        $totalkms = 0;
        $totallitros = 0;

        foreach ($data as $value) {
            $totalkms += $value['kms'];
            $totallitros += $value['litros'];
        }

        return ['data' => $data, 'totalkms' => $totalkms, 'totallitros' => $totallitros];
    }

    /*
     * Devuelve la diferencia entre el consumo a partir del indice de consumo y el que se refleja en la hoja de ruta
     * SE UTILIZA EN EL REPORTE "Diferencia de consumo"
     */
    public function diferenciaConsumo($firstday, $lastday, $auto_id = null)
    {

        $conn = $this->getEm()->getConnection();
        //PARA HACER CONSULTAS EN SQL EN CASO DE QUE NO EXISTAN LAS MISMAS PALABRAS RESERVADAS DE SQL EN DQL , PODEMOS UTILIZAR:
        if (!$auto_id) {
            $sql = 'SELECT v.matricula as matricula,  SUM(hr.litrosconsumidos) as consumoregistrado, SUM(hr.kmrecorrido/t.indice_consumo) as consumoreal  FROM traza t join hojaruta hr on(t.identificador=hr.id) join vehiculo v on(hr.vehiculo=v.id)  WHERE v.estado= 0 AND t.entity = :entity AND DATE(hr.fechasalida)>= :finicio AND DATE(hr.fechasalida)<= :ffin GROUP BY v.matricula';
            $parameters = ['finicio' => $firstday->format('Y-m-d'), 'ffin' => $lastday->format('Y-m-d'), 'entity' => Hojaruta::class];
        } else {
            $sql = 'SELECT v.matricula as matricula,  SUM(hr.litrosconsumidos) as consumoregistrado , SUM(hr.kmrecorrido/t.indice_consumo) as consumoreal FROM traza t join hojaruta hr on(t.identificador=hr.id) join vehiculo v on(hr.vehiculo=v.id) WHERE v.av.estado= 0 AND v.id= :id AND t.entity = :entity AND DATE(hr.fechasalida)>= :finicio AND DATE(hr.fechasalida)<= :ffin';
            $parameters = ['id' => $auto_id, 'finicio' => $firstday->format('Y-m-d'), 'ffin' => $lastday->format('Y-m-d'), 'entity' => Hojaruta::class];
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($parameters);
        $data = $stmt->fetchAll();

        return $data;
    }

    /*
     * Devuelve el remanente actual:
     * es el remanente anterior(LA CANTIDAD DE COMBUSTIBLE EN TANQUE) mas el abastecido
     * SE UTILZIA EN EL REPORTE "Remanente actual"
     */
    public function remanenteActual()
    {
        $em = $this->getEm()->getManager();
        $consulta = $em->createQuery('SELECT v.matricula, v.litrosentanque FROM App:Vehiculo v WHERE v.estado= 0 ');
        return $consulta->getResult();
    }

    /*
     * Devuelve el consumo de kw para un area en un mes-anno determinado
     * SE UTILIZA EN EL REPORTE "Consumo de kilowatts por área"
     */
    public function consumoKw($mes, $anno)
    {
        $em = $this->getEm()->getManager();
        $areas = $em->getRepository('App:Area')->findAll();
        $result = [];
        $firstDay = new \DateTime('01-' . $mes . '-' . $anno);
        $maxDay = Util::maxDays($mes, $anno);
        $lastDay = new \DateTime($maxDay . '-' . $mes . '-' . $anno);
        $conn = $this->getEm()->getConnection();
        $sql = 'SELECT SUM(r.lectura) FROM lectura_reloj r join reloj re on(r.reloj=re.id) join area a on(re.area=a.id) WHERE a.id= :area   AND   DATE(r.fecha)>= :finicio AND DATE(r.fecha)<= :ffin';

        foreach ($areas as $area) {
            $stmt = $conn->prepare($sql);
            $stmt->execute(['area' => $area->getId(), 'finicio' => $firstDay->format('Y-m-d'), 'ffin' => $lastDay->format('Y-m-d')]);
            $lectura = $stmt->fetchAll();
            $lectura = $lectura[0]['sum'] ?? 0;
            $result[] = ['area' => $area->getNombre(), 'consumo' => $lectura];
        }
        return $result;
    }

    /*
     * Devuelve el porciento de desviacion de un vehiculo en un determinado periodo
     * SE UTILIZA EN EL REPORTE "Porciento de desviación"
     */
    public function porcientoDesviacion($firstday, $lastday, $auto_id = null)
    {

        $conn = $this->getEm()->getConnection();
        $result = [];
        //PARA HACER CONSULTAS EN SQL EN CASO DE QUE NO EXISTAN LAS MISMAS PALABRAS RESERVADAS DE SQL EN DQL , PODEMOS UTILIZAR:
        if (!$auto_id) {
            $em = $this->getEm()->getManager();
            $consulta = $em->createQuery('SELECT v.id, v.matricula FROM App:Vehiculo v WHERE v.estado= 0 ');
            $vehiculos = $consulta->getResult();
            foreach ($vehiculos as $value)
                $result[] = ['matricula' => $value['matricula'], 'porciento' => $this->porcientoDesviacionAuto($firstday, $lastday, $value['id'])];
        } else {
            $em = $this->getEm()->getManager();
            $consulta = $em->createQuery('SELECT v FROM App:Vehiculo v WHERE v.id= :id ');
            $consulta->setParameter('id', $auto_id);
            $vehiculo = $consulta->getResult();
            if (!$vehiculo)
                throw new \Exception('Unable find Vehiculo Entity');

            $result[] = ['matricula' => $vehiculo->getMatricula(), 'porciento' => porcientoDesviacionAuto($firstday, $lastday, $auto_id)];
        }

        return $result;
    }

    /*
     * Devuelve el porciento de desviacion de un vehiculo en un determinado periodo
     * SE UTILIZA COMO FUNCION AUXILIAR DEL METODO porcientoDesviacion
     */
    private function porcientoDesviacionAuto($firstday, $lastday, $auto_id)
    {
        $kmconsumo = $this->kmRecorridosPeriodo($firstday, $lastday, $auto_id);
        $vehiculo=$this->getEm()->getManager()->getRepository(Vehiculo::class)->find($auto_id);
        $conn = $this->getEm()->getConnection();
        $sql = 'SELECT SUM(c.litrosextraidos) as litrosextraidos FROM traza t join vehiculo v on(t.vehiculo=v.id), chip c WHERE v.id= :id AND t.entity = :entity AND t.identificador=c.id';
        $parameters = ['id' => $auto_id, 'entity' => Chip::class];
        $stmt = $conn->prepare($sql);
        $stmt->execute($parameters);
        $data = $stmt->fetchAll();

        $consumo=$data[0]['litrosextraidos'];
        if(0==$consumo)
            $consumo=1;
        //inicio
       dump("consumo".$consumo."indi".$vehiculo->getIndconsumo()."kms".$kmconsumo['totalkms']);
        return (1 - $kmconsumo['totalkms'] / $vehiculo->getIndconsumo() / $consumo) * 100;
    }

    /*
     * Devuelve el listado de los vehiculos pendientes a mantenimiento
     * SE UTILIZA EN EL REPORTE "Pendientes a mantenimientos"
     */
    public function pendienteMantenimiento()
    {
        $em = $this->getEm()->getManager();
        $vehiculos = $em->createQuery('SELECT v FROM App:Vehiculo v WHERE v.estado= 0')->getResult();
        $result = [];
        foreach ($vehiculos as $vehiculo) {
            $mantenimiento = $this->ultimoMantenimiento($vehiculo->getId());
            $kms = $this->kmsVehiculo($vehiculo->getId(), $mantenimiento);
            if ($kms >= $vehiculo->getKmsxmantenimiento())
                $result[] = ['matricula' => $vehiculo->getMatricula(),
                    'kmsxrecorridos' => $kms,
                    'diferencia' => $kms - $vehiculo->getKmsxmantenimiento(),
                ];
        }
        return $result;
    }

    /*
     * Devuelve la fecha del ultimo mantenimiento que recibio un vehiculo, NULL si no existe
     * SE UTILIZA COMO FUNCION AUXILIAR DEL METODO pendienteMantenimiento
     */
    private function ultimoMantenimiento($vehiculo_id)
    {
        $em = $this->getEm()->getManager();
        $consulta = $em->createQuery('SELECT m.fechainicio as fecha FROM App:Mantenimiento m join m.vehiculo v WHERE v.id= :id ORDER BY m.fechafin');
        $consulta->setParameters(['id' => $vehiculo_id]);
        $consulta->setMaxResults(1);
        $mantenimiento = $consulta->getResult();
        return $mantenimiento[0]['fecha'] ?? null;
    }

    /*
     * Devuelve los kms recorridos por un vehiculo despues de una determinada fecha,
     * o de manera general si no se indica la fecha
     * SE UTILIZA COMO FUNCION AUXILIAR DEL METODO pendienteMantenimiento
     */
    private function kmsVehiculo($vehiculo_id, $fecha = null)
    {
        $em = $this->getEm()->getManager();
        $parameters = ['id' => $vehiculo_id];
        if (null == $fecha)
            $consulta = $em->createQuery('SELECT SUM(h.kmrecorrido) as kms FROM App:Hojaruta h join h.vehiculo v WHERE v.id= :id ');
        else {
            $consulta = $em->createQuery('SELECT SUM(h.kmrecorrido) as kms FROM App:Hojaruta h join h.vehiculo v WHERE v.id= :id AND h.fechasalida>= :fecha');
            $parameters['fecha'] = $fecha;
        }
        $consulta->setParameters($parameters);
        $hojarutas = $consulta->getResult();

        return $hojarutas[0]['kms'];
    }

    /*
     * Devuelve el consumo de combustibke en un mes por un area determinada
     * SE UTILIZA EN EL REPORTE "Consumo de combustible por área en un mes"
     */
    public function combustibleConsumoMesArea($anno, $mes)
    {
        $em = $this->getEm()->getManager();
        $tipocombustibles = $em->getRepository('App:Tipocombustible')->findAll();
        $areas = $em->getRepository('App:Area')->findAll();

        $tipocombustibles_array = [];
        foreach ($tipocombustibles as $tipocombustible) {
            $areas_array = [];
            foreach ($areas as $area) {
                $consulta = $em->createQuery('SELECT v FROM App:Vehiculo v join v.responsable r join r.area a WHERE a.id= :area AND v.tipocombustible= :tipocombustible AND v.estado= 0');
                $consulta->setParameters(['area' => $area->getId(), 'tipocombustible' => $tipocombustible->getId()]);
                $vehiculos = $consulta->getResult();
                if (!$vehiculos)
                    continue;
                $vehiculos_array = [];
                foreach ($vehiculos as $vehiculo) {
                    $data = $this->kmRecorridosMes($anno, $mes, $vehiculo->getId());
                    $vehiculos_array[] = ['responsable' => $vehiculo->getResponsable(), 'totalkms' => $data['totalkms'], 'totallitros' => $data['totallitros']];
                }
                $areas_array[] = ['area_nombre' => $area->getNombre(), 'vehiculos' => $vehiculos_array];
            }
            if (empty($areas_array))
                continue;
            $tipocombustibles_array[] = ['tipocombustible' => $tipocombustible->getNombre(), 'areas' => $areas_array];
        }
        return $tipocombustibles_array;
    }

    /*
     * Devuelve los kms recorridos por un auto en un periodo señalado a partir de su hoja de ruta
     * SE UTILIZA COMO FUNCION AUXILIAR DEL METODO combustibleConsumoMesArea
    */
    private function kmRecorridosMes($anno, $mes, $auto_id = null)
    {
        $firstday = new \DateTime("01-$mes-$anno");
        $maxday = Util::maxDays($mes, $anno);
        $lastday = new \DateTime("$maxday-$mes-$anno");

        return $this->kmRecorridosPeriodo($firstday, $lastday, $auto_id);
    }

    /*
     * Devuelve la distribucion de combustible en un mes determinado
     * SE UTILIZA EN EL REPORTE "Distribución de combustible por mes"
     */
    public function combustibleDistribucionMes($anno, $mes)
    {
        $em = $this->getEm()->getManager();
        $tipocombustibles = $em->getRepository('App:Tipocombustible')->findAll();

        $fechainicio = new \DateTime("01-$mes-$anno");
        $maxday = Util::maxDays($mes, $anno);
        $fechafin = new \DateTime("$maxday-$mes-$anno");
        $tipocombustibles_array = [];
        foreach ($tipocombustibles as $tipocombustible) {
            $tarjetas = $em->getRepository('App:Tarjeta')->findBy(['tipocombustible' => $tipocombustible]);
            $tarjetas_array = [];

            foreach ($tarjetas as $tarjeta) {
                $consulta = $em->createQuery('SELECT r.nombre,r.apellidos,t.codigo, SUM(hr.litrosconsumidos) as litros, SUM(hr.importe) as importe FROM App:Hojaruta hr join hr.vehiculo v join v.responsable r join r.tarjetas t join t.tipocombustible tc WHERE t.id= :tarjeta AND tc.id= :tipocombustible AND hr.fechasalida>= :finicio AND hr.fechallegada<= :ffin GROUP BY t.codigo,r.nombre,r.apellidos');
                $consulta->setParameters(['tipocombustible' => $tipocombustible->getId(), 'tarjeta' => $tarjeta->getId(), 'finicio' => $fechainicio, 'ffin' => $fechafin]);
                $result = $consulta->getResult();
                if (empty($result))
                    continue;

                $tarjetas_array[] = $result;
            }

            if (empty($tarjetas_array))
                continue;

            $tipocombustibles_array[] = ['tipocombustible' => $tipocombustible->getNombre(), 'tarjetas' => $tarjetas_array];
        }
        return $tipocombustibles_array;
    }

    /*
     *Funcion que devuelve el consumo de combustible de un vehiculo en un mes determinado
     * SE UTILIZA EN EL REPORTE "Consumo de combustible por vehículo en un mes"
     */
    public function combustibleConsumoMesVehiculo($anno, $mes)
    {
        $em = $this->getEm()->getManager();
        $tipocombustibles = $em->getRepository('App:Tipocombustible')->findAll();
        $tipocombustible_array = [];
        foreach ($tipocombustibles as $tipocombustible) {
            $consulta = $em->createQuery('SELECT v FROM App:Vehiculo v join v.responsable r WHERE v.tipocombustible= :tipocombustible AND v.estado= 0');
            $consulta->setParameters(['tipocombustible' => $tipocombustible->getId()]);
            $vehiculos = $consulta->getResult();
            $vehiculos_array = [];
            foreach ($vehiculos as $vehiculo) {
                $responsable = $vehiculo->getResponsable();
                if ($responsable->getTarjetas()->count() != 1)
                    continue;

                $tarjeta = $responsable->getTarjetas()->first();
                $data = $this->kmRecorridosMes($anno, $mes, $vehiculo->getId());
                $vehiculos_array[] = [
                    'matricula' => $vehiculo->getMatricula(), 'chofer' => $vehiculo->getChofer()->__toString(),
                    'litrosentanque' => $vehiculo->getLitrosentanque(),
                    'notarjeta' => $responsable->getTarjetas()->first()->getCodigo(),
                    'km' => $data['totalkms'],
                    'indice' => $vehiculo->getIndconsumo(),
                    'responsable' => $responsable->__toString()
                ];
            }
            if (empty($vehiculos_array))
                continue;
            $tipocombustible_array[] = ['tipocombustible' => $tipocombustible->getNombre(), 'vehiculo' => $vehiculos_array];

        }
        return $tipocombustible_array;
    }

    /*
     * Funcion que devuelve el estado de un portador en un determinado año
     * SE UTILIZA EN LA GRAFICA "Análisis de portadores"
     */
    public function analisisportadores($anno, $categoria)
    {
        $conn = $this->getEm()->getConnection();
        $meses = Util::getMesKey();
        $result = [];
        if ($categoria == 0)
            $real = 'SELECT SUM(ct.combustibleconsumido) as contador  FROM cierre_mes_tarjeta ct join cierre_mes_combustible c on(ct.cierre=c.id) WHERE c.anno= :anno AND c.mes= :mes';
        elseif ($categoria == 1)
            $real = 'SELECT SUM(ca.consumido) as contador  FROM cierremes_area ca join cierremes_kw c on(ca.cierre=c.id) WHERE c.anno= :anno AND c.mes= :mes';
        else
            throw new \LogicException('Seleccione una categoria válida');

        for ($i = 1; $i <= count($meses); $i++) {
            $plansql = 'SELECT SUM(pa.valor) as contador  FROM planportadores_area pa join planportadores p on(pa.planportadores=p.id) WHERE p.anno= :anno AND p.mes= :mes AND pa.categoria= :categoria';
            $parameters = ['anno' => $anno, 'mes' => $i, 'categoria' => $categoria];
            $stmt = $conn->prepare($plansql);
            $stmt->execute($parameters);
            $plandata = $stmt->fetchAll()[0]['contador'];

            $parameters_real = ['anno' => $anno, 'mes' => $i];
            $stmt = $conn->prepare($real);
            $stmt->execute($parameters_real);
            $realdata = $stmt->fetchAll()[0]['contador'];
            $result[] = ['mes' => Util::getMesKey($i), 'plan' => $plandata, 'real' => $realdata];
        }
        return $result;
    }

    /*
     *Funcion que devuelve el consumo mensual de kilowatts en un anno determinado
     * SE UTILIZA PARA EL GRAFICO "Consumo mensual de kilowatts"
     */
    public function consumoMensualKw($anno)
    {
        $conn = $this->getEm()->getConnection();
        $meses = Util::getMesKey();
        $result = [];
        $sql = 'SELECT SUM(l.lectura) as contador  FROM lectura_reloj l WHERE DATE(l.fecha)>= :finicio AND DATE(l.fecha)<= :ffin';
        for ($i = 1; $i <= count($meses); $i++) {
            $lastfirstDay = new \DateTime('01-' . $i . '-' . $anno);
            $maxDay = Util::maxDays($i, $anno);
            $lastlastDay = new \DateTime($maxDay . '-' . $i . '-' . $anno);
            $parameters = ['finicio' => $lastfirstDay->format('Y-m-d'), 'ffin' => $lastlastDay->format('Y-m-d')];
            $stmt = $conn->prepare($sql);
            $stmt->execute($parameters);
            $data = $stmt->fetchAll();
            $result[] = ['mes' => Util::getMesKey($i), 'contador' => $data[0]['contador'] ?? 0];
        }
        return $result;
    }

    /*
     *Funcion que devuelve el resumen de viajes en un periodo determinado
     * SE UTILIZA PARA LA GRAFICA "Resumen de viajes"
     */
    public function resumenViajesPeriodo($firstdate, $lastdate)
    {
        $conn = $this->getEm()->getConnection();
        $sql = 'SELECT COUNT(hr.id) as contador  FROM hojaruta hr WHERE DATE(hr.fechasalida)= :finicio';
        $dates = [];

        while ($firstdate <= $lastdate) {
            $fecha_string = $firstdate->format('Y-m-d');
            $parameters = ['finicio' => $fecha_string];

            $stmt = $conn->prepare($sql);
            $stmt->execute($parameters);
            $data = $stmt->fetchAll();
            $dates[] = ['date' => $fecha_string, 'value' => $data[0]['contador'], 'name' => rand()];
            $firstdate->add(new \DateInterval('P1D'));
        }
        return $dates;
    }

    /*
     * Funcion que devuelve el estado de los vehiculos,
     * SE UTILIZA PARA LA GRAFICA "Estado de los vehículos"
     */
    public function estadoVehiculos()
    {
        $em = $this->getEm()->getManager();
        $estados = ['Activo', 'En mantenimiento o reparación', 'Inactivos temporalmente', 'Pendiente a baja', 'Baja'];
        $result = [];
        for ($i = 0; $i < count($estados); $i++) {
            $consulta = $em->createQuery('SELECT COUNT(v.id) as contador FROM App:Vehiculo v WHERE v.estado= :estado');
            $consulta->setParameter('estado', $i);
            $result[] =
                [
                    'estado' => $estados[$i],
                    'cantidad' => (Integer)$consulta->getResult()[0]['contador'],
                ];
        }
        return $result;
    }


}