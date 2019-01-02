<?php

namespace App\Controller;

use App\Entity\Vehiculo;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Chip;
use App\Form\ChipType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/chip")
 */
class ChipController extends Controller
{
    /**
     * @Route("/", name="chip_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $chips = $this->getDoctrine()->getManager()->createQuery('SELECT c.id, t.codigo as tarjeta, c.fecha, c.idfisico, c.idlogico FROM App:Chip c JOIN c.tarjeta t')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($chips),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $chips,
                ]
                );
        }

        return $this->render('chip/index.html.twig');
    }

    /**
     * @Route("/new", name="chip_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $chip = new Chip();
        $form = $this->createForm(ChipType::class, $chip, array('action' => $this->generateUrl('chip_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $chip->setUsuario($this->getUser());
                $em->persist($chip);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El chip fue registrado satisfactoriamente",
                    'tarjeta' => $chip->getTarjeta()->getCodigo(),
                    'fecha' => $chip->getFecha(),
                    'idfisico' => $chip->getIdfisico(),
                    'idlogico' => $chip->getIdlogico(),
                    'id' => $chip->getId(),
                ));
            } else {
                $page = $this->renderView('chip/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('chip/new.html.twig', [
            'chip' => $chip,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/show", name="chip_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, Chip $chip): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $tarjeta=$chip->getTarjeta()->getId();

        $mes=$chip->getFecha()->format('m');
        $anno=$chip->getFecha()->format('Y');
        $cierre=$this->get('energia.service')->existeCierreCombustible($anno,$mes,$tarjeta);
        return $this->render('chip/_show.html.twig',['chip'=>$chip,'cierre'=>$cierre]);
    }

    /**
     * @Route("/{id}/delete", name="chip_delete", options={"expose"=true})
     */
    public function delete(Request $request, Chip $chip): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($chip);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El chip fue eliminado satisfactoriamente'));
    }

}
