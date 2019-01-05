<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Institucion;
use App\Form\InstitucionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/institucion")
 */
class InstitucionController extends Controller
{
    /**
     * @Route("/", name="institucion_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $institucions = $this->getDoctrine()->getManager()->createQuery('SELECT i.id, i.nombre, p.nombre as provincia, m.nombre as municipio FROM App:Institucion i JOIN i.provincia p JOIN i.municipio m')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($institucions),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $institucions,
                ]
                );
        }

        return $this->render('institucion/index.html.twig');
    }

    /**
     * @Route("/new", name="institucion_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $institucion = new Institucion();
        $form = $this->createForm(InstitucionType::class, $institucion, array('action' => $this->generateUrl('institucion_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($institucion);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La institución fue registrada satisfactoriamente",
                    'nombre' => $institucion->getNombre(),
                    'provincia' => $institucion->getProvincia()->getNombre(),
                    'municipio' => $institucion->getMunicipio()->getNombre(),
                    'id' => $institucion->getId(),
                ));
            } else {
                $page = $this->renderView('institucion/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('institucion/new.html.twig', [
            'institucion' => $institucion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="institucion_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, Institucion $institucion): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        return $this->render('institucion/_show.html.twig',['institucion'=>$institucion]);
    }

    /**
     * @Route("/{id}/edit", name="institucion_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Institucion $institucion): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(InstitucionType::class, $institucion, array('action' => $this->generateUrl('institucion_edit',array('id'=>$institucion->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($institucion);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La institución fue actualizada satisfactoriamente",
                    'nombre' => $institucion->getNombre(),
                    'provincia' => $institucion->getProvincia()->getNombre(),
                    'municipio' => $institucion->getMunicipio()->getNombre(),
                    'id' => $institucion->getId(),
                ));
            } else {
                $page = $this->renderView('institucion/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'institucion_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('institucion/new.html.twig', [
            'institucion' => $institucion,
            'form' => $form->createView(),
            'form_id' => 'institucion_edit',
            'action' => 'Actualizar',
            'title' => 'Editar institución'
        ]);
    }

    /**
     * @Route("/{id}/delete", name="institucion_delete", options={"expose"=true})
     */
    public function delete(Request $request, Institucion $institucion): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($institucion);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'La institución fue eliminada satisfactoriamente'));
    }
}
