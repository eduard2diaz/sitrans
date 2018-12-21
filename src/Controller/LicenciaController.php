<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Licencia;
use App\Form\LicenciaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/licencia")
 */
class LicenciaController extends Controller
{
    /**
     * @Route("/", name="licencia_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $licencias = $this->getDoctrine()->getManager()->createQuery('SELECT l.id, l.nombre FROM App:Licencia l')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($licencias),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $licencias,
                ]
                );
        }

        return $this->render('licencia/index.html.twig');
    }

    /**
     * @Route("/new", name="licencia_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $licencia = new Licencia();
        $form = $this->createForm(LicenciaType::class, $licencia, array('action' => $this->generateUrl('licencia_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($licencia);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La licencia fue registrada satisfactoriamente",
                    'nombre' => $licencia->getNombre(),
                    'id' => $licencia->getId(),
                ));
            } else {
                $page = $this->renderView('licencia/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('licencia/new.html.twig', [
            'licencia' => $licencia,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="licencia_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Licencia $licencia): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(LicenciaType::class, $licencia, array('action' => $this->generateUrl('licencia_edit',array('id'=>$licencia->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($licencia);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La licencia fue actualizada satisfactoriamente",
                    'nombre' => $licencia->getNombre(),
                    'id' => $licencia->getId(),
                ));
            } else {
                $page = $this->renderView('licencia/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'licencia_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('licencia/new.html.twig', [
            'licencia' => $licencia,
            'form' => $form->createView(),
            'form_id' => 'licencia_edit',
            'action' => 'Actualizar',
            'title' => 'Editar licencia'
        ]);
    }

    /**
     * @Route("/{id}/delete", name="licencia_delete", options={"expose"=true})
     */
    public function delete(Request $request, Licencia $licencia): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($licencia);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'La licencia fue eliminada satisfactoriamente'));
    }
}
