<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Area;
use App\Form\AreaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/area")
 */
class AreaController extends Controller
{
    /**
     * @Route("/", name="area_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $areas = $this->getDoctrine()->getManager()->createQuery('SELECT a.id, a.nombre, a.codigo, cc.nombre as centrocosto FROM App:Area a JOIN a.ccosto cc')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($areas),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $areas,
                ]
                );
        }

        return $this->render('area/index.html.twig');
    }

    /**
     * @Route("/new", name="area_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        
        $area = new Area();
        $form = $this->createForm(AreaType::class, $area, array('action' => $this->generateUrl('area_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($area);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El área fue registrada satisfactoriamente",
                    'nombre' => $area->getNombre(),
                    'codigo' => $area->getCodigo(),
                    'centrocosto' => $area->getCcosto()->getNombre(),
                    'id' => $area->getId(),
                ));
            } else {
                $page = $this->renderView('area/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('area/new.html.twig', [
            'area' => $area,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/show", name="area_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, Area $area): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        
        return $this->render('area/_show.html.twig',['area'=>$area]);
    }
    /**
     * @Route("/{id}/edit", name="area_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Area $area): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        
        $form = $this->createForm(AreaType::class, $area, array('action' => $this->generateUrl('area_edit',array('id'=>$area->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($area);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El área fue actualizada satisfactoriamente",
                    'nombre' => $area->getNombre(),
                    'codigo' => $area->getCodigo(),
                    'centrocosto' => $area->getCcosto()->getNombre(),
                    'id' => $area->getId(),
                ));
            } else {
                $page = $this->renderView('area/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'area_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('area/new.html.twig', [
            'area' => $area,
            'form' => $form->createView(),
            'form_id' => 'area_edit',
            'action' => 'Actualizar',
            'title' => 'Editar área'
        ]);
    }

    /**
     * @Route("/{id}/delete", name="area_delete", options={"expose"=true})
     */
    public function delete(Request $request, Area $area): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($area);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El área fue eliminada satisfactoriamente'));
    }
}
