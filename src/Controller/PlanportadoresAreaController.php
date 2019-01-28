<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\PlanportadoresArea;
use App\Entity\Planportadores;
use App\Form\PlanportadoresAreaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/planportadoresarea")
 */
class PlanportadoresAreaController extends Controller
{
    /**
     * @Route("/{id}/list", name="planportadoresarea_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request, Planportadores $planportadores): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW', $planportadores);
        $planportadoresareas = $this->getDoctrine()->getManager()->createQuery('SELECT ppa.id , a.nombre as area, ppa.categoria, ppa.valor FROM App:PlanportadoresArea ppa JOIN ppa.areas a JOIN ppa.planportadores p WHERE p.id= :id')->setParameter('id', $planportadores->getId())->getResult();
        return new JsonResponse(
            $result = [
                'iTotalRecords' => count($planportadoresareas),
                'iTotalDisplayRecords' => 10,
                'sEcho' => 0,
                'sColumns' => '',
                'aaData' => $planportadoresareas,
            ]
        );
    }

    /**
     * @Route("/{id}/new", name="planportadoresarea_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request, Planportadores $planportadores): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW', $planportadores);
        $planportadoresarea = new PlanportadoresArea();
        $planportadoresarea->setPlanportadores($planportadores);
        $planportadoresarea->setUsuario($this->getUser());
        $form = $this->createForm(PlanportadoresAreaType::class, $planportadoresarea, array('action' => $this->generateUrl('planportadoresarea_new', ['id' => $planportadores->getId()])));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($planportadoresarea);
                $em->flush();
                $this->addFlash('success', "El plan de portadores fue registrado satisfactoriamente");
                return new JsonResponse(['estado' => 1]);
            } else {
                $page = $this->renderView('planportadoresarea/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('planportadoresarea/new.html.twig', [
            'planportadoresarea' => $planportadoresarea,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="planportadoresarea_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, PlanportadoresArea $planportadoresarea): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW', $planportadoresarea->getPlanportadores());
        return $this->render('planportadoresarea/_show.html.twig', ['plan' => $planportadoresarea]);
    }

    /**
     * @Route("/{id}/edit", name="planportadoresarea_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, PlanportadoresArea $planportadoresarea): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW', $planportadoresarea->getPlanportadores());
        $form = $this->createForm(PlanportadoresAreaType::class, $planportadoresarea, array('action' => $this->generateUrl('planportadoresarea_edit', array('id' => $planportadoresarea->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $planportadoresarea->setUsuario($this->getUser());
                $em->persist($planportadoresarea);
                $em->flush();
                $this->addFlash('success', "El plan de portadores fue actualizado satisfactoriamente");
                return new JsonResponse(['estado' => 1]);
            } else {
                $page = $this->renderView('planportadoresarea/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'planportadoresarea_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('planportadoresarea/new.html.twig', [
            'planportadoresarea' => $planportadoresarea,
            'form' => $form->createView(),
            'form_id' => 'planportadoresarea_edit',
            'action' => 'Actualizar',
            'title' => 'Editar plan de portadores',
        ]);
    }

    /**
     * @Route("/{id}/delete", name="planportadoresarea_delete", options={"expose"=true})
     */
    public function delete(Request $request, PlanportadoresArea $planportadoresarea): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW', $planportadoresarea->getPlanportadores());
        $em = $this->getDoctrine()->getManager();
        $em->remove($planportadoresarea);
        $em->flush();
        $this->addFlash('success', "El plan de portadores fue eliminado satisfactoriamente");
        return new JsonResponse(['estado' => 1]);
    }
}
