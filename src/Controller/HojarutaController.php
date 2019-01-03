<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Hojaruta;
use App\Form\HojarutaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/hojaruta")
 */
class HojarutaController extends Controller
{
    /**
     * @Route("/", name="hojaruta_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $hojarutas = $this->getDoctrine()->getManager()->createQuery('SELECT h.id, v.matricula as vehiculo, h.codigo, h.fechasalida, h.fechallegada FROM App:Hojaruta h JOIN h.vehiculo v')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($hojarutas),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $hojarutas,
                ]
                );
        }

        return $this->render('hojaruta/index.html.twig');
    }

    /**
     * @Route("/new", name="hojaruta_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $hojaruta = new Hojaruta();
        $form = $this->createForm(HojarutaType::class, $hojaruta, array('action' => $this->generateUrl('hojaruta_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $hojaruta->setUsuario($this->getUser());
                $em->persist($hojaruta);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La hoja de ruta fue registrada satisfactoriamente",
                    'vehiculo' => $hojaruta->getVehiculo()->getMatricula(),
                    'codigo' => $hojaruta->getCodigo(),
                    'fechasalida' => $hojaruta->getFechasalida(),
                    'fechallegada' => $hojaruta->getFechallegada(),
                    'id' => $hojaruta->getId(),
                ));
            } else {
                $page = $this->renderView('hojaruta/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('hojaruta/new.html.twig', [
            'hojaruta' => $hojaruta,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/show", name="hojaruta_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, Hojaruta $hojaruta): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em=$this->getDoctrine()->getManager();
        $trazahruta=$em->getRepository('App:Traza')->findOneBy(['identificador'=>$hojaruta->getId(),'entity'=>get_class($hojaruta)]);
        $tarjeta=$trazahruta->getTarjeta();
        $mes=$hojaruta->getFechasalida()->format('m');
        $anno=$hojaruta->getFechasalida()->format('Y');
        $cierre=$this->get('energia.service')->existeCierreCombustible($anno,$mes,$tarjeta);

        return $this->render('hojaruta/_show.html.twig',['hojaruta'=>$hojaruta,'cierre'=>$cierre]);
    }

    /**
     * @Route("/{id}/edit", name="hojaruta_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Hojaruta $hojaruta): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(HojarutaType::class, $hojaruta, array('action' => $this->generateUrl('hojaruta_edit',array('id'=>$hojaruta->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $hojaruta->setUsuario($this->getUser());
                $em->persist($hojaruta);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La hoja de ruta fue actualizada satisfactoriamente",
                    'vehiculo' => $hojaruta->getVehiculo()->getMatricula(),
                    'codigo' => $hojaruta->getCodigo(),
                    'fechasalida' => $hojaruta->getFechasalida()->format('d-m-Y h:i a'),
                    'fechallegada' => $hojaruta->getFechallegada()->format('d-m-Y h:i a'),
                    'id' => $hojaruta->getId(),
                ));
            } else {
                $page = $this->renderView('hojaruta/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'hojaruta_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('hojaruta/new.html.twig', [
            'hojaruta' => $hojaruta,
            'form' => $form->createView(),
            'form_id' => 'hojaruta_edit',
            'action' => 'Actualizar',
            'title' => 'Editar hoja de ruta'
        ]);
    }

    /**
     * @Route("/{id}/delete", name="hojaruta_delete", options={"expose"=true})
     */
    public function delete(Request $request, Hojaruta $hojaruta): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($hojaruta);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'La hoja de ruta fue eliminada satisfactoriamente'));
    }
}
