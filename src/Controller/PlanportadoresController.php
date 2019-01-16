<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Planportadores;
use App\Form\PlanportadoresType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/planportadores")
 */
class PlanportadoresController extends Controller
{
    /**
     * @Route("/", name="planportadores_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $planportadoress = $this->getDoctrine()->getManager()->createQuery('SELECT p.id , p.anno, p.mes FROM App:Planportadores p JOIN p.institucion i WHERE i.id= :id')->setParameter('id',$this->getUser()->getInstitucion()->getId())->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($planportadoress),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $planportadoress,
                ]
                );
        }

        return $this->render('planportadores/index.html.twig');
    }

    /**
     * @Route("/new", name="planportadores_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $planportadores = new Planportadores();
        $planportadores->setInstitucion($this->getUser()->getInstitucion());
        $form = $this->createForm(PlanportadoresType::class, $planportadores, array('action' => $this->generateUrl('planportadores_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($planportadores);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El plan de portadores fue registrado satisfactoriamente",
                    'anno' => $planportadores->getAnno(),
                    'mes' => $planportadores->getMes(),
                    'id' => $planportadores->getId(),
                ));
            } else {
                $page = $this->renderView('planportadores/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('planportadores/new.html.twig', [
            'planportadores' => $planportadores,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="planportadores_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, Planportadores $planportadores): Response
    {
        return $this->render('planportadores/_show.html.twig',['plan'=>$planportadores]);
    }

    /**
     * @Route("/{id}/delete", name="planportadores_delete", options={"expose"=true})
     */
    public function delete(Request $request, Planportadores $planportadores): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($planportadores);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El plan de portadores fue eliminado satisfactoriamente'));
    }
}
