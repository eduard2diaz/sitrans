<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Provincia;
use App\Form\ProvinciaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/provincia")
 */
class ProvinciaController extends Controller
{
    /**
     * @Route("/", name="provincia_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $provincias = $this->getDoctrine()->getManager()->createQuery('SELECT t.id, t.nombre FROM App:Provincia t')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($provincias),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $provincias,
                ]
                );
        }

        return $this->render('provincia/index.html.twig');
    }

    /**
     * @Route("/new", name="provincia_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $provincia = new Provincia();
        $form = $this->createForm(ProvinciaType::class, $provincia, array('action' => $this->generateUrl('provincia_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($provincia);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La provincia fue registrada satisfactoriamente",
                    'nombre' => $provincia->getNombre(),
                    'id' => $provincia->getId(),
                ));
            } else {
                $page = $this->renderView('provincia/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('provincia/new.html.twig', [
            'provincia' => $provincia,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="provincia_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Provincia $provincia): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(ProvinciaType::class, $provincia, array('action' => $this->generateUrl('provincia_edit',array('id'=>$provincia->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($provincia);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La provincia fue actualizada satisfactoriamente",
                    'nombre' => $provincia->getNombre(),
                    'id' => $provincia->getId(),
                ));
            } else {
                $page = $this->renderView('provincia/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'provincia_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('provincia/new.html.twig', [
            'provincia' => $provincia,
            'eliminable'=>$this->esEliminable($provincia),
            'form' => $form->createView(),
            'form_id' => 'provincia_edit',
            'action' => 'Actualizar',
            'title' => 'Editar provincia'
        ]);
    }

    /**
     * @Route("/{id}/show", name="provincia_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, Provincia $provincia): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        return $this->render('provincia/_show.html.twig', [
            'provincia' => $provincia,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="provincia_delete", options={"expose"=true})
     */
    public function delete(Request $request, Provincia $provincia): Response
    {
        if (!$request->isXmlHttpRequest() || false==$this->esEliminable($provincia))
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($provincia);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'La provincia fue eliminada satisfactoriamente'));
    }

    /*
     * Funcionalidad que retorna un booleando indicando si una provincia es eliminable,
     * teniendo en cuenta que no exista ningun municipio asignado a la misma
     */
    private function esEliminable(Provincia $provincia){
        $em=$this->getDoctrine()->getManager();
        $municipio=$em->getRepository('App:Municipio')->findOneByProvincia($provincia);
        return $municipio==null;
    }
}
