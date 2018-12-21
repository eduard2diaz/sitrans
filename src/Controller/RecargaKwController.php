<?php

namespace App\Controller;

use App\Entity\Reloj;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\RecargaKw;
use App\Form\RecargaKwType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/recargakw")
 */
class RecargaKwController extends Controller
{
    /**
     * @Route("/", name="recargakw_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $recargakws = $this->getDoctrine()->getManager()->createQuery('SELECT r.id , re.codigo as reloj, r.fecha, r.asignacion FROM App:RecargaKw r JOIN r.reloj re')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($recargakws),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $recargakws,
                ]
                );
        }

        return $this->render('recargakw/index.html.twig');
    }

    /**
     * @Route("/new", name="recargakw_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $recargakw = new RecargaKw();
        $form = $this->createForm(RecargaKwType::class, $recargakw, array('action' => $this->generateUrl('recargakw_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $recargakw->setUsuario($this->getUser());
                $recargakw->getReloj()->setKwrestante($recargakw->getReloj()->getKwrestante()+$recargakw->getAsignacion());
                $em->persist($recargakw);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La recarga fue registrada satisfactoriamente",
                    'fecha' => $recargakw->getFecha(),
                    'reloj' => $recargakw->getReloj()->getCodigo(),
                    'asignacion' => $recargakw->getAsignacion(),
                    'id' => $recargakw->getId(),
                ));
            } else {
                $page = $this->renderView('recargakw/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('recargakw/new.html.twig', [
            'recargakw' => $recargakw,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="recargakw_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, RecargaKw $recargakw): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        return $this->render('recargakw/_show.html.twig',['recarga'=>$recargakw]);
    }

    /**
     * @Route("/{id}/delete", name="recargakw_delete", options={"expose"=true})
     */
    public function delete(Request $request, RecargaKw $recargakw): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $recargakw->getReloj()->setKwrestante($recargakw->getReloj()->getKwrestante()-$recargakw->getAsignacion());
        $em->remove($recargakw);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'La recarga fue eliminada satisfactoriamente'));
    }

    /**
     * @Route("/ajax", name="recargakw_ajax", options={"expose"=true})
     */
    public function ajax(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $reloj=$request->get('reloj');
        $fecha=$request->get('fecha');
        $em=$this->getDoctrine()->getManager();
        $reloj=$em->getRepository('App:Reloj')->find($reloj);
        $fecha=new \DateTime($fecha);
        if(!$reloj)
            throw new \Exception('Ha ocurrido un error');

        $area=$reloj->getArea()->getId();
        $mes_anterior=$fecha->sub(new \DateInterval('P1M'));
        $mes=$mes_anterior->format('m');
        $anno=$mes_anterior->format('y');
        return new JsonResponse(['restante'=>$this->get('energia.service')->estadoKw($area,$anno,$mes)['cierreanterior']]);
    }


}
