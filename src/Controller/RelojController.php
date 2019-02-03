<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Reloj;
use App\Form\RelojType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reloj")
 */
class RelojController extends Controller
{
    /**
     * @Route("/", name="reloj_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $relojs = $this->getDoctrine()->getManager()->createQuery('SELECT r.id , r.codigo, a.nombre as area, r.activo FROM App:Reloj r JOIN r.area a JOIN a.ccosto cc JOIN cc.cuenta c JOIN c.institucion i WHERE i.id= :id')->setParameter('id',$this->getUser()->getInstitucion()->getId())->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($relojs),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $relojs,
                ]
                );
        }

        return $this->render('reloj/index.html.twig');
    }

    /**
     * @Route("/new", name="reloj_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $reloj = new Reloj();
        $form = $this->createForm(RelojType::class, $reloj, array('institucion'=>$this->getUser()->getInstitucion()->getId(),'action' => $this->generateUrl('reloj_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($reloj);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El reloj fue registrado satisfactoriamente",
                    'codigo' => $reloj->getCodigo(),
                    'area' => $reloj->getArea()->getNombre(),
                    'activo' => $reloj->getActivo(),
                    'id' => $reloj->getId(),
                ));
            } else {
                $page = $this->renderView('reloj/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('reloj/new.html.twig', [
            'reloj' => $reloj,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="reloj_show", methods="GET|POST",options={"expose"=true})
     */
    public function show(Request $request, Reloj $reloj): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW',$reloj);
        return $this->render('reloj/_show.html.twig',['reloj'=>$reloj]);
    }

    /**
     * @Route("/{id}/edit", name="reloj_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Reloj $reloj): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        $this->denyAccessUnlessGranted('EDIT',$reloj);
        $form = $this->createForm(RelojType::class, $reloj, array('institucion'=>$this->getUser()->getInstitucion()->getId(),'action' => $this->generateUrl('reloj_edit',array('id'=>$reloj->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($reloj);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El reloj fue actualizado satisfactoriamente",
                    'codigo' => $reloj->getCodigo(),
                    'area' => $reloj->getArea()->getNombre(),
                    'activo' => $reloj->getActivo(),
                    'id' => $reloj->getId(),
                ));
            } else {
                $page = $this->renderView('reloj/_form.html.twig', array(
                    'reloj' => $reloj,
                    'form' => $form->createView(),
                    'form_id' => 'reloj_edit',
                    'action' => 'Actualizar',
                    'eliminable'=>$this->esEliminable($reloj)
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('reloj/new.html.twig', [
            'reloj' => $reloj,
            'form' => $form->createView(),
            'form_id' => 'reloj_edit',
            'action' => 'Actualizar',
            'title' => 'Editar reloj',
            'eliminable'=>$this->esEliminable($reloj)
        ]);
    }

    /**
     * @Route("/{id}/delete", name="reloj_delete", options={"expose"=true})
     */
    public function delete(Request $request, Reloj $reloj): Response
    {
        if (!$request->isXmlHttpRequest() || false==$this->esEliminable($reloj))
            throw $this->createAccessDeniedException();
        $this->denyAccessUnlessGranted('DELETE',$reloj);
        $em = $this->getDoctrine()->getManager();
        $em->remove($reloj);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El reloj fue eliminado satisfactoriamente'));
    }

    private function esEliminable(Reloj $reloj){
        $em=$this->getDoctrine()->getManager();
        $entidades=['LecturaReloj','RecargaKw'];
        foreach ($entidades as $value){
            $obj=$em->getRepository("App:$value")->findOneByReloj($reloj);
            if(null!=$obj)
                return false;
        }
        return true;
    }
}
