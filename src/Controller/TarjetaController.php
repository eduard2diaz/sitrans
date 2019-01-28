<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Tarjeta;
use App\Form\TarjetaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Institucion;
use Symfony\Component\Serializer\Encoder\JsonEncode;

/**
 * @Route("/tarjeta")
 */
class TarjetaController extends Controller
{
    /**
     * @Route("/", name="tarjeta_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $tarjetas = $this->getDoctrine()->getManager()->createQuery('SELECT t.id, t.codigo,tt.nombre as tipotarjeta, tc.nombre as tipocombustible,t.activo FROM App:Tarjeta t JOIN t.tipotarjeta tt JOIN t.tipocombustible tc JOIN tt.institucion i WHERE i.id= :id')->setParameter('id',$this->getUser()->getInstitucion()->getId())->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($tarjetas),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $tarjetas,
                ]
                );
        }
        return $this->render('tarjeta/index.html.twig');
    }

    /**
     * @Route("/new", name="tarjeta_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $tarjeta = new Tarjeta();
        $form = $this->createForm(TarjetaType::class, $tarjeta, array('institucion'=>$this->getUser()->getInstitucion()->getId(),'action' => $this->generateUrl('tarjeta_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($tarjeta);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La tarjeta fue registrada satisfactoriamente",
                    'codigo' => $tarjeta->getCodigo(),
                    'tipotarjeta' => $tarjeta->getTipotarjeta()->getNombre(),
                    'tipocombustible' => $tarjeta->getTipocombustible()->getNombre(),
                    'activo'=>$tarjeta->getActivo() ? 'Si' : 'No',
                    'id' => $tarjeta->getId(),
                ));
            } else {
                $page = $this->renderView('tarjeta/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('tarjeta/new.html.twig', [
            'tarjeta' => $tarjeta,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="tarjeta_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, Tarjeta $tarjeta): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW',$tarjeta);
        return $this->render('tarjeta/_show.html.twig',['tarjeta'=>$tarjeta]);
    }

    /**
     * @Route("/{id}/edit", name="tarjeta_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Tarjeta $tarjeta): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('EDIT',$tarjeta);
        $form = $this->createForm(TarjetaType::class, $tarjeta, array('institucion'=>$this->getUser()->getInstitucion()->getId(),'action' => $this->generateUrl('tarjeta_edit',array('id'=>$tarjeta->getId()))));
        $activoOriginal=$form->get('activo')->getData();
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($tarjeta);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"La tarjeta fue actualizada satisfactoriamente",
                    'codigo' => $tarjeta->getCodigo(),
                    'tipotarjeta' => $tarjeta->getTipotarjeta()->getNombre(),
                    'tipocombustible' => $tarjeta->getTipocombustible()->getNombre(),
                    'activo'=>$tarjeta->getActivo() ? 'Si' : 'No',
                    'id' => $tarjeta->getId(),
                ));
            } else {
                $page = $this->renderView('tarjeta/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'tarjeta_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('tarjeta/new.html.twig', [
            'tarjeta' => $tarjeta,
            'eliminable'=>$this->esEliminable($tarjeta),
            'form' => $form->createView(),
            'form_id' => 'tarjeta_edit',
            'action' => 'Actualizar',
            'title' => 'Editar tarjeta'
        ]);
    }

    /**
     * @Route("/{id}/delete", name="tarjeta_delete", options={"expose"=true})
     */
    public function delete(Request $request, Tarjeta $tarjeta): Response
    {
        if (!$request->isXmlHttpRequest() || false==$this->esEliminable($tarjeta))
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('DELETE',$tarjeta);
        $em = $this->getDoctrine()->getManager();
        $em->remove($tarjeta);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'La tarjeta fue eliminada satisfactoriamente'));
    }

    /**
     * @Route("/{id}/cantidadefectivo", name="tarjeta_cantidadefectivo", methods="GET",options={"expose"=true})
     */
    public function cantidadEfectivo(Request $request, Tarjeta $tarjeta): Response
    {
        /*
         *Funcionalidad que devuelve la cantidad de efectivo que posse una tarjeta en una fecha dada, es utilizada por la cajera
         * en el proceso de registrar un chip
         */
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW',$tarjeta);
        $fecha=$request->get('fecha') ?? null;

        $cantidad=$this->get('traza.service')->cantidadEfectivoTarjetaenFechaX($tarjeta->getId(),$fecha) ?? 0;
        return new Response($cantidad);
    }


    /**
     * @Route("/{id}/findbyinstitucion", name="tarjeta_findbyinstitucion", options={"expose"=true})
     */
    public function findbyinstitucion(Request $request, Institucion $institucion)
    {
        /*
         *Devuelve todas las tarjetas disponibles en una determinada institucion, esto es utilizado para la
         * captación de un responsable
         */
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        $id=$request->get('responsable');
        $repository=$this->getDoctrine()->getManager();
        $qb = $repository->createQueryBuilder('tarjeta');
        $qb->distinct(true);
        $qb->select('t')->from('App:Tarjeta', 't');
        $qb->join('t.responsable', 'r');
        $qb->join('t.tipotarjeta', 'tt');
        $qb->join('tt.institucion', 'i');
        $qb->where('i.id= :institucion')->setParameter('institucion', $institucion);

        $result = $qb->getQuery()->getResult();

        if (count($result) > 0) {
            $qb = $repository->createQueryBuilder('tarjeta');
            $qb->select('t')->from('App:Tarjeta', 't');
            $qb->join('t.tipotarjeta', 'tt');
            $qb->join('tt.institucion', 'i');
            $qb->where('t.activo = true AND i.id= :institucion AND t.id NOT IN (:responsable)')
                ->setParameters(['responsable' => $result, 'institucion' => $institucion]);
            $result = $qb->getQuery()->getResult();
        } else {
            //Si ningua tarjeta tiene responsable devuelve el listado de tarjetas activas
            $qb = $repository->createQueryBuilder('tarjeta');
            $qb->select('t')->from('App:Tarjeta', 't');
            $qb->join('t.tipotarjeta', 'tt');
            $qb->join('tt.institucion', 'i');
            $qb->where('t.activo = true AND i.id= :institucion ')
                ->setParameter('institucion', $institucion);
            $result = $qb->getQuery()->getResult();
        }

        //Si estamos modificando un responsable, devuelveme ademas todas mis tarjetas
        if (null != $id) {
            $qb = $repository->createQueryBuilder('tarjeta');
            $qb->select('t')->from('App:Tarjeta', 't');
            $qb->join('t.responsable', 'r');
            $qb->join('t.tipotarjeta', 'tt');
            $qb->join('tt.institucion', 'i');
            $qb->where('r.id = :id AND i.id= :institucion');
            $qb->setParameters(['id' => $id, 'institucion' => $institucion]);
            $mias = $qb->getQuery()->getResult();
        }

        $parameters=['responsable' => $result, 'institucion' => $institucion];
        $qb = $repository->createQueryBuilder('tarjeta');
        $qb->select('t')->from('App:Tarjeta', 't');
        $qb->join('t.tipotarjeta', 'tt');
        $qb->join('tt.institucion', 'i');
        $qb->where('t.activo = true AND i.id= :institucion AND t.id IN (:responsable)');
        if (null != $id) {
            $qb->orWhere('i.id= :institucion AND t.id IN (:mias)');
            $parameters['mias']=$mias;
        }
        $qb->setParameters($parameters);

        $tarjetas=$qb->getQuery()->getResult();


        $array=[];
        foreach ($tarjetas as $value){
            $array[]=['id'=>$value->getId(),'nombre'=>$value->getCodigo()];
        }
        return new JsonResponse($array);
    }

    /*
     *Funcionalidad que devuelve un boolean indicando si la tarjeta es o no eliminable
     */
    private function esEliminable(Tarjeta $tarjeta){
        //No se puede eliminar a una tarjeta que posee responsable
        if($tarjeta->getResponsable()!=null)
            return false;

        $em=$this->getDoctrine()->getManager();
        $entidades=['Recargatarjeta','AjusteTarjeta','Chip','CierreMesTarjeta'];
        foreach ($entidades as $value){
            $object=$em->getRepository("App:$value")->findOneByTarjeta($tarjeta);
            if(null!=$object)
                return false;
        }
        return true;
    }


}
