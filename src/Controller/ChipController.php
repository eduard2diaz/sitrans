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

        return $this->render('chip/_show.html.twig',['chip'=>$chip]);
    }

    /**
     * @Route("/{id}/edit", name="chip_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Chip $chip): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $importeOriginal=$chip->getImporte();
        $cantlitrosOriginal=$chip->getLitrosextraidos();

        $form = $this->createForm(ChipType::class, $chip, array('action' => $this->generateUrl('chip_edit',array('id'=>$chip->getId()))));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $chip->setUsuario($this->getUser());
                $traza=$this->findTraza($chip->getId());

                $diferenciaImporte=$chip->getImporte()-$importeOriginal;
                $diferenciaLitros= $chip->getLitrosextraidos()-$cantlitrosOriginal;
                if($diferenciaImporte<0){
                    $diferenciaImporte*=-1;
                    $chip->getTarjeta()->setCantefectivo($chip->getTarjeta()->getCantefectivo()+$diferenciaImporte);
                }elseif($diferenciaImporte>0)
                    $chip->getTarjeta()->setCantefectivo($chip->getTarjeta()->getCantefectivo()-$diferenciaImporte);

                if($diferenciaLitros<0){
                    $diferenciaLitros*=-1;
                    $chip->getTarjeta()->setCantlitros($chip->getTarjeta()->getCantlitros()+$diferenciaLitros);
                    if($traza->getVehiculo() instanceof Vehiculo) {
                        $traza->getVehiculo()->setLitrosentanque($traza->getVehiculo()->getLitrosentanque() - $diferenciaLitros);
                        $em->persist($traza->getVehiculo());
                    }
                }elseif($diferenciaLitros>0) {
                    $chip->getTarjeta()->setCantlitros($chip->getTarjeta()->getCantlitros() - $diferenciaLitros);

                    if($traza->getVehiculo() instanceof Vehiculo) {
                        $traza->getVehiculo()->setLitrosentanque($traza->getVehiculo()->getLitrosentanque() + $diferenciaLitros);
                        $em->persist($traza->getVehiculo());
                    }
                }

                $em->persist($chip);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El chip fue actualizado satisfactoriamente",
                    'tarjeta' => $chip->getTarjeta()->getCodigo(),
                    'idfisico' => $chip->getIdfisico(),
                    'fecha' => $chip->getFecha()->format('d-m-Y h:i a'),
                    'idlogico' => $chip->getIdlogico(),
                    'id' => $chip->getId(),
                ));
            } else {
                $page = $this->renderView('chip/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'chip_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('chip/new.html.twig', [
            'chip' => $chip,
            'form' => $form->createView(),
            'form_id' => 'chip_edit',
            'action' => 'Actualizar',
            'title' => 'Editar chip'
        ]);
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

    //Funciones extras
    private function findVehiculo($tarjeta_id){
        $manager=$this->getDoctrine()->getManager();
        $consulta=$manager->createQuery('SELECT v FROM App:Vehiculo v JOIN v.responsable r JOIN r.tarjetas t WHERE t.id = :tarjeta_id');
        $consulta->setParameter('tarjeta_id',$tarjeta_id);
        $vehiculo=$consulta->getResult();
        if(!$vehiculo)
           return;

        return $vehiculo;
    }

    private function findTraza($chip){
        $manager=$this->getDoctrine()->getManager();
        $consulta=$manager->createQuery('SELECT t FROM App:Traza t WHERE t.identificador = :id AND t.entity = :entity');
        $consulta->setParameters(['id'=>$chip,'entity'=>Chip::class]);
        $traza=$consulta->getSingleResult();
        if(!$traza)
            throw new \LogicException('No existe la traza');

        return $traza;
    }
}
