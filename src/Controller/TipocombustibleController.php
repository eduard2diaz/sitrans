<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Tipocombustible;
use App\Form\TipocombustibleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tipocombustible")
 */
class TipocombustibleController extends Controller
{
    /**
     * @Route("/", name="tipocombustible_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $tipocombustibles = $this->getDoctrine()->getManager()->createQuery('SELECT t.id, t.nombre FROM App:Tipocombustible t')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($tipocombustibles),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $tipocombustibles,
                ]
                );
        }

        return $this->render('tipocombustible/index.html.twig');
    }

    /**
     * @Route("/new", name="tipocombustible_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $tipocombustible = new Tipocombustible();
        $form = $this->createForm(TipocombustibleType::class, $tipocombustible, array('action' => $this->generateUrl('tipocombustible_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($tipocombustible);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El tipo de combustible fue registrado satisfactoriamente",
                    'nombre' => $tipocombustible->getNombre(),
                    'id' => $tipocombustible->getId(),
                ));
            } else {
                $page = $this->renderView('tipocombustible/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('tipocombustible/new.html.twig', [
            'tipocombustible' => $tipocombustible,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="tipocombustible_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Tipocombustible $tipocombustible): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(TipocombustibleType::class, $tipocombustible, array('action' => $this->generateUrl('tipocombustible_edit',array('id'=>$tipocombustible->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($tipocombustible);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El tipo de combustible fue actualizado satisfactoriamente",
                    'nombre' => $tipocombustible->getNombre(),
                    'id' => $tipocombustible->getId(),
                ));
            } else {
                $page = $this->renderView('tipocombustible/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'tipocombustible_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('tipocombustible/new.html.twig', [
            'tipocombustible' => $tipocombustible,
            'form' => $form->createView(),
            'form_id' => 'tipocombustible_edit',
            'action' => 'Actualizar',
            'title' => 'Editar tipo de combustible',
            'eliminable'=>$this->esEliminable($tipocombustible)
        ]);
    }

    /**
     * @Route("/{id}/delete", name="tipocombustible_delete", options={"expose"=true})
     */
    public function delete(Request $request, Tipocombustible $tipocombustible): Response
    {
        if (!$request->isXmlHttpRequest() || false==$this->esEliminable($tipocombustible))
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($tipocombustible);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El tipo de combustible fue eliminado satisfactoriamente'));
    }

    /*
     * Esta funcion deuelve un boolean que indica si el tipo de combustible es o no eliminable
     */
    private function esEliminable(Tipocombustible $tipocombustible){
        $em=$this->getDoctrine()->getManager();
        $tarjeta=$em->getRepository('App:Tarjeta')->findOneBy(['tipocombustible'=>$tipocombustible]);
        return null==$tarjeta;
    }
}
