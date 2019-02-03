<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Tipotarjeta;
use App\Form\TipotarjetaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tipotarjeta")
 */
class TipotarjetaController extends Controller
{
    /**
     * @Route("/", name="tipotarjeta_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $tipotarjetas = $this->getDoctrine()->getManager()->createQuery('SELECT t.id, t.nombre FROM App:Tipotarjeta t JOIN t.institucion i WHERE i.id= :id')->setParameter('id',$this->getUser()->getInstitucion()->getId())->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($tipotarjetas),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $tipotarjetas,
                ]
                );
        }

        return $this->render('tipotarjeta/index.html.twig');
    }

    /**
     * @Route("/new", name="tipotarjeta_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $tipotarjeta = new Tipotarjeta();
        $tipotarjeta->setInstitucion($this->getUser()->getInstitucion());
        $form = $this->createForm(TipotarjetaType::class, $tipotarjeta, array('action' => $this->generateUrl('tipotarjeta_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($tipotarjeta);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El tipo de tarjeta fue registrado satisfactoriamente",
                    'nombre' => $tipotarjeta->getNombre(),
                    'id' => $tipotarjeta->getId(),
                ));
            } else {
                $page = $this->renderView('tipotarjeta/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('tipotarjeta/new.html.twig', [
            'tipotarjeta' => $tipotarjeta,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="tipotarjeta_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Tipotarjeta $tipotarjeta): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('EDIT',$tipotarjeta);
        $form = $this->createForm(TipotarjetaType::class, $tipotarjeta, array('action' => $this->generateUrl('tipotarjeta_edit',array('id'=>$tipotarjeta->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($tipotarjeta);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El tipo de tarjeta fue actualizado satisfactoriamente",
                    'nombre' => $tipotarjeta->getNombre(),
                    'id' => $tipotarjeta->getId(),
                ));
            } else {
                $page = $this->renderView('tipotarjeta/_form.html.twig', array(
                    'tipotarjeta' => $tipotarjeta,
                    'form' => $form->createView(),
                    'form_id' => 'tipotarjeta_edit',
                    'action' => 'Actualizar',
                    'eliminable'=>$this->esEliminable($tipotarjeta)
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('tipotarjeta/new.html.twig', [
            'tipotarjeta' => $tipotarjeta,
            'form' => $form->createView(),
            'form_id' => 'tipotarjeta_edit',
            'action' => 'Actualizar',
            'title' => 'Editar tipo de tarjeta',
            'eliminable'=>$this->esEliminable($tipotarjeta)
        ]);
    }

    /**
     * @Route("/{id}/delete", name="tipotarjeta_delete", options={"expose"=true})
     */
    public function delete(Request $request, Tipotarjeta $tipotarjeta): Response
    {
        if (!$request->isXmlHttpRequest() || false==$this->esEliminable($tipotarjeta))
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('DELETE',$tipotarjeta);
        $em = $this->getDoctrine()->getManager();
        $em->remove($tipotarjeta);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El tipo de tarjeta fue eliminado satisfactoriamente'));
    }

    private function esEliminable(Tipotarjeta $tipotarjeta){
        $em=$this->getDoctrine()->getManager();
        $tarjeta=$em->getRepository('App:Tarjeta')->findOneBy(['tipotarjeta'=>$tipotarjeta]);
        return $tarjeta==null;
    }
}
