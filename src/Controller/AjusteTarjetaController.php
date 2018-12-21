<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\AjusteTarjeta;
use App\Form\AjusteTarjetaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ajustetarjeta")
 */
class AjusteTarjetaController extends Controller
{
    /**
     * @Route("/", name="ajustetarjeta_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $ajustetarjetas = $this->getDoctrine()->getManager()->createQuery('SELECT r.id , t.codigo as tarjeta, r.fecha, r.monto as cantidadlitros, r.cantefectivo as cantidadefectivo FROM App:AjusteTarjeta r JOIN r.tarjeta t')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($ajustetarjetas),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $ajustetarjetas,
                ]
                );
        }

        return $this->render('ajustetarjeta/index.html.twig');
    }

    /**
     * @Route("/new", name="ajustetarjeta_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $ajustetarjeta = new AjusteTarjeta();
        $form = $this->createForm(AjusteTarjetaType::class, $ajustetarjeta, array('action' => $this->generateUrl('ajustetarjeta_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $ajustetarjeta->setUsuario($this->getUser());
                $em->persist($ajustetarjeta);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El ajuste fue registrado satisfactoriamente",
                    'fecha' => $ajustetarjeta->getFecha(),
                    'cantidadlitros' => $ajustetarjeta->getMonto(),
                    'cantidadefectivo' => $ajustetarjeta->getCantefectivo(),
                    'tarjeta' => $ajustetarjeta->getTarjeta()->getCodigo(),
                    'id' => $ajustetarjeta->getId(),
                ));
            } else {
                $page = $this->renderView('ajustetarjeta/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('ajustetarjeta/new.html.twig', [
            'ajustetarjeta' => $ajustetarjeta,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="ajustetarjeta_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, AjusteTarjeta $ajustetarjeta): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        return $this->render('ajustetarjeta/_show.html.twig',['ajuste'=>$ajustetarjeta]);
    }

    /**
     * @Route("/{id}/edit", name="ajustetarjeta_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, AjusteTarjeta $ajustetarjeta): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $importeOriginal=$ajustetarjeta->getCantefectivo();
        $cantlitrosOriginal=$ajustetarjeta->getMonto();

        $form = $this->createForm(AjusteTarjetaType::class, $ajustetarjeta, array('action' => $this->generateUrl('ajustetarjeta_edit',array('id'=>$ajustetarjeta->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                    $ajustetarjeta->setUsuario($this->getUser());
                    $diferenciaImporte=$ajustetarjeta->getCantefectivo()-$importeOriginal;
                    $diferenciaLitros= $ajustetarjeta->getMonto()-$cantlitrosOriginal;
                    $multiplo=$ajustetarjeta->getTipo() ==1 ? 1 : -1;
                    if($diferenciaImporte<0){
                        $diferenciaImporte*=-1;
                        $ajustetarjeta->getTarjeta()->setCantefectivo($ajustetarjeta->getTarjeta()->getCantefectivo()-$diferenciaImporte*$multiplo);
                    }elseif($diferenciaImporte>0)
                        $ajustetarjeta->getTarjeta()->setCantefectivo($ajustetarjeta->getTarjeta()->getCantefectivo()+$diferenciaImporte*$multiplo);

                if($diferenciaLitros<0){
                    $diferenciaLitros*=-1;
                    $ajustetarjeta->getTarjeta()->setCantlitros($ajustetarjeta->getTarjeta()->getCantlitros()-$diferenciaLitros*$multiplo);
                }elseif($diferenciaLitros>0)
                    $ajustetarjeta->getTarjeta()->setCantlitros($ajustetarjeta->getTarjeta()->getCantlitros()+$diferenciaLitros*$multiplo);

                $em->persist($ajustetarjeta);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El ajuste fue actualizado satisfactoriamente",
                    'fecha' => $ajustetarjeta->getFecha()->format('d-m-Y h:i a'),
                    'cantidadlitros' => $ajustetarjeta->getMonto(),
                    'cantidadefectivo' => $ajustetarjeta->getCantefectivo(),
                    'tarjeta' => $ajustetarjeta->getTarjeta()->getCodigo(),
                    'id' => $ajustetarjeta->getId(),
                ));
            } else {
                $page = $this->renderView('ajustetarjeta/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'ajustetarjeta_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('ajustetarjeta/new.html.twig', [
            'ajustetarjeta' => $ajustetarjeta,
            'form' => $form->createView(),
            'form_id' => 'ajustetarjeta_edit',
            'action' => 'Actualizar',
            'title' => 'Editar ajuste de tarjeta',
        ]);
    }

    /**
     * @Route("/{id}/delete", name="ajustetarjeta_delete", options={"expose"=true})
     */
    public function delete(Request $request, AjusteTarjeta $ajustetarjeta): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($ajustetarjeta);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El ajuste fue eliminado satisfactoriamente'));
    }
}
