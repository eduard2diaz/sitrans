<?php

namespace App\Controller;

use App\Entity\RangoTarifaKw;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\TarifaKw;
use App\Form\TarifaKwType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tarifakw")
 */
class TarifaKwController extends Controller
{
    /**
     * @Route("/", name="tarifakw_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $tarifakws = $this->getDoctrine()->getManager()->createQuery('SELECT t.id , t.fecha FROM App:TarifaKw t')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($tarifakws),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $tarifakws,
                ]
                );
        }

        return $this->render('tarifakw/index.html.twig');
    }

    /**
     * @Route("/new", name="tarifakw_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $tarifakw = new TarifaKw();
        $form = $this->createForm(TarifaKwType::class, $tarifakw, array('action' => $this->generateUrl('tarifakw_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
            foreach ($form->get('rangoTarifaKws')->getData() as $value){
                $value->setTarifas($tarifakw);
                $em->persist($tarifakw);
            }

                $em->persist($tarifakw);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La tarifa fue registrada satisfactoriamente",
                    'fecha' => $tarifakw->getFecha(),
                    'id' => $tarifakw->getId(),
                ));
            } else {
                $page = $this->renderView('tarifakw/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('tarifakw/new.html.twig', [
            'tarifakw' => $tarifakw,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="tarifakw_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, TarifaKw $tarifakw): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        return $this->render('tarifakw/_show.html.twig',['tarifa'=>$tarifakw]);
    }

    /**
     * @Route("/{id}/edit", name="tarifakw_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, TarifaKw $tarifakw): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(TarifaKwType::class, $tarifakw, array('action' => $this->generateUrl('tarifakw_edit',array('id'=>$tarifakw->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                foreach ($form->get('rangoTarifaKws')->getData() as $value){
                    $value->setTarifas($tarifakw);
                    $em->persist($tarifakw);
                }

                $em->persist($tarifakw);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La tarifa fue actualizada satisfactoriamente",
                    'fecha' => $tarifakw->getFecha()->format('d-m-Y'),
                    'id' => $tarifakw->getId(),
                ));
            } else {
                $page = $this->renderView('tarifakw/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'tarifakw_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('tarifakw/new.html.twig', [
            'tarifakw' => $tarifakw,
            'form' => $form->createView(),
            'form_id' => 'tarifakw_edit',
            'action' => 'Actualizar',
            'title' => 'Editar tarifa de kw',
        ]);
    }


    /**
     * @Route("/{id}/delete", name="tarifakw_delete", options={"expose"=true})
     */
    public function delete(Request $request, TarifaKw $tarifakw): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($tarifakw);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'La tarifa fue eliminada satisfactoriamente'));
    }

    /**
     * @Route("/{id}/deleterango", name="rango_tarifa_kw_delete",options={"expose"=true} )
     */
    public function deleteRango(Request $request, RangoTarifaKw $rangoTarifaKw): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($rangoTarifaKw);
        $em->flush();

        return new JsonResponse(array('mensaje' =>'El rango fue eliminado satisfactoriamente'));
    }

}
