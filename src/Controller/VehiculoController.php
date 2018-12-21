<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Vehiculo;
use App\Form\VehiculoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/vehiculo")
 */
class VehiculoController extends Controller
{
    /**
     * @Route("/", name="vehiculo_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $vehiculos = $this->getDoctrine()->getManager()->createQuery('SELECT v.id, v.matricula , ch.nombre as chofer, r.nombre as responsable FROM App:Vehiculo v JOIN v.responsable r JOIN v.chofer ch')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($vehiculos),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $vehiculos,
                ]
                );
        }

        return $this->render('vehiculo/index.html.twig');
    }

    /**
     * @Route("/new", name="vehiculo_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $vehiculo = new Vehiculo();
        $form = $this->createForm(VehiculoType::class, $vehiculo, array('action' => $this->generateUrl('vehiculo_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($vehiculo);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El vehículo fue registrado satisfactoriamente",
                    'matricula' => $vehiculo->getMatricula(),
                    'responsable' => $vehiculo->getResponsable()->__toString(),
                    'chofer' => $vehiculo->getChofer()->__toString(),
                    'id' => $vehiculo->getId(),
                ));
            } else {
                $page = $this->renderView('vehiculo/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('vehiculo/new.html.twig', [
            'vehiculo' => $vehiculo,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/show", name="vehiculo_show", methods="GET|POST",options={"expose"=true})
     */
    public function show(Request $request, Vehiculo $vehiculo): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        return $this->render('vehiculo/_show.html.twig',['vehiculo'=>$vehiculo]);
    }

    /**
     * @Route("/{id}/edit", name="vehiculo_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Vehiculo $vehiculo): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(VehiculoType::class, $vehiculo, array('action' => $this->generateUrl('vehiculo_edit',array('id'=>$vehiculo->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($vehiculo);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El vehículo fue actualizado satisfactoriamente",
                    'matricula' => $vehiculo->getMatricula(),
                    'responsable' => $vehiculo->getResponsable()->__toString(),
                    'chofer' => $vehiculo->getChofer()->__toString(),
                    'id' => $vehiculo->getId(),
                ));
            } else {
                $page = $this->renderView('vehiculo/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'vehiculo_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('vehiculo/new.html.twig', [
            'vehiculo' => $vehiculo,
            'form' => $form->createView(),
            'form_id' => 'vehiculo_edit',
            'action' => 'Actualizar',
            'title' => 'Editar vehículo'
        ]);
    }

    /**
     * @Route("/{id}/delete", name="vehiculo_delete", options={"expose"=true})
     */
    public function delete(Request $request, Vehiculo $vehiculo): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($vehiculo);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El vehículo fue eliminado satisfactoriamente'));
    }

    /**
     * @Route("/activo", name="vehiculo_activos", methods="GET",options={"expose"=true})
     */
    public function activos_ajax(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em=$this->getDoctrine()->getManager();
        $vehiculos=$em->getRepository('App:Vehiculo')->findBy(['estado'=>0]);
        $text="";
        foreach ($vehiculos as $value)
            $text.="<option value={$value->getId()}>{$value->getMatricula()}</option>";
        return new Response($text);
    }
}
