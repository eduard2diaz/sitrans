<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Recargatarjeta;
use App\Form\RecargatarjetaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/recargatarjeta")
 */
class RecargatarjetaController extends Controller
{
    /**
     * @Route("/", name="recargatarjeta_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $recargatarjetas = $this->getDoctrine()->getManager()->createQuery('SELECT r.id , t.codigo as tarjeta, r.fecha, r.cantidadlitros, r.cantidadefectivo FROM App:Recargatarjeta r JOIN r.tarjeta t')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($recargatarjetas),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $recargatarjetas,
                ]
                );
        }

        return $this->render('recargatarjeta/index.html.twig');
    }

    /**
     * @Route("/new", name="recargatarjeta_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        
        $recargatarjeta = new Recargatarjeta();
        $form = $this->createForm(RecargatarjetaType::class, $recargatarjeta, array('action' => $this->generateUrl('recargatarjeta_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $recargatarjeta->setUsuario($this->getUser());
                $em->persist($recargatarjeta);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La recarga fue registrada satisfactoriamente",
                    'fecha' => $recargatarjeta->getFecha(),
                    'cantidadlitros' => $recargatarjeta->getCantidadlitros(),
                    'cantidadefectivo' => $recargatarjeta->getCantidadefectivo(),
                    'tarjeta' => $recargatarjeta->getTarjeta()->getCodigo(),
                    'id' => $recargatarjeta->getId(),
                ));
            } else {
                $page = $this->renderView('recargatarjeta/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('recargatarjeta/new.html.twig', [
            'recargatarjeta' => $recargatarjeta,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="recargatarjeta_show", methods="GET|POST",options={"expose"=true})
     */
    public function show(Request $request, Recargatarjeta $recargatarjeta): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        return $this->render('recargatarjeta/_show.html.twig',['recarga'=>$recargatarjeta]);
    }

    /**
     * @Route("/{id}/edit", name="recargatarjeta_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Recargatarjeta $recargatarjeta): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        
        $importeOriginal=$recargatarjeta->getCantidadefectivo();
        $cantlitrosOriginal=$recargatarjeta->getCantidadlitros();

        $form = $this->createForm(RecargatarjetaType::class, $recargatarjeta, array('action' => $this->generateUrl('recargatarjeta_edit',array('id'=>$recargatarjeta->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                    $recargatarjeta->setUsuario($this->getUser());
                    $diferenciaImporte=$recargatarjeta->getCantidadefectivo()-$importeOriginal;
                    $diferenciaLitros= $recargatarjeta->getCantidadlitros()-$cantlitrosOriginal;
                    if($diferenciaImporte<0){
                        $diferenciaImporte*=-1;
                        $recargatarjeta->getTarjeta()->setCantefectivo($recargatarjeta->getTarjeta()->getCantefectivo()-$diferenciaImporte);
                    }elseif($diferenciaImporte>0)
                        $recargatarjeta->getTarjeta()->setCantefectivo($recargatarjeta->getTarjeta()->getCantefectivo()+$diferenciaImporte);

                if($diferenciaLitros<0){
                    $diferenciaLitros*=-1;
                    $recargatarjeta->getTarjeta()->setCantlitros($recargatarjeta->getTarjeta()->getCantlitros()-$diferenciaLitros);
                }elseif($diferenciaLitros>0)
                    $recargatarjeta->getTarjeta()->setCantlitros($recargatarjeta->getTarjeta()->getCantlitros()+$diferenciaLitros);

                $em->persist($recargatarjeta);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La recarga fue actualizada satisfactoriamente",
                    'fecha' => $recargatarjeta->getFecha()->format('d-m-Y h:i a'),
                    'cantidadlitros' => $recargatarjeta->getCantidadlitros(),
                    'cantidadefectivo' => $recargatarjeta->getCantidadefectivo(),
                    'tarjeta' => $recargatarjeta->getTarjeta()->getCodigo(),
                    'id' => $recargatarjeta->getId(),
                ));
            } else {
                $page = $this->renderView('recargatarjeta/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'recargatarjeta_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('recargatarjeta/new.html.twig', [
            'recargatarjeta' => $recargatarjeta,
            'form' => $form->createView(),
            'form_id' => 'recargatarjeta_edit',
            'action' => 'Actualizar',
            'title' => 'Editar recarga de tarjeta',
        ]);
    }

    /**
     * @Route("/{id}/delete", name="recargatarjeta_delete", options={"expose"=true})
     */
    public function delete(Request $request, Recargatarjeta $recargatarjeta): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($recargatarjeta);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'La recarga fue eliminada satisfactoriamente'));
    }
}
