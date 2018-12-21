<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Centrocosto;
use App\Form\CentrocostoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/centrocosto")
 */
class CentrocostoController extends Controller
{
    /**
     * @Route("/", name="centrocosto_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $centrocostos = $this->getDoctrine()->getManager()->createQuery('SELECT sc.id , sc.nombre, sc.codigo, c.nombre as cuenta FROM App:Centrocosto sc JOIN sc.cuenta c')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($centrocostos),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $centrocostos,
                ]
                );
        }

        return $this->render('centrocosto/index.html.twig');
    }

    /**
     * @Route("/new", name="centrocosto_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $centrocosto = new Centrocosto();
        $form = $this->createForm(CentrocostoType::class, $centrocosto, array('action' => $this->generateUrl('centrocosto_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($centrocosto);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El centro de costo fue registrado satisfactoriamente",
                    'nombre' => $centrocosto->getNombre(),
                    'codigo' => $centrocosto->getCodigo(),
                    'cuenta' => $centrocosto->getCuenta()->getNombre(),
                    'id' => $centrocosto->getId(),
                ));
            } else {
                $page = $this->renderView('centrocosto/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('centrocosto/new.html.twig', [
            'centrocosto' => $centrocosto,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="centrocosto_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Centrocosto $centrocosto): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(CentrocostoType::class, $centrocosto, array('action' => $this->generateUrl('centrocosto_edit',array('id'=>$centrocosto->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($centrocosto);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El centro de costo fue actualizado satisfactoriamente",
                    'nombre' => $centrocosto->getNombre(),
                    'codigo' => $centrocosto->getCodigo(),
                    'id' => $centrocosto->getId(),
                ));
            } else {
                $page = $this->renderView('centrocosto/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'centrocosto_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('centrocosto/new.html.twig', [
            'centrocosto' => $centrocosto,
            'form' => $form->createView(),
            'form_id' => 'centrocosto_edit',
            'action' => 'Actualizar',
            'title' => 'Editar centro de costo',
        ]);
    }

    /**
     * @Route("/{id}/delete", name="centrocosto_delete", options={"expose"=true})
     */
    public function delete(Request $request, Centrocosto $centrocosto): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($centrocosto);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El centro de costo fue eliminado satisfactoriamente'));
    }
}
