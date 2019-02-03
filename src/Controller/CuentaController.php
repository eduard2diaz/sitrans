<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Cuenta;
use App\Form\CuentaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cuenta")
 */
class CuentaController extends Controller
{
    /**
     * @Route("/", name="cuenta_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $cuentas = $this->getDoctrine()->getManager()->createQuery('SELECT c.id, c.nombre, c.codigo, c.naturaleza, c.nae FROM App:Cuenta c JOIN c.institucion t WHERE t.id= :id')->setParameter('id',$this->getUser()->getInstitucion()->getId())->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($cuentas),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $cuentas,
                ]
                );
        }

        return $this->render('cuenta/index.html.twig');
    }

    /**
     * @Route("/new", name="cuenta_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        
        $cuenta = new Cuenta();
        $cuenta->setInstitucion($this->getUser()->getInstitucion());
        $form = $this->createForm(CuentaType::class, $cuenta, array('action' => $this->generateUrl('cuenta_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($cuenta);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La cuenta fue registrada satisfactoriamente",
                    'nombre' => $cuenta->getNombre(),
                    'codigo' => $cuenta->getCodigo(),
                    'naturaleza' => $cuenta->getNaturaleza(),
                    'nae' => $cuenta->getNae(),
                    'id' => $cuenta->getId(),
                ));
            } else {
                $page = $this->renderView('cuenta/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('cuenta/new.html.twig', [
            'cuenta' => $cuenta,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="cuenta_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Cuenta $cuenta): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('EDIT',$cuenta);
        $form = $this->createForm(CuentaType::class, $cuenta, array('action' => $this->generateUrl('cuenta_edit',array('id'=>$cuenta->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($cuenta);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La cuenta fue actualizada satisfactoriamente",
                    'nombre' => $cuenta->getNombre(),
                    'codigo' => $cuenta->getCodigo(),
                    'naturaleza' => $cuenta->getNaturalezaToString(),
                    'nae' => $cuenta->getNae(),
                    'id' => $cuenta->getId(),
                ));
            } else {
                $page = $this->renderView('cuenta/_form.html.twig', array(
                    'cuenta' => $cuenta,
                    'form' => $form->createView(),
                    'form_id' => 'cuenta_edit',
                    'action' => 'Actualizar',
                    'eliminable'=>$this->esEliminable($cuenta)
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('cuenta/new.html.twig', [
            'cuenta' => $cuenta,
            'form' => $form->createView(),
            'form_id' => 'cuenta_edit',
            'action' => 'Actualizar',
            'title' => 'Editar cuenta',
            'eliminable'=>$this->esEliminable($cuenta)
        ]);
    }

    /**
     * @Route("/{id}/delete", name="cuenta_delete", options={"expose"=true})
     */
    public function delete(Request $request, Cuenta $cuenta): Response
    {
        if (!$request->isXmlHttpRequest() || false==$this->esEliminable($cuenta))
            throw $this->createAccessDeniedException();
        $this->denyAccessUnlessGranted('DELETE',$cuenta);
        $em = $this->getDoctrine()->getManager();
        $em->remove($cuenta);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'La cuenta fue eliminada satisfactoriamente'));
    }

    private function esEliminable(Cuenta $cuenta){
        $em=$this->getDoctrine()->getManager();
        $entidades=['Partida','Centrocosto'];
        foreach ($entidades as $value){
            $objeto=$em->getRepository("App:$value")->findOneByCuenta($cuenta);
            if(null!=$objeto)
                return false;
        }
        return true;
    }
}
