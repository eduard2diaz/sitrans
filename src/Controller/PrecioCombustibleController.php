<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\PrecioCombustible;
use App\Form\PrecioCombustibleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/preciocombustible")
 */
class PrecioCombustibleController extends Controller
{
    /**
     * @Route("/", name="preciocombustible_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $preciocombustibles = $this->getDoctrine()->getManager()->createQuery('SELECT c.id, tc.nombre as tipocombustible, c.importe, c.fecha FROM App:PrecioCombustible c JOIN c.tipocombustible tc')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($preciocombustibles),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $preciocombustibles,
                ]
                );
        }

        return $this->render('preciocombustible/index.html.twig');
    }

    /**
     * @Route("/new", name="preciocombustible_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        
        $preciocombustible = new PrecioCombustible();
        $form = $this->createForm(PrecioCombustibleType::class, $preciocombustible, array('action' => $this->generateUrl('preciocombustible_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($preciocombustible);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El precio de combustible fue registrado satisfactoriamente",
                    'fecha' => $preciocombustible->getFecha(),
                    'importe' => $preciocombustible->getImporte(),
                    'tipocombustible' => $preciocombustible->getTipocombustible()->getNombre(),
                    'id' => $preciocombustible->getId(),
                ));
            } else {
                $page = $this->renderView('preciocombustible/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('preciocombustible/new.html.twig', [
            'preciocombustible' => $preciocombustible,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/show", name="preciocombustible_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, PrecioCombustible $preciocombustible): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        
        return $this->render('preciocombustible/_show.html.twig',['preciocombustible'=>$preciocombustible]);
    }
    /**
     * @Route("/{id}/edit", name="preciocombustible_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, PrecioCombustible $preciocombustible): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        
        $form = $this->createForm(PrecioCombustibleType::class, $preciocombustible, array('action' => $this->generateUrl('preciocombustible_edit',array('id'=>$preciocombustible->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($preciocombustible);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El precio de combustible fue actualizado satisfactoriamente",
                    'fecha' => $preciocombustible->getFecha()->format('d-m-Y'),
                    'importe' => $preciocombustible->getImporte(),
                    'tipocombustible' => $preciocombustible->getTipocombustible()->getNombre(),
                    'id' => $preciocombustible->getId(),
                ));
            } else {
                $page = $this->renderView('preciocombustible/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'preciocombustible_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('preciocombustible/new.html.twig', [
            'preciocombustible' => $preciocombustible,
            'form' => $form->createView(),
            'form_id' => 'preciocombustible_edit',
            'action' => 'Actualizar',
            'title' => 'Editar precio de combustible'
        ]);
    }

    /**
     * @Route("/{id}/delete", name="preciocombustible_delete", options={"expose"=true})
     */
    public function delete(Request $request, PrecioCombustible $preciocombustible): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($preciocombustible);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El precio de combustible fue eliminado satisfactoriamente'));
    }

    /**
     * @Route("/findbytarjeta", name="preciocombustible_findbytarjeta", options={"expose"=true})
     */
    public function findByTarjeta(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $precio=$request->get('litros');
        $fecha=$request->get('fecha');
        $em=$this->getDoctrine()->getManager();
        $tarjeta=$request->get('tarjeta');
        $tarjeta=$em->getRepository('App:Tarjeta')->find($tarjeta);
        $tipocombustible=$tarjeta->getTipocombustible();
        $importe=$this->get('energia.service')->importeCombustible($tipocombustible,$fecha) ;

        if(!$importe)
            throw new \Exception("No existe el importe");

        return new Response($importe[0]['importe']*$precio);
    }

    /**
     * @Route("/findbyvehiculo", name="preciocombustible_findbyvehiculo", options={"expose"=true})
     */
    public function findByVehiculo(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $precio=$request->get('litros');
        $fecha=$request->get('fecha');
        $em=$this->getDoctrine()->getManager();
        $vehiculo=$request->get('vehiculo');
        $vehiculo=$em->getRepository('App:Vehiculo')->find($vehiculo);
        $tipocombustible=$vehiculo->getResponsable()->getTarjetas()->first()->getTipocombustible();
        $importe=$this->get('energia.service')->importeCombustible($tipocombustible,$fecha);

        if(!$importe)
            throw new \Exception("No existe el importe");

        return new Response($importe[0]['importe']*$precio);
    }
}
