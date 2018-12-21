<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Mantenimiento;
use App\Form\MantenimientoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/mantenimiento")
 */
class MantenimientoController extends Controller
{
    /**
     * @Route("/", name="mantenimiento_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $mantenimientos = $this->getDoctrine()->getManager()->createQuery('SELECT m.id, v.matricula as vehiculo, m.fechainicio, m.fechafin FROM App:Mantenimiento m JOIN m.vehiculo v')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($mantenimientos),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $mantenimientos,
                ]
                );
        }

        return $this->render('mantenimiento/index.html.twig');
    }

    /**
     * @Route("/new", name="mantenimiento_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $mantenimiento = new Mantenimiento();
        $form = $this->createForm(MantenimientoType::class, $mantenimiento, array('action' => $this->generateUrl('mantenimiento_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($mantenimiento);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El mantenimiento fue registrado satisfactoriamente",
                    'vehiculo' => $mantenimiento->getVehiculo()->getMatricula(),
                    'fechainicio' => $mantenimiento->getFechainicio()->format('d-m-Y h:i a'),
                    'fechafin' => $mantenimiento->getFechafin()->format('d-m-Y h:i a'),
                    'id' => $mantenimiento->getId(),
                ));
            } else {
                $page = $this->renderView('mantenimiento/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('mantenimiento/new.html.twig', [
            'mantenimiento' => $mantenimiento,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/show", name="mantenimiento_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, Mantenimiento $mantenimiento): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        return $this->render('mantenimiento/_show.html.twig',['mantenimiento'=>$mantenimiento]);
    }

    /**
     * @Route("/{id}/edit", name="mantenimiento_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Mantenimiento $mantenimiento): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(MantenimientoType::class, $mantenimiento, array('action' => $this->generateUrl('mantenimiento_edit',array('id'=>$mantenimiento->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($mantenimiento);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El mantenimiento fue actualizado satisfactoriamente",
                    'vehiculo' => $mantenimiento->getVehiculo()->getMatricula(),
                    'fechainicio' => $mantenimiento->getFechainicio()->format('d-m-Y h:i a'),
                    'fechafin' => $mantenimiento->getFechafin()->format('d-m-Y h:i a'),
                    'id' => $mantenimiento->getId(),
                ));
            } else {
                $page = $this->renderView('mantenimiento/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'mantenimiento_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('mantenimiento/new.html.twig', [
            'mantenimiento' => $mantenimiento,
            'form' => $form->createView(),
            'form_id' => 'mantenimiento_edit',
            'action' => 'Actualizar',
            'title' => 'Editar mantenimiento'
        ]);
    }

    /**
     * @Route("/{id}/delete", name="mantenimiento_delete", options={"expose"=true})
     */
    public function delete(Request $request, Mantenimiento $mantenimiento): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($mantenimiento);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El mantenimiento fue eliminado satisfactoriamente'));
    }
}
