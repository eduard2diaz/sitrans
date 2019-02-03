<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\AjusteTarjeta;
use App\Form\AjusteTarjetaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ajustetarjeta")
 */
class AjusteTarjetaController extends Controller
{
    /**
     * @Route("/", name="ajustetarjeta_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $ajustetarjetas = $this->getDoctrine()->getManager()->createQuery('SELECT r.id , t.codigo as tarjeta, r.fecha, r.cantefectivo as cantidadefectivo FROM App:AjusteTarjeta r JOIN r.tarjeta t JOIN t.tipotarjeta tt JOIN tt.institucion i WHERE i.id= :id')->setParameter('id',$this->getUser()->getInstitucion()->getId())->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($ajustetarjetas),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $ajustetarjetas,
                ]
                );
        }

        return $this->render('ajustetarjeta/index.html.twig');
    }

    /**
     * @Route("/new", name="ajustetarjeta_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $ajustetarjeta = new AjusteTarjeta();
        $ajustetarjeta->setUsuario($this->getUser());
        $form = $this->createForm(AjusteTarjetaType::class, $ajustetarjeta, array('institucion'=>$this->getUser()->getInstitucion()->getId(),'action' => $this->generateUrl('ajustetarjeta_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($ajustetarjeta);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El ajuste fue registrado satisfactoriamente",
                    'fecha' => $ajustetarjeta->getFecha(),
                    'cantidadefectivo' => $ajustetarjeta->getCantefectivo(),
                    'tarjeta' => $ajustetarjeta->getTarjeta()->getCodigo(),
                    'id' => $ajustetarjeta->getId(),
                ));
            } else {
                $page = $this->renderView('ajustetarjeta/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('ajustetarjeta/new.html.twig', [
            'ajustetarjeta' => $ajustetarjeta,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="ajustetarjeta_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, AjusteTarjeta $ajustetarjeta): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW',$ajustetarjeta);
       $tarjeta=$ajustetarjeta->getTarjeta()->getId();
        $esEliminable=$ajustetarjeta==$this->get('tarjeta.service')->ultimaOperacionTarjeta($tarjeta,$ajustetarjeta->getFecha());
        return $this->render('ajustetarjeta/_show.html.twig',['ajuste'=>$ajustetarjeta,'eliminable'=>$esEliminable]);
    }

    /**
     * @Route("/{id}/delete", name="ajustetarjeta_delete", options={"expose"=true})
     */
    public function delete(Request $request, AjusteTarjeta $ajustetarjeta): Response
    {
        if (!$request->isXmlHttpRequest() || $ajustetarjeta!=$this->get('tarjeta.service')->ultimaOperacionTarjeta($ajustetarjeta->getTarjeta(),$ajustetarjeta->getFecha()))
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('DELETE',$ajustetarjeta);
        $em = $this->getDoctrine()->getManager();
        $em->remove($ajustetarjeta);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El ajuste fue eliminado satisfactoriamente'));
    }
}
