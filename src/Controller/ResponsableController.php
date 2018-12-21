<?php

namespace App\Controller;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Responsable;
use App\Form\ResponsableType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/responsable")
 */
class ResponsableController extends Controller
{
    /**
     * @Route("/", name="responsable_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $responsables = $this->getDoctrine()->getManager()->createQuery('SELECT r.id, r.nombre, r.apellidos, r.ci, a.nombre as area FROM App:Responsable r JOIN r.area a')->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($responsables),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $responsables,
                ]
                );
        }

        return $this->render('responsable/index.html.twig');
    }

    /**
     * @Route("/new", name="responsable_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $responsable = new Responsable();
        $form = $this->createForm(ResponsableType::class, $responsable, array('action' => $this->generateUrl('responsable_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {

                /*
                 * Como la llave foranea en realidad esta en tarjeta y no en responsable tengo que ir una a una a cada
                 * tarjeta y asignarle el responsable
                 */
                foreach ($responsable->getTarjetas()->toArray() as $value){
                    $value->setResponsable($responsable);
                    $em->persist($value);
                }

                $em->persist($responsable);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El responsable fue registrado satisfactoriamente",
                    'nombre' => $responsable->getNombre(),
                    'apellidos' => $responsable->getApellidos(),
                    'ci' => $responsable->getCi(),
                    'area' => $responsable->getArea()->getNombre(),
                    'id' => $responsable->getId(),
                ));
            } else {
                $page = $this->renderView('responsable/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('responsable/new.html.twig', [
            'responsable' => $responsable,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/show", name="responsable_show", methods="GET|POST",options={"expose"=true})
     */
    public function show(Request $request, Responsable $responsable): Response
    {
      if(!$request->isXmlHttpRequest())
          throw $this->createAccessDeniedException();
      return $this->render('responsable/_show.html.twig',['responsable'=>$responsable]);
    }
    /**
     * @Route("/{id}/edit", name="responsable_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Responsable $responsable): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(ResponsableType::class, $responsable, array('action' => $this->generateUrl('responsable_edit',array('id'=>$responsable->getId()))));
        $activoOriginal=$form->get('activo')->getData();
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {

                $tarjetas=$em->getRepository('App:Tarjeta')->findByResponsable($responsable);
                if(!$responsable->getActivo() && $activoOriginal==true) {
                    $this->disableVehiculo($responsable->getId());
                    foreach ($tarjetas as $val){
                        $val->setActivo(false);
                        $em->persist($val);
                    }
                }

           /*
            * Como la llave foranea en realidad esta en tarjeta y no en responsable tengo que ir una a una a cada
            * tarjeta y asignarle el responsable y antes de ello tengo que quitarle todas las tarjetas que tiene
            * asignado
            */
            foreach ($tarjetas as $value){
                if(!$responsable->getTarjetas()->contains($value)){
                    $responsable->getTarjetas()->removeElement($value);
                    $value->setResponsable(null);
                    $em->persist($value);
                }else
                    $responsable->getTarjetas()->removeElement($value);
            }

            foreach ($responsable->getTarjetas()->toArray() as $value){
                $value->setResponsable($responsable);
                $em->persist($value);
            }

                $em->persist($responsable);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El responsable fue actualizado satisfactoriamente",
                    'nombre' => $responsable->getNombre(),
                    'apellidos' => $responsable->getApellidos(),
                    'ci' => $responsable->getCi(),
                    'area' => $responsable->getArea()->getNombre(),
                    'id' => $responsable->getId(),
                ));
            } else {
                $page = $this->renderView('responsable/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'responsable_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('responsable/new.html.twig', [
            'responsable' => $responsable,
            'form' => $form->createView(),
            'form_id' => 'responsable_edit',
            'action' => 'Actualizar',
            'title' => 'Editar responsable'
        ]);
    }

    /**
     * @Route("/{id}/delete", name="responsable_delete", options={"expose"=true})
     */
    public function delete(Request $request, Responsable $responsable): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($responsable);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El responsable fue eliminado satisfactoriamente'));
    }

    public function disableVehiculo($responsable){
        $em=$this->getDoctrine()->getManager();
        $estados=[0,1];
        $consulta=$em->createQuery('SELECT v FROM App:Vehiculo v JOIN v.responsable r WHERE r.id= :id AND v.estado IN (:estados)');
        $consulta->setParameters(['id'=>$responsable,'estados'=>$estados]);
        $vehiculos=$consulta->getResult();

        foreach ($vehiculos as $value){
            $value->setEstado(2);
            $em->persist($value);
        }

        $em->flush();
    }

}
