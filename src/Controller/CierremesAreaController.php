<?php

namespace App\Controller;

use App\Entity\CierremesArea;
use App\Entity\CierremesKw;
use App\Form\CierremesAreaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Area;

/**
 * @Route("/cierremesarea")
 */
class CierremesAreaController extends Controller
{
    /**
     * @Route("/{id}/index", name="cierremesarea_index", methods="GET",options={"expose"=true})
     */
    public function index(CierremesKw $cierre, Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $consulta='SELECT c.id , a.nombre as area, c.fecha as fecha, c.restante  FROM App:CierremesArea c JOIN c.cierre c1 JOIN c.area a WHERE c1.id =:id';
            $cierremesareas = $this->getDoctrine()->getManager()
                                                     ->createQuery($consulta)
                                                     ->setParameter('id',$cierre->getId())
                                                     ->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($cierremesareas),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $cierremesareas,
                ]
                );
        }

        return $this->render('cierremesarea/index.html.twig');
    }

    /**
     * @Route("/{id}/new", name="cierremesarea_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(CierremesKw $cierre, Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $cierremesarea = new CierremesArea();
        $cierremesarea->setCierre($cierre);
        $cierremesarea->setUsuario($this->getUser());
        $form = $this->createForm(CierremesAreaType::class, $cierremesarea, array('action' => $this->generateUrl('cierremesarea_new',array('id'=>$cierre->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($cierremesarea);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El cierre de área fue registrado satisfactoriamente",
                    'fecha' => $cierremesarea->getFecha(),
                    'restante' => $cierremesarea->getRestante(),
                    'area' => $cierremesarea->getArea()->getNombre(),
                    'id' => $cierremesarea->getId(),
                ));
            } else {
                $page = $this->renderView('cierremesarea/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('cierremesarea/new.html.twig', [
            'cierremesarea' => $cierremesarea,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="cierremesarea_show", methods="GET|POST",options={"expose"=true})
     */
    public function show(Request $request, CierremesArea $cierremesarea): Response
    {
        $this->denyAccessUnlessGranted('VIEW',$cierremesarea->getCierre());
        return $this->render('cierremesarea/_show.html.twig',['cierre'=>$cierremesarea]);
    }

    /**
     * @Route("/{id}/edit", name="cierremesarea_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, CierremesArea $cierremesarea): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW',$cierremesarea->getCierre());
        $form = $this->createForm(CierremesAreaType::class, $cierremesarea, array('action' => $this->generateUrl('cierremesarea_edit',array('id'=>$cierremesarea->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $cierremesarea->setUsuario($this->getUser());
                $em->persist($cierremesarea);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El cierre de área fue actualizado satisfactoriamente",
                    'fecha' => $cierremesarea->getFecha()->format('d-m-Y h:i a'),
                    'restante' => $cierremesarea->getRestante(),
                    'area' => $cierremesarea->getArea()->getNombre(),
                    'id' => $cierremesarea->getId(),
                ));
            } else {
                $page = $this->renderView('cierremesarea/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'cierremesarea_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('cierremesarea/new.html.twig', [
            'cierremesarea' => $cierremesarea,
            'form' => $form->createView(),
            'form_id' => 'cierremesarea_edit',
            'action' => 'Actualizar',
            'title' => 'Editar cierre de área',
        ]);
    }

    /**
     * @Route("/{id}/delete", name="cierremesarea_delete", options={"expose"=true})
     */
    public function delete(Request $request, CierremesArea $cierremesarea): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW',$cierremesarea->getCierre());
        $em = $this->getDoctrine()->getManager();
        $em->remove($cierremesarea);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El cierre de área fue eliminado satisfactoriamente'));
    }

    /**
     * @Route("/{cierre}/ajax/{area}", name="cierremesarea_ajax", options={"expose"=true})
     */
    public function ajax(Request $request, CierremesKw $cierre, Area $area): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $anno=$cierre->getAnno();
        $mes=$cierre->getMes();
        $data=$this->get('reloj.service')->estadoKw($area->getId(),$anno,$mes);
        dump($data);
        dump(new JsonResponse($data));
        return new JsonResponse($data);
    }

    /**
     * @Route("/{id}/cierremesautomatico", name="cierremesarea_automatico", options={"expose"=true})
     */
    public function cierremesAutomatico(Request $request, CierremesKw $cierre): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW',$cierre);
        $em=$this->getDoctrine()->getManager();
        $consulta=$em->createQuery('SELECT a FROM App:Area a JOIN a.ccosto cc JOIN cc.cuenta c JOIN c.institucion i WHERE i.id= :institucion');
        $consulta->setParameter('institucion',$cierre->getInstitucion()->getId());
        $areas=$consulta->getResult();

        $anno=$cierre->getAnno();
        $mes=$cierre->getMes();
        $validator=$this->get('validator');
        foreach ($areas as $value) {
            $existeCierre = $em->getRepository('App:CierremesArea')->findBy(['cierre' => $cierre, 'area' => $value]);
            if (!$existeCierre){
                $estado = $this->get('reloj.service')->estadoKw($value->getId(),$anno,$mes);
                $cierrearea = new CierremesArea();
                $cierrearea->setArea($value);
                $cierrearea->setCierre($cierre);
                $cierrearea->setUsuario($this->getUser());
                $cierrearea->setFecha(new \DateTime());
                $cierrearea->setRestante($estado['restante']);
                $cierrearea->setConsumido($estado['consumido']);
                $cierrearea->setEfectivoconsumido($estado['efectivoconsumido']);
                $cierrearea->setEfectivorestante($estado['efectivorestante']);
                if($validator->validate($cierrearea))
                    $em->persist($cierrearea);
            }
        }
        $em->flush();
        return new JsonResponse(['mensaje'=>'El cierre finalizó exitosamente']);

    }
}
