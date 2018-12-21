<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Cupet;
use App\Form\CupetType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cupet")
 */
class CupetController extends Controller
{
    /**
     * @Route("/", name="cupet_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $cupets = $this->getDoctrine()->getManager()->createQuery('SELECT c.id, c.nombre,  c.enfuncionamiento FROM App:Cupet c')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($cupets),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $cupets,
                ]
                );
        }

        return $this->render('cupet/index.html.twig');
    }

    /**
     * @Route("/new", name="cupet_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $cupet = new Cupet();
        $form = $this->createForm(CupetType::class, $cupet, array('action' => $this->generateUrl('cupet_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($cupet);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El cupet fue registrado satisfactoriamente",
                    'nombre' => $cupet->getNombre(),
                    'enfuncionamiento' => $cupet->getEnfuncionamiento(),
                    'id' => $cupet->getId(),
                ));
            } else {
                $page = $this->renderView('cupet/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('cupet/new.html.twig', [
            'cupet' => $cupet,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="cupet_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, Cupet $cupet): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        return $this->render('cupet/_show.html.twig',['cupet'=>$cupet]);
    }

    /**
     * @Route("/{id}/edit", name="cupet_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Cupet $cupet): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(CupetType::class, $cupet, array('action' => $this->generateUrl('cupet_edit',array('id'=>$cupet->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($cupet);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El cupet fue actualizado satisfactoriamente",
                    'nombre' => $cupet->getNombre(),
                    'enfuncionamiento' => $cupet->getEnfuncionamiento(),
                    'id' => $cupet->getId(),
                ));
            } else {
                $page = $this->renderView('cupet/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'cupet_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('cupet/new.html.twig', [
            'cupet' => $cupet,
            'form' => $form->createView(),
            'form_id' => 'cupet_edit',
            'action' => 'Actualizar',
            'title' => 'Editar cupet'
        ]);
    }

    /**
     * @Route("/{id}/delete", name="cupet_delete", options={"expose"=true})
     */
    public function delete(Request $request, Cupet $cupet): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($cupet);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El cupet fue eliminado satisfactoriamente'));
    }
}
