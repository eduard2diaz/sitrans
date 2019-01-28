<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Mantenimiento;
use App\Form\MantenimientoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/mantenimiento")
 */
class MantenimientoController extends Controller
{
    /**
     * @Route("/", name="mantenimiento_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $mantenimientos = $this->getDoctrine()->getManager()->createQuery('SELECT m.id, v.matricula as vehiculo, m.fechainicio, m.fechafin FROM App:Mantenimiento m JOIN m.vehiculo v JOIN m.institucion i WHERE i.id= :institucion')->setParameter('institucion',$this->getUser()->getInstitucion()->getId())->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($mantenimientos),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $mantenimientos,
                ]
                );
        }

        return $this->render('mantenimiento/index.html.twig');
    }

    /**
     * @Route("/new", name="mantenimiento_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $mantenimiento = new Mantenimiento();
        $mantenimiento->setInstitucion($this->getUser()->getInstitucion());
        $form = $this->createForm(MantenimientoType::class, $mantenimiento, array('institucion'=>$this->getUser()->getInstitucion()->getId(),'action' => $this->generateUrl('mantenimiento_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($mantenimiento);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El mantenimiento fue registrado satisfactoriamente",
                    'vehiculo' => $mantenimiento->getVehiculo()->getMatricula(),
                    'fechainicio' => $mantenimiento->getFechainicio(),
                    'fechafin' => $mantenimiento->getFechafin(),
                    'id' => $mantenimiento->getId(),
                ));
            } else {
                $page = $this->renderView('mantenimiento/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('mantenimiento/new.html.twig', [
            'mantenimiento' => $mantenimiento,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/show", name="mantenimiento_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, Mantenimiento $mantenimiento): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW',$mantenimiento);
        return $this->render('mantenimiento/_show.html.twig',['mantenimiento'=>$mantenimiento]);
    }

    /**
     * @Route("/{id}/edit", name="mantenimiento_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Mantenimiento $mantenimiento): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('EDIT',$mantenimiento);
        $form = $this->createForm(MantenimientoType::class, $mantenimiento, array('institucion'=>$this->getUser()->getInstitucion()->getId(),'action' => $this->generateUrl('mantenimiento_edit',array('id'=>$mantenimiento->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($mantenimiento);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El mantenimiento fue actualizado satisfactoriamente",
                    'vehiculo' => $mantenimiento->getVehiculo()->getMatricula(),
                    'fechainicio' => $mantenimiento->getFechainicio()->format('d-m-Y h:i a'),
                    'fechafin' => $mantenimiento->getFechafin()->format('d-m-Y h:i a'),
                    'id' => $mantenimiento->getId(),
                ));
            } else {
                $page = $this->renderView('mantenimiento/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'mantenimiento_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('mantenimiento/new.html.twig', [
            'mantenimiento' => $mantenimiento,
            'form' => $form->createView(),
            'form_id' => 'mantenimiento_edit',
            'action' => 'Actualizar',
            'title' => 'Editar mantenimiento'
        ]);
    }

    /**
     * @Route("/{id}/delete", name="mantenimiento_delete", options={"expose"=true})
     */
    public function delete(Request $request, Mantenimiento $mantenimiento): Response
    {
        /*
         *La eliminacion o edicion de un mantenimiento puede ser llevada a cabo en cualquier momento puesto que la misma
         * no repercute en la contabilidad ni en la gestion de los portadores energéticos, sino que es mas un mecanismo
         * para estar informado acerca de la "historia" del vehiculo, ademas no es relevante la fecha de captacion es decir
         * yo puedo registrar un mantenimiento dias o meses antes de que vaya a realizar
         */
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('DELETE',$mantenimiento);
        $em = $this->getDoctrine()->getManager();
        $em->remove($mantenimiento);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El mantenimiento fue eliminado satisfactoriamente'));
    }
}
