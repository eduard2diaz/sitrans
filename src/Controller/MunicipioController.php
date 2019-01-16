<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Municipio;
use App\Entity\Provincia;
use App\Form\MunicipioType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/municipio")
 */
class MunicipioController extends Controller
{
    /**
     * @Route("/", name="municipio_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $municipios = $this->getDoctrine()->getManager()->createQuery('SELECT a.id, a.nombre, p.nombre as provincia FROM App:Municipio a JOIN a.provincia p')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($municipios),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $municipios,
                ]
                );
        }

        return $this->render('municipio/index.html.twig');
    }

    /**
     * @Route("/new", name="municipio_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        
        $municipio = new Municipio();
        $form = $this->createForm(MunicipioType::class, $municipio, array('action' => $this->generateUrl('municipio_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($municipio);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El municipio fue registrado satisfactoriamente",
                    'nombre' => $municipio->getNombre(),
                    'provincia' => $municipio->getProvincia()->getNombre(),
                    'id' => $municipio->getId(),
                ));
            } else {
                $page = $this->renderView('municipio/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('municipio/new.html.twig', [
            'municipio' => $municipio,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="municipio_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Municipio $municipio): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        
        $form = $this->createForm(MunicipioType::class, $municipio, array('action' => $this->generateUrl('municipio_edit',array('id'=>$municipio->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($municipio);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El municipio fue actualizado satisfactoriamente",
                    'nombre' => $municipio->getNombre(),
                    'provincia' => $municipio->getProvincia()->getNombre(),
                    'id' => $municipio->getId(),
                ));
            } else {
                $page = $this->renderView('municipio/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'municipio_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('municipio/new.html.twig', [
            'municipio' => $municipio,
            'form' => $form->createView(),
            'form_id' => 'municipio_edit',
            'action' => 'Actualizar',
            'title' => 'Editar municipio'
        ]);
    }

    /**
     * @Route("/{id}/delete", name="municipio_delete", options={"expose"=true})
     */
    public function delete(Request $request, Municipio $municipio): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($municipio);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El municipio fue eliminado satisfactoriamente'));
    }

    //Funcionalidad ajax usada por otras clases
    /**
     * @Route("/{id}/findbyprovincia", name="municipio_findbyprovincia", options={"expose"=true})
     */
    public function findByProvincia(Request $request, Provincia $provincia)
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $municipios=[];
        foreach ($provincia->getMunicipios() as $value){
            $municipios[]=['id'=>$value->getId(),'nombre'=>$value->__toString()];
        }

        return new JsonResponse($municipios);
    }
}
