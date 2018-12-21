<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Tarjeta;
use App\Form\TarjetaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\QueryBuilder;

/**
 * @Route("/tarjeta")
 */
class TarjetaController extends Controller
{
    /**
     * @Route("/", name="tarjeta_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $tarjetas = $this->getDoctrine()->getManager()->createQuery('SELECT t.id, t.codigo,tt.nombre as tipotarjeta, tc.nombre as tipocombustible,t.activo FROM App:Tarjeta t JOIN t.tipotarjeta tt JOIN t.tipocombustible tc')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($tarjetas),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $tarjetas,
                ]
                );
        }

        return $this->render('tarjeta/index.html.twig');
    }

    /**
     * @Route("/new", name="tarjeta_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $tarjeta = new Tarjeta();
        $form = $this->createForm(TarjetaType::class, $tarjeta, array('action' => $this->generateUrl('tarjeta_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($tarjeta);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La tarjeta fue registrada satisfactoriamente",
                    'codigo' => $tarjeta->getCodigo(),
                    'tipotarjeta' => $tarjeta->getTipotarjeta()->getNombre(),
                    'tipocombustible' => $tarjeta->getTipocombustible()->getNombre(),
                    'activo'=>$tarjeta->getActivo() ? 'Si' : 'No',
                    'id' => $tarjeta->getId(),
                ));
            } else {
                $page = $this->renderView('tarjeta/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('tarjeta/new.html.twig', [
            'tarjeta' => $tarjeta,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="tarjeta_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, Tarjeta $tarjeta): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        return $this->render('tarjeta/_show.html.twig',['tarjeta'=>$tarjeta]);
    }

    /**
     * @Route("/{id}/edit", name="tarjeta_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Tarjeta $tarjeta): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(TarjetaType::class, $tarjeta, array('action' => $this->generateUrl('tarjeta_edit',array('id'=>$tarjeta->getId()))));
        $activoOriginal=$form->get('activo')->getData();
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {

                if(!$tarjeta->getActivo() && $activoOriginal==true && null!=$tarjeta->getResponsable())
                    $this->forward('App\Controller\ResponsableController::disableVehiculo', ['responsable' => $tarjeta->getResponsable()->getId()]);

                $em->persist($tarjeta);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La tarjeta fue actualizada satisfactoriamente",
                    'codigo' => $tarjeta->getCodigo(),
                    'tipotarjeta' => $tarjeta->getTipotarjeta()->getNombre(),
                    'tipocombustible' => $tarjeta->getTipocombustible()->getNombre(),
                    'activo'=>$tarjeta->getActivo() ? 'Si' : 'No',
                    'id' => $tarjeta->getId(),
                ));
            } else {
                $page = $this->renderView('tarjeta/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'tarjeta_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('tarjeta/new.html.twig', [
            'tarjeta' => $tarjeta,
            'form' => $form->createView(),
            'form_id' => 'tarjeta_edit',
            'action' => 'Actualizar',
            'title' => 'Editar tarjeta'
        ]);
    }

    /**
     * @Route("/{id}/delete", name="tarjeta_delete", options={"expose"=true})
     */
    public function delete(Request $request, Tarjeta $tarjeta): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($tarjeta);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'La tarjeta fue eliminada satisfactoriamente'));
    }

}
