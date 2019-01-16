<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Recargatarjeta;
use App\Form\RecargatarjetaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/recargatarjeta")
 */
class RecargatarjetaController extends Controller
{
    /**
     * @Route("/", name="recargatarjeta_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $recargatarjetas = $this->getDoctrine()->getManager()->createQuery('SELECT r.id , t.codigo as tarjeta, r.fecha, r.cantidadlitros, r.cantidadefectivo FROM App:Recargatarjeta r JOIN r.tarjeta t JOIN t.tipotarjeta tt JOIN tt.institucion i WHERE i.id= :id')->setParameter('id',$this->getUser()->getInstitucion()->getId())->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($recargatarjetas),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $recargatarjetas,
                ]
                );
        }

        return $this->render('recargatarjeta/index.html.twig');
    }

    /**
     * @Route("/new", name="recargatarjeta_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        
        $recargatarjeta = new Recargatarjeta();
        $recargatarjeta->setUsuario($this->getUser());
        $form = $this->createForm(RecargatarjetaType::class, $recargatarjeta, array('institucion'=>$this->getUser()->getInstitucion()->getId(),'action' => $this->generateUrl('recargatarjeta_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($recargatarjeta);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La recarga fue registrada satisfactoriamente",
                    'fecha' => $recargatarjeta->getFecha(),
                    'cantidadlitros' => $recargatarjeta->getCantidadlitros(),
                    'cantidadefectivo' => $recargatarjeta->getCantidadefectivo(),
                    'tarjeta' => $recargatarjeta->getTarjeta()->getCodigo(),
                    'id' => $recargatarjeta->getId(),
                ));
            } else {
                $page = $this->renderView('recargatarjeta/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('recargatarjeta/new.html.twig', [
            'recargatarjeta' => $recargatarjeta,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="recargatarjeta_show", methods="GET|POST",options={"expose"=true})
     */
    public function show(Request $request, Recargatarjeta $recargatarjeta): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $tarjeta=$recargatarjeta->getTarjeta()->getId();
        $mes=$recargatarjeta->getFecha()->format('m');
        $anno=$recargatarjeta->getFecha()->format('Y');
        $cierre=$this->get('energia.service')->existeCierreCombustible($anno,$mes,$tarjeta);

        return $this->render('recargatarjeta/_show.html.twig',['recarga'=>$recargatarjeta,'cierre'=>$cierre]);
    }

    /**
     * @Route("/{id}/delete", name="recargatarjeta_delete", options={"expose"=true})
     */
    public function delete(Request $request, Recargatarjeta $recargatarjeta): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($recargatarjeta);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'La recarga fue eliminada satisfactoriamente'));
    }
}
