<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Centrocosto;
use App\Entity\Cuenta;
use App\Form\CentrocostoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/centrocosto")
 */
class CentrocostoController extends Controller
{
    /**
     * @Route("/", name="centrocosto_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $centrocostos = $this->getDoctrine()->getManager()->createQuery('SELECT sc.id , sc.nombre, sc.codigo, c.nombre as cuenta FROM App:Centrocosto sc JOIN sc.cuenta c JOIN c.institucion i WHERE i.id= :id')->setParameter('id',$this->getUser()->getInstitucion()->getId())->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($centrocostos),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $centrocostos,
                ]
                );
        }

        return $this->render('centrocosto/index.html.twig');
    }

    /**
     * @Route("/new", name="centrocosto_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $centrocosto = new Centrocosto();
        $form = $this->createForm(CentrocostoType::class, $centrocosto, array('institucion'=>$this->getUser()->getInstitucion()->getId(),'action' => $this->generateUrl('centrocosto_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($centrocosto);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El centro de costo fue registrado satisfactoriamente",
                    'nombre' => $centrocosto->getNombre(),
                    'codigo' => $centrocosto->getCodigo(),
                    'cuenta' => $centrocosto->getCuenta()->getNombre(),
                    'id' => $centrocosto->getId(),
                ));
            } else {
                $page = $this->renderView('centrocosto/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('centrocosto/new.html.twig', [
            'centrocosto' => $centrocosto,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="centrocosto_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Centrocosto $centrocosto): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        $this->denyAccessUnlessGranted('EDIT',$centrocosto);
        $form = $this->createForm(CentrocostoType::class, $centrocosto, array('institucion'=>$this->getUser()->getInstitucion()->getId(),'action' => $this->generateUrl('centrocosto_edit',array('id'=>$centrocosto->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($centrocosto);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El centro de costo fue actualizado satisfactoriamente",
                    'nombre' => $centrocosto->getNombre(),
                    'codigo' => $centrocosto->getCodigo(),
                    'cuenta' => $centrocosto->getCuenta()->getNombre(),
                    'id' => $centrocosto->getId(),
                ));
            } else {
                $page = $this->renderView('centrocosto/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'centrocosto_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('centrocosto/new.html.twig', [
            'centrocosto' => $centrocosto,
            'form' => $form->createView(),
            'form_id' => 'centrocosto_edit',
            'action' => 'Actualizar',
            'title' => 'Editar centro de costo',
            'eliminable'=>$this->esEliminable($centrocosto)
        ]);
    }

    /**
     * @Route("/{id}/delete", name="centrocosto_delete", options={"expose"=true})
     */
    public function delete(Request $request, Centrocosto $centrocosto): Response
    {
        if (!$request->isXmlHttpRequest() || false==$this->esEliminable($centrocosto))
            throw $this->createAccessDeniedException();
        $this->denyAccessUnlessGranted('DELETE',$centrocosto);
        $em = $this->getDoctrine()->getManager();
        $em->remove($centrocosto);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El centro de costo fue eliminado satisfactoriamente'));
    }

    private function esEliminable(Centrocosto $centrocosto){
        $em=$this->getDoctrine()->getManager();
        $entidades=[
            ['nombre'=>'Area','llave'=>'ccosto'],
            ['nombre'=>'PlanefectivoCuenta','llave'=>'centrocosto']
        ];
        foreach ($entidades as $value){
            $entidad=$value['nombre'];
            $llave=$value['llave'];
            $consulta=$em->createQuery("SELECT count(o.id) FROM App:$entidad o JOIN o.$llave a WHERE a.id= :id");
            $consulta->setParameter('id',$centrocosto->getId());
            $consulta->setMaxResults(1);
            $result=$consulta->getResult();
            if($result[0][1]>0)
                return false;
        }
        return true;
    }


    /**
     * @Route("/{cuenta}/searchbycuenta",options={"expose"=true}, name="centrocosto_searchbycuenta")
     */
    public function searchbycuenta(Request $request, Cuenta $cuenta): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em=$this->getDoctrine()->getManager();
        $consulta=$em->createQuery('SELECT cc FROM App:Centrocosto cc JOIN cc.cuenta c WHERE c.id= :id');
        $consulta->setParameter('id',$cuenta->getId());
        $ccostos=$consulta->getResult();
        $array=array();
        foreach ($ccostos as $value)
            $array[]=['id'=>$value->getId(),'nombre'=>$value->getNombre()];

        return new Response(json_encode($array));
    }
}
