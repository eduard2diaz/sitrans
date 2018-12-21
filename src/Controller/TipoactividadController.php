<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Tipoactividad;
use App\Form\TipoactividadType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tipoactividad")
 */
class TipoactividadController extends Controller
{
    /**
     * @Route("/", name="tipoactividad_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $tipoactividads = $this->getDoctrine()->getManager()->createQuery('SELECT t.id, t.nombre FROM App:Tipoactividad t')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($tipoactividads),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $tipoactividads,
                ]
                );
        }

        return $this->render('tipoactividad/index.html.twig');
    }

    /**
     * @Route("/new", name="tipoactividad_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $tipoactividad = new Tipoactividad();
        $form = $this->createForm(TipoactividadType::class, $tipoactividad, array('action' => $this->generateUrl('tipoactividad_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($tipoactividad);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El tipo de actividad fue registrada satisfactoriamente",
                    'nombre' => $tipoactividad->getNombre(),
                    'id' => $tipoactividad->getId(),
                ));
            } else {
                $page = $this->renderView('tipoactividad/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('tipoactividad/new.html.twig', [
            'tipoactividad' => $tipoactividad,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="tipoactividad_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Tipoactividad $tipoactividad): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(TipoactividadType::class, $tipoactividad, array('action' => $this->generateUrl('tipoactividad_edit',array('id'=>$tipoactividad->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($tipoactividad);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El tipo de actividad fue actualizada satisfactoriamente",
                    'nombre' => $tipoactividad->getNombre(),
                    'id' => $tipoactividad->getId(),
                ));
            } else {
                $page = $this->renderView('tipoactividad/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'tipoactividad_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('tipoactividad/new.html.twig', [
            'tipoactividad' => $tipoactividad,
            'form' => $form->createView(),
            'form_id' => 'tipoactividad_edit',
            'action' => 'Actualizar',
            'title' => 'Editar tipo de actividad'
        ]);
    }

    /**
     * @Route("/{id}/delete", name="tipoactividad_delete", options={"expose"=true})
     */
    public function delete(Request $request, Tipoactividad $tipoactividad): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($tipoactividad);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El tipo de actividad fue eliminada satisfactoriamente'));
    }
}
