<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\PlanefectivoCuenta;
use App\Entity\Planefectivo;
use App\Form\PlanefectivoCuentaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/planefectivocuenta")
 */
class PlanefectivoCuentaController extends Controller
{
    /**
     * @Route("/{id}/list", name="planefectivocuenta_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request,Planefectivo $planefectivo): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

            $planefectivocuentas = $this->getDoctrine()->getManager()->createQuery('SELECT pe.id , cu.nombre as cuenta, cc.nombre as centrocosto, se.nombre as subelemento FROM App:PlanefectivoCuenta pe JOIN pe.planefectivo p JOIN pe.centrocosto cc JOIN pe.subelemento se JOIN pe.cuenta cu WHERE p.id= :id')->setParameter('id',$planefectivo->getId())->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($planefectivocuentas),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $planefectivocuentas,
                ]
                );
    }

    /**
     * @Route("/{id}/new", name="planefectivocuenta_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request,Planefectivo $planefectivo): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        
        $planefectivocuenta = new PlanefectivoCuenta();
        $planefectivocuenta->setPlanefectivo($planefectivo);
        $form = $this->createForm(PlanefectivoCuentaType::class, $planefectivocuenta, array('action' => $this->generateUrl('planefectivocuenta_new',['id'=>$planefectivo->getId()])));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $planefectivocuenta->setUsuario($this->getUser());
                $em->persist($planefectivocuenta);
                $em->flush();
                $this->addFlash('success',"El plan de efectivo fue registrado satisfactoriamente");
                return new JsonResponse(['estado' =>1]);
            } else {
                $page = $this->renderView('planefectivocuenta/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('planefectivocuenta/new.html.twig', [
            'planefectivocuenta' => $planefectivocuenta,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="planefectivocuenta_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, PlanefectivoCuenta $planefectivocuenta): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        
        return $this->render('planefectivocuenta/_show.html.twig',['plan'=>$planefectivocuenta]);
    }

    /**
     * @Route("/{id}/edit", name="planefectivocuenta_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, PlanefectivoCuenta $planefectivocuenta): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        
        $form = $this->createForm(PlanefectivoCuentaType::class, $planefectivocuenta, array('action' => $this->generateUrl('planefectivocuenta_edit',array('id'=>$planefectivocuenta->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $planefectivocuenta->setUsuario($this->getUser());
                $em->persist($planefectivocuenta);
                $em->flush();
                $this->addFlash('success',"El plan de efectivo fue actualizado satisfactoriamente");
                return new JsonResponse(['estado' =>1]);
            } else {
                $page = $this->renderView('planefectivocuenta/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'planefectivocuenta_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('planefectivocuenta/new.html.twig', [
            'planefectivocuenta' => $planefectivocuenta,
            'form' => $form->createView(),
            'form_id' => 'planefectivocuenta_edit',
            'action' => 'Actualizar',
            'title' => 'Editar plan de efectivo',
        ]);
    }

    /**
     * @Route("/{id}/delete", name="planefectivocuenta_delete", options={"expose"=true})
     */
    public function delete(Request $request, PlanefectivoCuenta $planefectivocuenta): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($planefectivocuenta);
        $em->flush();
        $this->addFlash('success',"El plan de efectivo fue eliminado satisfactoriamente");
        return new JsonResponse(['estado' =>1]);
    }
}
