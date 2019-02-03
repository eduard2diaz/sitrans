<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Tipovehiculo;
use App\Form\TipovehiculoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tipovehiculo")
 */
class TipovehiculoController extends Controller
{
    /**
     * @Route("/", name="tipovehiculo_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $tipovehiculos = $this->getDoctrine()->getManager()->createQuery('SELECT t.id, t.nombre FROM App:Tipovehiculo t')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($tipovehiculos),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $tipovehiculos,
                ]
                );
        }

        return $this->render('tipovehiculo/index.html.twig');
    }

    /**
     * @Route("/new", name="tipovehiculo_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $tipovehiculo = new Tipovehiculo();
        $form = $this->createForm(TipovehiculoType::class, $tipovehiculo, array('action' => $this->generateUrl('tipovehiculo_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($tipovehiculo);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El tipo de vehículo fue registrado satisfactoriamente",
                    'nombre' => $tipovehiculo->getNombre(),
                    'id' => $tipovehiculo->getId(),
                ));
            } else {
                $page = $this->renderView('tipovehiculo/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('tipovehiculo/new.html.twig', [
            'tipovehiculo' => $tipovehiculo,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/show", name="tipovehiculo_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, Tipovehiculo $tipovehiculo): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        return $this->render('tipovehiculo/_show.html.twig',['vehiculo'=>$tipovehiculo]);
    }

    /**
     * @Route("/{id}/edit", name="tipovehiculo_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Tipovehiculo $tipovehiculo): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(TipovehiculoType::class, $tipovehiculo, array('action' => $this->generateUrl('tipovehiculo_edit',array('id'=>$tipovehiculo->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($tipovehiculo);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El tipo de vehículo fue actualizado satisfactoriamente",
                    'nombre' => $tipovehiculo->getNombre(),
                    'id' => $tipovehiculo->getId(),
                ));
            } else {
                $page = $this->renderView('tipovehiculo/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'tipovehiculo_edit',
                    'action' => 'Actualizar',
                    'tipovehiculo' => $tipovehiculo,
                    'eliminable' => $this->esEliminable($tipovehiculo)
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('tipovehiculo/new.html.twig', [
            'tipovehiculo' => $tipovehiculo,
            'form' => $form->createView(),
            'form_id' => 'tipovehiculo_edit',
            'action' => 'Actualizar',
            'title' => 'Editar tipo de vehículo',
            'eliminable' => $this->esEliminable($tipovehiculo)
        ]);
    }

    /**
     * @Route("/{id}/delete", name="tipovehiculo_delete", options={"expose"=true})
     */
    public function delete(Request $request, Tipovehiculo $tipovehiculo): Response
    {
        if (!$request->isXmlHttpRequest() || false==$this->esEliminable($tipovehiculo))
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($tipovehiculo);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El tipo de vehículo fue eliminado satisfactoriamente'));
    }

    /*
     * Funcion que devuelve un booleano indicando si un tipo de vehiculo es o no eliminable
     */
    private function esEliminable(Tipovehiculo $tipovehiculo){
        $em=$this->getDoctrine()->getManager();
        return null==$em->getRepository('App:Vehiculo')->findOneByTipovehiculo($tipovehiculo);
    }
}
