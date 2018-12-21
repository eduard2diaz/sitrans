<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\CierremesKw;
use App\Form\CierremesKwType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tarjeta;
/**
 * @Route("/cierremeskw")
 */
class CierremesKwController extends Controller
{
    /**
     * @Route("/", name="cierremeskw_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $cierremeskws = $this->getDoctrine()->getManager()->createQuery('SELECT c.id , c.anno, c.mes FROM App:CierremesKw c')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($cierremeskws),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $cierremeskws,
                ]
                );
        }

        return $this->render('cierremeskw/index.html.twig');
    }

    /**
     * @Route("/new", name="cierremeskw_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $cierremeskw = new CierremesKw();
        $form = $this->createForm(CierremesKwType::class, $cierremeskw, array('action' => $this->generateUrl('cierremeskw_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($cierremeskw);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El cierre fue registrado satisfactoriamente",
                    'anno' => $cierremeskw->getAnno(),
                    'mes' => $cierremeskw->getMes(),
                    'id' => $cierremeskw->getId(),
                ));
            } else {
                $page = $this->renderView('cierremeskw/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('cierremeskw/new.html.twig', [
            'cierremeskw' => $cierremeskw,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="cierremeskw_show", methods="GET|POST",options={"expose"=true})
     */
    public function show(Request $request, CierremesKw $cierremeskw): Response
    {

        return $this->render('cierremeskw/show.html.twig', [
            'cierremeskw' => $cierremeskw,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="cierremeskw_delete", options={"expose"=true})
     */
    public function delete(Request $request, CierremesKw $cierremeskw): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($cierremeskw);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El cierre fue eliminado satisfactoriamente'));
    }
}
