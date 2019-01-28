<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Reparacion;
use App\Form\ReparacionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reparacion")
 */
class ReparacionController extends Controller
{
    /**
     * @Route("/", name="reparacion_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $reparacions = $this->getDoctrine()->getManager()->createQuery('SELECT m.id, v.matricula as vehiculo, m.fechainicio, m.fechafin FROM App:Reparacion m JOIN m.vehiculo v JOIN m.institucion i WHERE i.id= :institucion')->setParameter('institucion',$this->getUser()->getInstitucion()->getId())->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($reparacions),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $reparacions,
                ]
                );
        }

        return $this->render('reparacion/index.html.twig');
    }

    /**
     * @Route("/new", name="reparacion_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $reparacion = new Reparacion();
        $reparacion->setInstitucion($this->getUser()->getInstitucion());
        $form = $this->createForm(ReparacionType::class, $reparacion, array('institucion'=>$this->getUser()->getInstitucion()->getId(),'action' => $this->generateUrl('reparacion_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($reparacion);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La reparación fue registrada satisfactoriamente",
                    'vehiculo' => $reparacion->getVehiculo()->getMatricula(),
                    'fechainicio' => $reparacion->getFechainicio(),
                    'fechafin' => $reparacion->getFechafin(),
                    'id' => $reparacion->getId(),
                ));
            } else {
                $page = $this->renderView('reparacion/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('reparacion/new.html.twig', [
            'reparacion' => $reparacion,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/show", name="reparacion_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, Reparacion $reparacion): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW',$reparacion);
        return $this->render('reparacion/_show.html.twig',['reparacion'=>$reparacion]);
    }

    /**
     * @Route("/{id}/edit", name="reparacion_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Reparacion $reparacion): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('EDIT',$reparacion);
        $form = $this->createForm(ReparacionType::class, $reparacion, array('institucion'=>$this->getUser()->getInstitucion()->getId(),'action' => $this->generateUrl('reparacion_edit',array('id'=>$reparacion->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($reparacion);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La reparación fue actualizada satisfactoriamente",
                    'vehiculo' => $reparacion->getVehiculo()->getMatricula(),
                    'fechainicio' => $reparacion->getFechainicio()->format('d-m-Y h:i a'),
                    'fechafin' => $reparacion->getFechafin()->format('d-m-Y h:i a'),
                    'id' => $reparacion->getId(),
                ));
            } else {
                $page = $this->renderView('reparacion/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'reparacion_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('reparacion/new.html.twig', [
            'reparacion' => $reparacion,
            'form' => $form->createView(),
            'form_id' => 'reparacion_edit',
            'action' => 'Actualizar',
            'title' => 'Editar reparación'
        ]);
    }

    /**
     * @Route("/{id}/delete", name="reparacion_delete", options={"expose"=true})
     */
    public function delete(Request $request, Reparacion $reparacion): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('DELETE',$reparacion);
        $em = $this->getDoctrine()->getManager();
        $em->remove($reparacion);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'La reparación fue eliminada satisfactoriamente'));
    }
}
