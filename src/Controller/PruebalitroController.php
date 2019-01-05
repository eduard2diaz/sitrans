<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Pruebalitro;
use App\Form\PruebalitroType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/pruebalitro")
 */
class PruebalitroController extends Controller
{
    /**
     * @Route("/", name="pruebalitro_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $pruebalitros = $this->getDoctrine()->getManager()->createQuery('SELECT p.id, v.matricula as vehiculo, p.fecha FROM App:Pruebalitro p JOIN p.vehiculo v')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($pruebalitros),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $pruebalitros,
                ]
                );
        }

        return $this->render('pruebalitro/index.html.twig');
    }

    /**
     * @Route("/new", name="pruebalitro_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $pruebalitro = new Pruebalitro();
        $form = $this->createForm(PruebalitroType::class, $pruebalitro, array('action' => $this->generateUrl('pruebalitro_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($pruebalitro);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La prueba fue registrada satisfactoriamente",
                    'vehiculo' => $pruebalitro->getVehiculo()->getMatricula(),
                    'fecha' => $pruebalitro->getFecha(),
                    'id' => $pruebalitro->getId(),
                ));
            } else {
                $page = $this->renderView('pruebalitro/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('pruebalitro/new.html.twig', [
            'pruebalitro' => $pruebalitro,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/show", name="pruebalitro_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, Pruebalitro $pruebalitro): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        return $this->render('pruebalitro/_show.html.twig',['pruebalitro'=>$pruebalitro]);
    }

    /**
     * @Route("/{id}/delete", name="pruebalitro_delete", options={"expose"=true})
     */
    public function delete(Request $request, Pruebalitro $pruebalitro): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($pruebalitro);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'La prueba fue eliminada satisfactoriamente'));
    }
}
