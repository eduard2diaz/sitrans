<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\LecturaReloj;
use App\Form\LecturaRelojType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lecturareloj")
 */
class LecturaRelojController extends Controller
{
    /**
     * @Route("/", name="lecturareloj_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $lecturarelojs = $this->getDoctrine()->getManager()->createQuery('SELECT l.id, r.codigo as reloj,a.nombre as area, l.fecha, l.lectura FROM App:LecturaReloj l JOIN l.reloj r JOIN r.area a')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($lecturarelojs),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $lecturarelojs,
                ]
                );
        }

        return $this->render('lectura_reloj/index.html.twig');
    }

    /**
     * @Route("/new", name="lecturareloj_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        
        $lecturareloj = new LecturaReloj();
        $form = $this->createForm(LecturaRelojType::class, $lecturareloj, array('action' => $this->generateUrl('lecturareloj_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $lecturareloj->setUsuario($this->getUser());
                $em->persist($lecturareloj);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La lectura fue registrada satisfactoriamente",
                    'reloj' => $lecturareloj->getReloj()->getCodigo(),
                    'fecha' => $lecturareloj->getFecha(),
                    'area' => $lecturareloj->getReloj()->getArea()->__toString(),
                    'lectura' => $lecturareloj->getLectura(),
                    'id' => $lecturareloj->getId(),
                ));
            } else {
                $page = $this->renderView('lectura_reloj/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('lectura_reloj/new.html.twig', [
            'lecturareloj' => $lecturareloj,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/show", name="lecturareloj_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, LecturaReloj $lecturareloj): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        
        return $this->render('lectura_reloj/_show.html.twig',['lecturareloj'=>$lecturareloj]);
    }

    /**
     * @Route("/{id}/edit", name="lecturareloj_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, LecturaReloj $lecturareloj): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        
        $form = $this->createForm(LecturaRelojType::class, $lecturareloj, array('action' => $this->generateUrl('lecturareloj_edit',array('id'=>$lecturareloj->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $lecturareloj->setUsuario($this->getUser());
                $em->persist($lecturareloj);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La lectura fue actualizada satisfactoriamente",
                    'reloj' => $lecturareloj->getReloj()->getCodigo(),
                    'fecha' => $lecturareloj->getFecha()->format('d-m-Y h:i a'),
                    'area' => $lecturareloj->getReloj()->getArea()->__toString(),
                    'lectura' => $lecturareloj->getLectura(),
                    'id' => $lecturareloj->getId(),
                ));
            } else {
                $page = $this->renderView('lectura_reloj/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'lecturareloj_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('lectura_reloj/new.html.twig', [
            'lecturareloj' => $lecturareloj,
            'form' => $form->createView(),
            'form_id' => 'lecturareloj_edit',
            'action' => 'Actualizar',
            'title' => 'Editar lectura'
        ]);
    }

    /**
     * @Route("/{id}/delete", name="lecturareloj_delete", options={"expose"=true})
     */
    public function delete(Request $request, LecturaReloj $lecturareloj): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($lecturareloj);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'La lectura fue eliminada satisfactoriamente'));
    }
}
