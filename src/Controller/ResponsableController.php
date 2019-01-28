<?php

namespace App\Controller;

use App\Entity\Institucion;
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
        if ($request->isXmlHttpRequest()) {
            $responsables = $this->get('institucion.service')->obtenerResponsablesSubordinados();

            return new JsonResponse(
                $result = [
                    'iTotalRecords' => count($responsables),
                    'iTotalDisplayRecords' => 10,
                    'sEcho' => 0,
                    'sColumns' => '',
                    'aaData' => $responsables,
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
        if (!$request->isXmlHttpRequest())
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
                foreach ($responsable->getTarjetas()->toArray() as $value) {
                    $value->setResponsable($responsable);
                    $em->persist($value);
                }

                $em->persist($responsable);
                $em->flush();
                return new JsonResponse(array('mensaje' => "El responsable fue registrado satisfactoriamente",
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

        return new JsonResponse([
            'html' => $this->renderView('responsable/new.html.twig', [
                'responsable' => $responsable,
                'form' => $form->createView(),
            ]),
            'responsable' => $responsable->getId()
        ]);
    }

    /**
     * @Route("/{id}/show", name="responsable_show", methods="GET|POST",options={"expose"=true})
     */
    public function show(Request $request, Responsable $responsable): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW', $responsable);
        return $this->render('responsable/_show.html.twig', ['responsable' => $responsable]);
    }

    /**
     * @Route("/{id}/edit", name="responsable_edit", methods="GET|POST",options={"expose"=true})
     */
    public function edit(Request $request, Responsable $responsable): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('EDIT', $responsable);
        $form = $this->createForm(ResponsableType::class, $responsable, array('action' => $this->generateUrl('responsable_edit', array('id' => $responsable->getId()))));
        $institucioOriginal = $responsable->getInstitucion()->getId();

        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted()) {
            if ($institucioOriginal != $responsable->getInstitucion()->getId())
                $this->tieneVehiculoTarjeta($form, $responsable->getId());

            if ($form->isValid()) {
                $tarjetas = $em->getRepository('App:Tarjeta')->findByResponsable($responsable);
                /*
                 * Como la llave foranea en realidad esta en tarjeta y no en responsable tengo que ir una a una a cada
                 * tarjeta y asignarle el responsable y antes de ello tengo que quitarle todas las tarjetas que tiene
                 * asignado
                 */
                foreach ($tarjetas as $value) {
                    if (!$responsable->getTarjetas()->contains($value)) {
                        $responsable->getTarjetas()->removeElement($value);
                        $value->setResponsable(null);
                        $em->persist($value);
                    } else
                        $responsable->getTarjetas()->removeElement($value);
                }

                foreach ($responsable->getTarjetas()->toArray() as $value) {
                    $value->setResponsable($responsable);
                    $em->persist($value);
                }

                $em->persist($responsable);
                $em->flush();
                return new JsonResponse(array('mensaje' => "El responsable fue actualizado satisfactoriamente",
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
        }

        return new JsonResponse([
            'html' => $this->renderView('responsable/new.html.twig', [
                'responsable' => $responsable,
                'form' => $form->createView(),
                'form_id' => 'responsable_edit',
                'action' => 'Actualizar',
                'title' => 'Editar responsable',
                'eliminable' => $this->esEliminable($responsable)
            ]),
            'responsable' => $responsable->getId()
        ]);
    }

    /**
     * @Route("/{id}/delete", name="responsable_delete", options={"expose"=true})
     */
    public function delete(Request $request, Responsable $responsable): Response
    {
        if (!$request->isXmlHttpRequest() || false == $this->esEliminable($responsable))
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('DELETE', $responsable);
        $em = $this->getDoctrine()->getManager();
        $em->remove($responsable);
        $em->flush();
        return new JsonResponse(array('mensaje' => 'El responsable fue eliminado satisfactoriamente'));
    }

    /*
     *Funcion que devuelve todos los responsables de una determinada institucion, se utiliza en el proceso de gestionar
     * vehiculos
     */
    /**
     * @Route("/{id}/findbyinstitucion", name="responsable_findbyinstitucion", options={"expose"=true})
     */
    public function findbyinstitucion(Request $request, Institucion $institucion)
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $vehiculo = $request->get('vehiculo');
        $responsable = null;
        if ($vehiculo != null) {
            $vehiculo = $em->getRepository('App:Vehiculo')->find($vehiculo);
            if(null!=$vehiculo->getResponsable())
                $responsable = $vehiculo->getResponsable()->getId();
        }

        $res = $em->createQueryBuilder('responsable');
        $res->select('r')->from('App:Responsable', 'r');
        $res->join('r.tarjetas', 't');
        $res->join('t.tipotarjeta', 'tt');
        $res->join('tt.institucion', 'i');
        $res->where('t.activo = TRUE AND r.activo= TRUE AND i.id = :institucion')->setParameter('institucion', $institucion->getId());
        $responsables = $res->getQuery()->getResult();

        $result = [];
        foreach ($responsables as $value)
            if ($value->getTarjetas()->count() == 1 || $$value->getId() == $responsable)
                $result[] = ['id' => $value->getId(), 'nombre' => $value->getNombre()];

        return new JsonResponse($result);
    }


    /*
     *FUNCIONALIDADES DE APOYO AL GESTIONAR
     * Esta funcionalidad garantiza que si se va a mover un responsable de institución el mismo no tenga en el proceso
     * de movimiento ninguna tarjeta o vehículo a su cargo
     */
    private function tieneVehiculoTarjeta(&$form, $responsable)
    {
        $em = $this->getDoctrine()->getManager();
        $consulta = $em->createQuery('SELECT t.id FROM App:Tarjeta t JOIN t.responsable r WHERE r.id= :id');
        $consulta->setParameter('id', $responsable);
        $consulta->setMaxResults(1);
        if (!empty($consulta->getResult()))
            $form->get('institucion')->addError(new  FormError("Para poder cambiar el responsable de institución antes debe quitarle las tarjetas asignadas"));
        else {
            $consulta = $em->createQuery('SELECT v.matricula FROM App:Vehiculo t JOIN v.responsable r WHERE r.id= :id');
            $consulta->setParameter('id', $responsable);
            $consulta->setMaxResults(1);
            $vehiculo_asignado = $consulta->getResult();
            if (!empty($vehiculo_asignado))
                $form->get('institucion')->addError(new  FormError("Para poder cambiar el responsable de institución antes debe quitarlo del vehiculo con matrícula " . $vehiculo_asignado[0]['matricula']));
        }
    }

    /*
     * Funcionalidad qie devuelve un boolean indicando si un responsable es eliminable o no
     */
    private function esEliminable(Responsable $responsable)
    {
        $em = $this->getDoctrine()->getManager();
        $entidades = ['Tarjeta', 'Vehiculo'];
        foreach ($entidades as $value) {
            $result = $em->getRepository("App:$value")->findOneByResponsable($responsable);
            if (null != $result)
                return false;
        }
        return true;
    }


}
