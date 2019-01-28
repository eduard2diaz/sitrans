<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\TablaDistancia;
use App\Entity\Provincia;
use App\Form\TablaDistanciaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tabladistancia")
 */
class TablaDistanciaController extends Controller
{
    /**
     * @Route("/", name="tabladistancia_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $tabladistancias = $this->getDoctrine()->getManager()->createQuery('SELECT t.id, t.origen, t.destino, t.kms  FROM App:TablaDistancia t')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($tabladistancias),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $tabladistancias,
                ]
                );
        }

        return $this->render('tabladistancia/index.html.twig');
    }

    /**
     * @Route("/new", name="tabladistancia_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        
        $tabladistancia = new TablaDistancia();
        $form = $this->createForm(TablaDistanciaType::class, $tabladistancia, array('action' => $this->generateUrl('tabladistancia_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($tabladistancia);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La tabla de distancia fue registrada satisfactoriamente",
                    'origen' => $tabladistancia->getOrigen(),
                    'destino' => $tabladistancia->getDestino(),
                    'kms' => $tabladistancia->getKms(),
                    'id' => $tabladistancia->getId(),
                ));
            } else {
                $page = $this->renderView('tabladistancia/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('tabladistancia/new.html.twig', [
            'tabladistancia' => $tabladistancia,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="tabladistancia_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, TablaDistancia $tabladistancia): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        
        $form = $this->createForm(TablaDistanciaType::class, $tabladistancia, array('action' => $this->generateUrl('tabladistancia_edit',array('id'=>$tabladistancia->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($tabladistancia);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La tabla de distancia fue actualizada satisfactoriamente",
                    'origen' => $tabladistancia->getOrigen(),
                    'destino' => $tabladistancia->getDestino(),
                    'kms' => $tabladistancia->getKms(),
                ));
            } else {
                $page = $this->renderView('tabladistancia/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'tabladistancia_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('tabladistancia/new.html.twig', [
            'tabladistancia' => $tabladistancia,
            'form' => $form->createView(),
            'form_id' => 'tabladistancia_edit',
            'action' => 'Actualizar',
            'title' => 'Editar tabla de distancia'
        ]);
    }

    /**
     * @Route("/{id}/delete", name="tabladistancia_delete", options={"expose"=true})
     */
    public function delete(Request $request, TablaDistancia $tabladistancia): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($tabladistancia);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'La tabla de distancia fue eliminada satisfactoriamente'));
    }
}
