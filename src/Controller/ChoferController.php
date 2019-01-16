<?php

namespace App\Controller;

use App\Entity\Tipovehiculo;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Chofer;
use App\Form\ChoferType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/chofer")
 */
class ChoferController extends Controller
{
    /**
     * @Route("/", name="chofer_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $em=$this->getDoctrine()->getManager();
            if($this->isGranted('ROLE_SUPERADMIN'))
                $consulta = $em->createQuery('SELECT ch.id, ch.nombre, ch.apellido, ch.ci FROM App:Chofer ch');
            else
            {
                $consulta = $em->createQuery('SELECT ch.id, ch.nombre, ch.apellido, ch.ci FROM App:Chofer ch JOIN ch.institucion i WHERE i.id= :institucion');
                $consulta->setParameter('institucion',$this->getUser()->getInstitucion()->getId());
            }

            $chofers = $consulta->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($chofers),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $chofers,
                ]
                );
        }

        return $this->render('chofer/index.html.twig');
    }

    /**
     * @Route("/new", name="chofer_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $chofer = new Chofer();
        $form = $this->createForm(ChoferType::class, $chofer, array('institucion'=>$this->getUser()->getInstitucion()->getId(),'action' => $this->generateUrl('chofer_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($chofer);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El chofer fue registrado satisfactoriamente",
                    'nombre' => $chofer->getNombre(),
                    'apellido' => $chofer->getApellido(),
                    'ci' => $chofer->getCi(),
                    'id' => $chofer->getId(),
                ));
            } else {
                $page = $this->renderView('chofer/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('chofer/new.html.twig', [
            'chofer' => $chofer,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/show", name="chofer_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, Chofer $chofer): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        return $this->render('chofer/_show.html.twig',['chofer'=>$chofer]);
    }
    /**
     * @Route("/{id}/edit", name="chofer_edit", methods="GET|POST",options={"expose"=true})
    */
    public function edit(Request $request, Chofer $chofer): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(ChoferType::class, $chofer, array('institucion' => $this->getUser()->getInstitucion()->getId(), 'action' => $this->generateUrl('chofer_edit', array('id' => $chofer->getId()))));
        $activoOriginal = $form->get('activo')->getData();
        $institucionOriginal = $chofer->getInstitucion()->getId();
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted()){
                if($chofer->getInstitucion()->getId()!=$institucionOriginal){
                    $vehiculo_asignado=$this->tieneVehiculoAsignado($chofer->getId());
                    if(!empty($vehiculo_asignado))
                        $form->get('institucion')->addError(new  FormError("Para poder cambiar el chofer de institución antes debe quitarlo del vehiculo con matrícula ".$vehiculo_asignado[0]['matricula']));
                }

            if ($form->isValid()) {
                if (!$chofer->getActivo() && $activoOriginal == true)
                    $this->disableVehiculo($chofer->getId());

                $em->persist($chofer);
                $em->flush();
                return new JsonResponse(array('mensaje' => "El chofer fue actualizado satisfactoriamente",
                    'nombre' => $chofer->getNombre(),
                    'apellido' => $chofer->getApellido(),
                    'ci' => $chofer->getCi(),
                    'id' => $chofer->getId(),
                ));
            } else {
                $page = $this->renderView('chofer/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'chofer_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }
    }

        return $this->render('chofer/new.html.twig', [
            'chofer' => $chofer,
            'form' => $form->createView(),
            'form_id' => 'chofer_edit',
            'action' => 'Actualizar',
            'title' => 'Editar chofer'
        ]);
    }

    /**
     * @Route("/{id}/delete", name="chofer_delete", options={"expose"=true})
     */
    public function delete(Request $request, Chofer $chofer): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($chofer);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El chofer fue eliminado satisfactoriamente'));
    }

    /**
     * @Route("/{id}/findbytipovehiculo", name="chofer_findbytipovehiculo", options={"expose"=true})
     */
    public function findbytipovehiculo(Request $request, Tipovehiculo $tipovehiculo)
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em=$this->getDoctrine()->getManager();
        $institucion=$request->get('institucion');
        $choferesActivos=$em->createQuery('Select ch from App:Chofer ch join ch.institucion i WHERE ch.activo= TRUE AND i.id= :institucion')->setParameter('institucion',$institucion)->getResult();
        $choferes=[];
        foreach ($choferesActivos as $value){
            $esValido=true;
            foreach ($tipovehiculo->getIdlicencia() as $lic)
                if(!$value->getIdlicencia()->contains($lic)){
                    $esValido=false;
                    break;
                }
                if(true==$esValido)
                    $choferes[]=['id'=>$value->getId(),'nombre'=>$value->__toString()];

        }
        return new JsonResponse($choferes);
    }

    private function tieneVehiculoAsignado($chofer){
        $em=$this->getDoctrine()->getManager();
        $consulta=$em->createQuery('SELECT v.matricula FROM App:Vehiculo v JOIN v.chofer c WHERE c.id= :id');
        $consulta->setParameter('id',$chofer);
        $consulta->setMaxResults(1);
        return $consulta->getResult();
    }


    private function disableVehiculo($chofer){
        $em=$this->getDoctrine()->getManager();
        $estados=[0,1];
        $consulta=$em->createQuery('SELECT v FROM App:Vehiculo v JOIN v.chofer c WHERE c.id= :id AND v.estado IN (:estados)');
        $consulta->setParameters(['id'=>$chofer,'estados'=>$estados]);
        $vehiculos=$consulta->getResult();

        foreach ($vehiculos as $value){
            $value->setEstado(2);
            $em->persist($value);
        }

        $em->flush();

    }
}
