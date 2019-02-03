<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Partida;
use App\Form\PartidaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/partida")
 */
class PartidaController extends Controller
{
    /**
     * @Route("/", name="partida_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $partidas = $this->getDoctrine()->getManager()->createQuery('SELECT p.id , p.nombre, p.codigo FROM App:Partida p JOIN p.cuenta c JOIN c.institucion i WHERE i.id= :id')->setParameter('id',$this->getUser()->getInstitucion()->getId())->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($partidas),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $partidas,
                ]
                );
        }

        return $this->render('partida/index.html.twig');
    }

    /**
     * @Route("/new", name="partida_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $partida = new Partida();
        $form = $this->createForm(PartidaType::class, $partida, array('institucion'=>$this->getUser()->getInstitucion()->getId(),'action' => $this->generateUrl('partida_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($partida);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La partida fue registrada satisfactoriamente",
                    'nombre' => $partida->getNombre(),
                    'codigo' => $partida->getCodigo(),
                    'id' => $partida->getId(),
                ));
            } else {
                $page = $this->renderView('partida/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('partida/new.html.twig', [
            'partida' => $partida,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/show", name="partida_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, Partida $partida): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        $this->denyAccessUnlessGranted('VIEW',$partida);
        return $this->render('partida/_show.html.twig',['partida'=>$partida]);
    }

    /**
     * @Route("/{id}/edit", name="partida_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Partida $partida): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        $this->denyAccessUnlessGranted('EDIT',$partida);
        $form = $this->createForm(PartidaType::class, $partida, array('institucion'=>$this->getUser()->getInstitucion()->getId(),'action' => $this->generateUrl('partida_edit',array('id'=>$partida->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($partida);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La partida fue actualizada satisfactoriamente",
                    'nombre' => $partida->getNombre(),
                    'codigo' => $partida->getCodigo(),
                    'id' => $partida->getId(),
                ));
            } else {
                $page = $this->renderView('partida/_form.html.twig', array(
                    'partida' => $partida,
                    'form' => $form->createView(),
                    'form_id' => 'partida_edit',
                    'action' => 'Actualizar',
                    'eliminable'=>$this->esEliminable($partida)
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('partida/new.html.twig', [
            'partida' => $partida,
            'form' => $form->createView(),
            'form_id' => 'partida_edit',
            'action' => 'Actualizar',
            'title' => 'Editar partida',
            'eliminable'=>$this->esEliminable($partida)
        ]);
    }

    /**
     * @Route("/{id}/delete", name="partida_delete", options={"expose"=true})
     */
    public function delete(Request $request, Partida $partida): Response
    {
        if (!$request->isXmlHttpRequest() || false==$this->esEliminable($partida))
            throw $this->createAccessDeniedException();
        $this->denyAccessUnlessGranted('DELETE',$partida);
        $em = $this->getDoctrine()->getManager();
        $em->remove($partida);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'La partida fue eliminada satisfactoriamente'));
    }

    private function esEliminable(Partida $partida){
        $em=$this->getDoctrine()->getManager();
        $elemento=$em->getRepository('App:Elemento')->findOneByPartida($partida);
        return null==$elemento;
    }
}
