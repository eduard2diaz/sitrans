<?php

namespace App\Controller;

use App\Entity\Cuenta;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Subelemento;
use App\Form\SubelementoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/subelemento")
 */
class SubelementoController extends Controller
{
    /**
     * @Route("/", name="subelemento_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $subelementos = $this->getDoctrine()->getManager()->createQuery('SELECT se.id , se.nombre, se.codigo  FROM  App:Subelemento se JOIN se.partida p JOIN p.cuenta c JOIN c.institucion i WHERE i.id= :id')->setParameter('id',$this->getUser()->getInstitucion()->getId())->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($subelementos),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $subelementos,
                ]
                );
        }

        return $this->render('subelemento/index.html.twig');
    }

    /**
     * @Route("/new", name="subelemento_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $subelemento = new Subelemento();
        $form = $this->createForm(SubelementoType::class, $subelemento, array('institucion'=>$this->getUser()->getInstitucion()->getId(),'action' => $this->generateUrl('subelemento_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($subelemento);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El subelemento fue registrado satisfactoriamente",
                    'nombre' => $subelemento->getNombre(),
                    'codigo' => $subelemento->getCodigo(),
                    'id' => $subelemento->getId()
                ));
            } else {
                $page = $this->renderView('subelemento/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('subelemento/new.html.twig', [
            'subelemento' => $subelemento,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/show", name="subelemento_show", methods="GET|POST",options={"expose"=true})
     */
    public function show(Request $request, Subelemento $subelemento): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        $this->denyAccessUnlessGranted('VIEW',$subelemento);
    return $this->render('subelemento/_show.html.twig',['subelemento'=>$subelemento]);
    }


    /**
     * @Route("/{id}/edit", name="subelemento_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Subelemento $subelemento): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        $this->denyAccessUnlessGranted('EDIT',$subelemento);
        $form = $this->createForm(SubelementoType::class, $subelemento, array('institucion'=>$this->getUser()->getInstitucion()->getId(),'action' => $this->generateUrl('subelemento_edit',array('id'=>$subelemento->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($subelemento);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El subelemento fue actualizado satisfactoriamente",
                    'nombre' => $subelemento->getNombre(),
                    'codigo' => $subelemento->getCodigo(),
                    'id' => $subelemento->getId(),
                ));
            } else {
                $page = $this->renderView('subelemento/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'subelemento_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('subelemento/new.html.twig', [
            'subelemento' => $subelemento,
            'form' => $form->createView(),
            'form_id' => 'subelemento_edit',
            'action' => 'Actualizar',
            'title' => 'Editar subelemento',
        ]);
    }

    /**
     * @Route("/{id}/delete", name="subelemento_delete", options={"expose"=true})
     */
    public function delete(Request $request, Subelemento $subelemento): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        $this->denyAccessUnlessGranted('DELETE',$subelemento);
        $em = $this->getDoctrine()->getManager();
        $em->remove($subelemento);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El subelemento fue eliminado satisfactoriamente'));
    }

    //Funcionalidad ajax utilizada por otras clases
    /**
     * @Route("/{cuenta}/searchbycuenta",options={"expose"=true}, name="subelemento_searchbycuenta")
     */
    public function searchbycuenta(Request $request, Cuenta $cuenta): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em=$this->getDoctrine()->getManager();
        $consulta=$em->createQuery('SELECT s FROM App:Subelemento s JOIN s.partida p JOIN p.cuenta c WHERE c.id= :id');
        $consulta->setParameter('id',$cuenta->getId());
        $subelementos=$consulta->getResult();
        $array=array();
        foreach ($subelementos as $value)
            $array[]=['id'=>$value->getId(),'nombre'=>$value->getNombre().' - '.$value->getCodigo()];

        return new Response(json_encode($array));
    }
}
