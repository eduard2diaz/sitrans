<?php

namespace App\Controller;

use App\Entity\Partida;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Elemento;
use App\Form\ElementoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/elemento")
 */
class ElementoController extends Controller
{
    /**
     * @Route("/", name="elemento_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $elementos = $this->getDoctrine()->getManager()->createQuery('SELECT e.id , e.nombre, e.codigo, p.nombre as partida FROM App:Elemento e JOIN e.partida p JOIN p.cuenta c JOIN c.institucion i WHERE i.id= :id')->setParameter('id',$this->getUser()->getInstitucion()->getId())->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($elementos),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $elementos,
                ]
                );
        }

        return $this->render('elemento/index.html.twig');
    }

    /**
     * @Route("/new", name="elemento_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        
        $elemento = new Elemento();
        $form = $this->createForm(ElementoType::class, $elemento, array('institucion'=>$this->getUser()->getInstitucion()->getId(),'action' => $this->generateUrl('elemento_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($elemento);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El elemento fue registrado satisfactoriamente",
                    'nombre' => $elemento->getNombre(),
                    'codigo' => $elemento->getCodigo(),
                    'partida' => $elemento->getPartida()->getNombre(),
                    'id' => $elemento->getId(),
                ));
            } else {
                $page = $this->renderView('elemento/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('elemento/new.html.twig', [
            'elemento' => $elemento,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="elemento_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Elemento $elemento): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        $this->denyAccessUnlessGranted('EDIT',$elemento);
        $form = $this->createForm(ElementoType::class, $elemento, array('institucion'=>$this->getUser()->getInstitucion()->getId(),'action' => $this->generateUrl('elemento_edit',array('id'=>$elemento->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($elemento);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El elemento fue actualizado satisfactoriamente",
                    'nombre' => $elemento->getNombre(),
                    'codigo' => $elemento->getCodigo(),
                    'partida' => $elemento->getPartida()->getNombre(),
                    'id' => $elemento->getId(),
                ));
            } else {
                $page = $this->renderView('elemento/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'elemento_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('elemento/new.html.twig', [
            'elemento' => $elemento,
            'form' => $form->createView(),
            'form_id' => 'elemento_edit',
            'action' => 'Actualizar',
            'title' => 'Editar elemento',
            'eliminable'=>$this->esEliminable($elemento)
        ]);
    }

    /**
     * @Route("/{id}/delete", name="elemento_delete", options={"expose"=true})
     */
    public function delete(Request $request, Elemento $elemento): Response
    {
        if (!$request->isXmlHttpRequest() || false==$this->esEliminable($elemento))
            throw $this->createAccessDeniedException();
        $this->denyAccessUnlessGranted('DELETE',$elemento);
        $em = $this->getDoctrine()->getManager();
        $em->remove($elemento);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El elemento fue eliminado satisfactoriamente'));
    }

    private function esEliminable(Elemento $elemento){
        $em=$this->getDoctrine()->getManager();
        $subelemento=$em->getRepository('App:Subelemento')->findOneByElemento($elemento);
        return null==$subelemento;
    }

    //Funcion ajax utilizada por otras clases
    /**
     * @Route("/{partida}/searchbypartida",options={"expose"=true}, name="elemento_searchbypartida")
     */
    public function searchbypartida(Request $request, Partida $partida): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em=$this->getDoctrine()->getManager();
        $elementos=$em->getRepository('App:Elemento')->findByPartida($partida);
        $array=array();
        foreach ($elementos as $value)
            $array[]=['id'=>$value->getId(),'nombre'=>$value->getCodigo().' - '.$value->getNombre()];

        return new Response(json_encode($array));
    }
}
