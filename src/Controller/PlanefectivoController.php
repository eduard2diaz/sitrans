<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Planefectivo;
use App\Form\PlanefectivoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/planefectivo")
 */
class PlanefectivoController extends Controller
{
    /**
     * @Route("/", name="planefectivo_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $planefectivos = $this->getDoctrine()->getManager()->createQuery('SELECT p.id , p.anno, p.mes FROM App:Planefectivo p')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($planefectivos),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $planefectivos,
                ]
                );
        }

        return $this->render('planefectivo/index.html.twig');
    }

    /**
     * @Route("/new", name="planefectivo_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        
        $planefectivo = new Planefectivo();
        $form = $this->createForm(PlanefectivoType::class, $planefectivo, array('action' => $this->generateUrl('planefectivo_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($planefectivo);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El plan de efectivo fue registrado satisfactoriamente",
                    'anno' => $planefectivo->getAnno(),
                    'mes' => $planefectivo->getMes(),
                    'id' => $planefectivo->getId(),
                ));
            } else {
                $page = $this->renderView('planefectivo/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('planefectivo/new.html.twig', [
            'planefectivo' => $planefectivo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="planefectivo_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, Planefectivo $planefectivo): Response
    {

        return $this->render('planefectivo/_show.html.twig',['plan'=>$planefectivo]);
    }

    /**
     * @Route("/{id}/delete", name="planefectivo_delete", options={"expose"=true})
     */
    public function delete(Request $request, Planefectivo $planefectivo): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($planefectivo);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El plan de efectivo fue eliminado satisfactoriamente'));
    }
}
