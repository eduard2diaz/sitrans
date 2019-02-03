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
            $lecturarelojs = $this->getDoctrine()->getManager()->createQuery('SELECT l.id, re.codigo as reloj,a.nombre as area, l.fecha, l.lectura FROM App:LecturaReloj l JOIN l.reloj re JOIN re.area a JOIN a.ccosto cc JOIN cc.cuenta c JOIN c.institucion i WHERE i.id= :id')->setParameter('id',$this->getUser()->getInstitucion()->getId())->getResult();
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
        $lecturareloj->setUsuario($this->getUser());
        $form = $this->createForm(LecturaRelojType::class, $lecturareloj, array('institucion'=>$this->getUser()->getInstitucion()->getId(),'action' => $this->generateUrl('lecturareloj_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $lecturareloj->getReloj()->setKwrestante($lecturareloj->getReloj()->getKwrestante()-$lecturareloj->getLectura());
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

        $this->denyAccessUnlessGranted('VIEW',$lecturareloj->getReloj());
        $area=$lecturareloj->getReloj()->getArea()->getId();
        $mes=$lecturareloj->getFecha()->format('m');
        $anno=$lecturareloj->getFecha()->format('Y');
        $eliminable=$this->get('reloj.service')->ultimaOperacionKwArea($area,$lecturareloj->getFecha())==$lecturareloj;
        return $this->render('lectura_reloj/_show.html.twig',['lecturareloj'=>$lecturareloj,'eliminable'=>$eliminable]);
    }

    /**
     * @Route("/{id}/delete", name="lecturareloj_delete", options={"expose"=true})
     */
    public function delete(Request $request, LecturaReloj $lecturareloj): Response
    {
        $this->denyAccessUnlessGranted('VIEW',$lecturareloj->getReloj());
        $area=$lecturareloj->getReloj()->getArea()->getId();

        if (!$request->isXmlHttpRequest() || $lecturareloj!=$this->get('reloj.service')->ultimaOperacionKwArea($area,$lecturareloj->getFecha()))
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $lecturareloj->getReloj()->setKwrestante($lecturareloj->getReloj()->getKwrestante()+$lecturareloj->getLectura());
        $em->remove($lecturareloj);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'La lectura fue eliminada satisfactoriamente'));
    }
}
