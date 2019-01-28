<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Somaton;
use App\Form\SomatonType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/somaton")
 */
class SomatonController extends Controller
{
    /**
     * @Route("/", name="somaton_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $somatons = $this->getDoctrine()->getManager()->createQuery('SELECT s.id, v.matricula as vehiculo, s.fechainicio FROM App:Somaton s JOIN s.vehiculo v JOIN s.institucion i WHERE i.id= :institucion')->setParameter('institucion',$this->getUser()->getInstitucion()->getId())->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($somatons),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $somatons,
                ]
                );
        }

        return $this->render('somaton/index.html.twig');
    }

    /**
     * @Route("/new", name="somaton_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $somaton = new Somaton();
        $somaton->setInstitucion($this->getUser()->getInstitucion());
        $form = $this->createForm(SomatonType::class, $somaton, array('institucion'=>$this->getUser()->getInstitucion()->getId(),'action' => $this->generateUrl('somaton_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($somaton);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"Los resultados del somatón fueron registrados satisfactoriamente",
                    'vehiculo' => $somaton->getVehiculo()->getMatricula(),
                    'fechainicio' => $somaton->getFechainicio(),
                    'id' => $somaton->getId(),
                ));
            } else {
                $page = $this->renderView('somaton/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('somaton/new.html.twig', [
            'somaton' => $somaton,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/show", name="somaton_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, Somaton $somaton): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW',$somaton);
        return $this->render('somaton/_show.html.twig',['somaton'=>$somaton]);
    }

    /**
     * @Route("/{id}/delete", name="somaton_delete", options={"expose"=true})
     */
    public function delete(Request $request, Somaton $somaton): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('DELETE',$somaton);
        $em = $this->getDoctrine()->getManager();
        $em->remove($somaton);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'Los resultados del somatón fueron eliminados satisfactoriamente'));
    }
}
