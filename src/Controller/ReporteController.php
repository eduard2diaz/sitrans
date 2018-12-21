<?php

namespace App\Controller;

use App\Entity\Hojaruta;
use App\Tools\Util;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;

/**
 * @Route("/reporte")
 */
class ReporteController extends Controller
{
    /**
     * @Route("/kmconsumo", name="kmconsumo_report", options={"expose"=true})
     */
    public function kmconsumoReport(Request $request)
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $first_date=new \DateTime($request->request->get('finicio'));
        $last_date=new \DateTime($request->request->get('ffin'));
        $data=$this->get('energia.service')->kmRecorridosPeriodo($first_date, $last_date);

        return new JsonResponse(['html'=>$this->renderView('reporte/html/kmconsumo.html.twig',['data'=>$data]),
            'pdf'=>$this->renderView('reporte/pdf/kmconsumo.html.twig',['data'=>$data])]);
    }

    /**
     * @Route("/diferenciaconsumo", name="diferenciaconsumo_report", options={"expose"=true})
     */
    public function diferenciaconsumoReport(Request $request)
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $first_date=new \DateTime($request->request->get('finicio'));
        $last_date=new \DateTime($request->request->get('ffin'));
        $data=$this->get('energia.service')->diferenciaConsumo($first_date, $last_date);
        return new JsonResponse(['html'=>$this->renderView('reporte/html/diferenciaconsumo.html.twig',['data'=>$data]),
            'pdf'=>$this->renderView('reporte/pdf/diferenciaconsumo.html.twig',['data'=>$data])]);
    }

    /**
     * @Route("/remanenteactual", name="remanenteactual_report")
     */
    public function remanenteactualReport(Request $request)
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $data=$this->get('energia.service')->remanenteActual();
        return new JsonResponse(['html'=>$this->renderView('reporte/html/remanente_actual.html.twig',['data'=>$data]),
            'pdf'=>$this->renderView('reporte/pdf/remanente_actual.html.twig',['data'=>$data])]);
    }

    /**
     * @Route("/porcientodesviacion", name="porcientodesviacion_report", options={"expose"=true})
     */
    public function porcientodesviacionReport(Request $request)
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $first_date=new \DateTime($request->request->get('finicio'));
        $last_date=new \DateTime($request->request->get('ffin'));
        $data=$this->get('energia.service')->porcientoDesviacion($first_date, $last_date);

        return new JsonResponse(['html'=>$this->renderView('reporte/html/porcientodesviacion.html.twig',['data'=>$data]),
            'pdf'=>$this->renderView('reporte/pdf/porcientodesviacion.html.twig',['data'=>$data])]);
    }

    /**
     * @Route("/pendientemantenimiento", name="pendientemantenimiento_report")
     */
    public function pendientemantenimientoReport(Request $request)
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $data=$this->get('energia.service')->pendienteMantenimiento();
        return new JsonResponse(['html'=>$this->renderView('reporte/html/pendiente_mantenimiento.html.twig',['data'=>$data]),
            'pdf'=>$this->renderView('reporte/pdf/pendiente_mantenimiento.html.twig',['data'=>$data])]);
    }

    /**
     * @Route("/kwconsumoarea", name="kwconsumoarea_report", options={"expose"=true})
     */
    public function kwConsumoReport(Request $request)
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $mes=$request->request->get('mes');
        $anno=$request->request->get('anno');
        $consumo=$this->get('energia.service')->consumoKw($mes,$anno);
        return new JsonResponse(['html'=>$this->renderView('reporte/html/kwconsumo.html.twig',['data'=>$consumo]),
            'pdf'=>$this->renderView('reporte/pdf/kwconsumo.html.twig',['data'=>$consumo])]);
    }

    /**
     * @Route("/estadovehiculos", name="estadovehiculos_report")
     */
    public function estadoVehiculosReport(Request $request)
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

       $estados=$this->get('energia.service')->estadoVehiculos();
       return new JsonResponse(
           [
               'view'=>$this->renderView('reporte/html/estado_vehiculo.html.twig'),
               'data'=>json_encode($estados)
           ]);
    }

    /**
     * @Route("/resumenviajes", name="resumenviajes_report", options={"expose"=true})
     */
    public function resumenviajesReport(Request $request)
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $first_date=new \DateTime($request->request->get('finicio'));
        $last_date=new \DateTime($request->request->get('ffin'));
        $data=$this->get('energia.service')->resumenViajesPeriodo($first_date, $last_date);
        return new JsonResponse(
            [
                        'view'=>$this->renderView('reporte/html/resumenviajes.html.twig'),
                        'data'=>$data
            ]);
    }

    /**
     * @Route("/consumomensualkw", name="consumomensualkw_report", options={"expose"=true})
     */
    public function consumomensualkwReport(Request $request)
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $anno=$request->request->get('anno') ?? date('Y');
        $data = $this->get('energia.service')->consumoMensualKw($anno);
        return new JsonResponse(
            [
                'view' => $this->renderView('reporte/html/consumomensualkw.twig'),
                'data' => $data
            ]);
    }

    /**
     * @Route("/analisisportadores", name="analisisportadores_report", options={"expose"=true})
     */
    public function analisisportadoresReport(Request $request)
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $year=$request->request->get('anno');
        $categoria=$request->request->get('categoria');
        $data=$this->get('energia.service')->analisisportadores($year, $categoria);
        return new JsonResponse(
            [
                'view'=>$this->renderView('reporte/html/analisisportadores.html.twig'),
                'data'=>$data
            ]);
    }

    /**
     * @Route("/combustibleconsumomesarea", name="combustibleconsumomesarea_report", options={"expose"=true})
     */
    public function combustibleConsumoMesArea(Request $request)
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $anno=$request->request->get('anno');
        $mes=$request->request->get('mes');
        $tipocombustibles_array=$this->get('energia.service')->combustibleConsumoMesArea($anno,$mes);
        return new JsonResponse(['html'=>$this->renderView('reporte/html/consumocombustiblemesarea.html.twig',['data'=>$tipocombustibles_array,'anno'=>$anno,'mes'=>Util::getMesKey($mes)]),
            'pdf'=>$this->renderView('reporte/pdf/consumocombustiblemesarea.html.twig',['data'=>$tipocombustibles_array,'anno'=>$anno,'mes'=>Util::getMesKey($mes)])]);
    }

    /**
     * @Route("/combustibledistribucionmes", name="combustibledistribucionmes_report", options={"expose"=true})
     */
    public function combustibleDistribucionMes(Request $request)
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $anno=$request->request->get('anno');
        $mes=$request->request->get('mes');
        $tipocombustibles_array=$this->get('energia.service')->combustibleDistribucionMes($anno,$mes);

        return new JsonResponse(['html'=>$this->renderView('reporte/html/distribucioncombustiblemes.html.twig',['data'=>$tipocombustibles_array,'anno'=>$anno,'mes'=>Util::getMesKey($mes)]),
            'pdf'=>$this->renderView('reporte/pdf/distribucioncombustiblemes.html.twig',['data'=>$tipocombustibles_array,'anno'=>$anno,'mes'=>Util::getMesKey($mes)])]);
    }

    /**
     * @Route("/combustibleconsumomesvehiculo", name="combustibleconsumomesvehiculo_report", options={"expose"=true})
     */
    public function combustibleConsumoMesVehiculo(Request $request)
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $anno=$request->request->get('anno');
        $mes=$request->request->get('mes');
        $tipocombustibles_array=$this->get('energia.service')->combustibleConsumoMesVehiculo($anno,$mes);

        return new JsonResponse(['html'=>$this->renderView('reporte/html/consumocombustiblemesvehiculo.html.twig',['data'=>$tipocombustibles_array,'anno'=>$anno,'mes'=>Util::getMesKey($mes)]),
            'pdf'=>$this->renderView('reporte/pdf/consumocombustiblemesvehiculo.html.twig',['data'=>$tipocombustibles_array,'anno'=>$anno,'mes'=>Util::getMesKey($mes)])]);
    }

    /**
     * @Route("/analisishojaruta", name="analisishojaruta_report", options={"expose"=true})
     */
    public function analisisHojaRuta(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $vehiculo=$request->request->get('vehiculo');
        $finicio=new \DateTime($request->request->get('finicio'));
        $ffin=new \DateTime($request->request->get('ffin'));

        $vehiculo=$em->getRepository('App:Vehiculo')->find($vehiculo);
        $tarjeta=$vehiculo->getResponsable()->getTarjetas()->first()->getCodigo();
        $conn = $this->getDoctrine()->getConnection();
        //PARA HACER CONSULTAS EN SQL EN CASO DE QUE NO EXISTAN LAS MISMAS PALABRAS RESERVADAS DE SQL EN DQL , PODEMOS UTILIZAR:
        $sql = 'SELECT hr.id,hr.fechasalida,hr.kmrecorrido,hr.litrosconsumidos FROM hojaruta hr join vehiculo v on(hr.vehiculo=v.id) WHERE v.id= :identificador AND DATE(hr.fechasalida)>= :finicio AND DATE(hr.fechallegada)<= :ffin ORDER BY hr.fechasalida ASC';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['identificador'=>$vehiculo->getId(),'finicio' => $finicio->format('Y-m-d'),'ffin'=>$ffin->format('Y-m-d')]);
        $hojasrutas=$stmt->fetchAll();
        $datos=[];
        foreach ($hojasrutas as $hoja){
            $traza=$this->get('traza.service')->findTraza(Hojaruta::class,$hoja['id']);
            if(!$traza)
                continue;
            $datos[]=[
                'fecha'=>$hoja['fechasalida'],
                'tarjeta'=>$tarjeta,
                'kms'=>$hoja['kmrecorrido'],
                'litros'=>$hoja['litrosconsumidos'],
                'entanque'=>$traza->getCombustibleentanque()
            ];
        }

        return new JsonResponse(['html'=>$this->renderView('reporte/html/analisishojaruta.html.twig',['vehiculo'=>$vehiculo,'datos'=>$datos]),
            'pdf'=>$this->renderView('reporte/pdf/analisishojaruta.html.twig',['vehiculo'=>$vehiculo,'datos'=>$datos])]);
    }

    /**
     * @Route("/exportar", name="exportar_report", options={"expose"=true})
     */
    public function exportar(Request $request)
    {
       /* $html = $this->renderView('MyBundle:Foo:bar.html.twig', array(
            'some'  => $vars
        ));*/
        $html='hola mundo';
        return new PdfResponse(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            'Eduardo.pdf'
        );
    }

}
