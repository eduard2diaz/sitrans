<?php

namespace App\Controller;

use App\Entity\CierreMesTarjeta;
use App\Form\CierreMesTarjetaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\CierreMesCombustible;
use App\Form\CierreMesCombustibleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tarjeta;

/**
 * @Route("/cierremestarjeta")
 */
class CierreMesTarjetaController extends Controller
{
    /**
     * @Route("/{id}/index", name="cierremestarjeta_index", methods="GET",options={"expose"=true})
     */
    public function index(CierreMesCombustible $cierre, Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $consulta='SELECT c.id , t.codigo as tarjeta, c.fecha, c.restantecombustible as combustiblerestante, c.restanteefectivo as efectivorestante
                        FROM App:CierreMesTarjeta c JOIN c.cierre c1  JOIN c.tarjeta t   WHERE c1.id =:id';
            $cierremestarjetas = $this->getDoctrine()->getManager()
                                                     ->createQuery($consulta)
                                                     ->setParameter('id',$cierre->getId())
                                                     ->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($cierremestarjetas),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $cierremestarjetas,
                ]
                );
        }

        return $this->render('cierremestarjeta/index.html.twig');
    }

    /**
     * @Route("/{id}/new", name="cierremestarjeta_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(CierreMesCombustible $cierre, Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $cierremestarjeta = new CierreMesTarjeta();
        $cierremestarjeta->setCierre($cierre);
        $form = $this->createForm(CierreMesTarjetaType::class, $cierremestarjeta, array('action' => $this->generateUrl('cierremestarjeta_new',array('id'=>$cierre->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $cierremestarjeta->setUsuario($this->getUser());
                $em->persist($cierremestarjeta);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El cierre de tarjeta fue registrado satisfactoriamente",
                    'fecha' => $cierremestarjeta->getFecha(),
                    'combustiblerestante' => $cierremestarjeta->getRestantecombustible(),
                    'efectivorestante' => $cierremestarjeta->getRestanteefectivo(),
                    'tarjeta' => $cierremestarjeta->getTarjeta()->getCodigo(),
                    'id' => $cierremestarjeta->getId(),
                ));
            } else {
                $page = $this->renderView('cierremestarjeta/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('cierremestarjeta/new.html.twig', [
            'cierremestarjeta' => $cierremestarjeta,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="cierremestarjeta_show", methods="GET|POST",options={"expose"=true})
     */
    public function show(Request $request, CierreMesTarjeta $cierremestarjeta): Response
    {
        return $this->render('cierremestarjeta/_show.html.twig',['cierre'=>$cierremestarjeta]);
    }

    /**
     * @Route("/{id}/edit", name="cierremestarjeta_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, CierreMesTarjeta $cierremestarjeta): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(CierreMesTarjetaType::class, $cierremestarjeta, array('action' => $this->generateUrl('cierremestarjeta_edit',array('id'=>$cierremestarjeta->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $cierremestarjeta->setUsuario($this->getUser());
                $em->persist($cierremestarjeta);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El cierre de tarjeta fue actualizado satisfactoriamente",
                    'fecha' => $cierremestarjeta->getFecha()->format('d-m-Y h:i a'),
                    'combustiblerestante' => $cierremestarjeta->getRestantecombustible(),
                    'efectivorestante' => $cierremestarjeta->getRestanteefectivo(),
                    'tarjeta' => $cierremestarjeta->getTarjeta()->getCodigo(),
                    'id' => $cierremestarjeta->getId(),
                ));
            } else {
                $page = $this->renderView('cierremestarjeta/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'cierremestarjeta_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('cierremestarjeta/new.html.twig', [
            'cierremestarjeta' => $cierremestarjeta,
            'form' => $form->createView(),
            'form_id' => 'cierremestarjeta_edit',
            'action' => 'Actualizar',
            'title' => 'Editar cierre de tarjeta',
        ]);
    }

    /**
     * @Route("/{id}/delete", name="cierremestarjeta_delete", options={"expose"=true})
     */
    public function delete(Request $request, CierreMesTarjeta $cierremestarjeta): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($cierremestarjeta);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El cierre de tarjeta fue eliminado satisfactoriamente'));
    }

    /**
     * @Route("/{cierre}/ajax/{tarjeta}", name="cierremestarjeta_ajax", options={"expose"=true})
     */
    public function ajax(Request $request,CierreMesCombustible $cierre, Tarjeta $tarjeta): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $anno=$cierre->getAnno();
        $mes=$cierre->getMes();
        return new JsonResponse($this->get('energia.service')->estadoCombustible($tarjeta->getId(),$anno,$mes));
    }

    /**
     * @Route("/{id}/cierremesautomatico", name="cierremestarjeta_automatico", options={"expose"=true})
     */
    public function cierremesTarjeta(Request $request, CierreMesCombustible $cierre): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em=$this->getDoctrine()->getManager();
        $tarjetas=$em->getRepository('App:Tarjeta')->findBy(['activo'=>true]);
        $mes=$cierre->getMes();
        $anno=$cierre->getAnno();
        foreach ($tarjetas as $value) {
            $existeCierre = $em->getRepository('App:CierreMesTarjeta')->findOneBy(['cierre' => $cierre, 'tarjeta' => $value]);
            if (!$existeCierre){
                $estado = $this->get('energia.service')->estadoCombustible($value->getId(),$anno,$mes);
                $cierretarjeta = new CierreMesTarjeta();
                $cierretarjeta->setTarjeta($value);
                $cierretarjeta->setCierre($cierre);
                $cierretarjeta->setUsuario($this->getUser());
                $cierretarjeta->setFecha(new \DateTime());
                $cierretarjeta->setRestantecombustible($estado['restante']['litros']);
                $cierretarjeta->setRestanteefectivo($estado['restante']['efectivo']);
                $cierretarjeta->setCombustibleconsumido($estado['consumido'][0]['litros']);
                $cierretarjeta->setEfectivoconsumido($estado['consumido'][0]['efectivo']);
                $em->persist($cierretarjeta);
            }
        }
        $em->flush();
        return new JsonResponse(['mensaje'=>'El cierre finalizó exitosamente']);

    }
}
