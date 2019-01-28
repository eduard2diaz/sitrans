<?php

namespace App\Controller;

use App\Entity\Tipovehiculo;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Usuario;
use App\Form\UsuarioType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/usuario")
 */
class UsuarioController extends Controller
{
    /**
     * @Route("/", name="usuario_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $usuarios=$this->get('institucion.service')->obtenerUsuariosSubordinados();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($usuarios),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $usuarios,
                ]
                );
        }

        return $this->render('usuario/index.html.twig');
    }

    /**
     * @Route("/new", name="usuario_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $usuario = new Usuario();
        if($this->isGranted('ROLE_ADMIN'))
            $usuario->setInstitucion($this->getUser()->getInstitucion());

        $form = $this->createForm(UsuarioType::class, $usuario, array('action' => $this->generateUrl('usuario_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($usuario);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El usuario fue registrado satisfactoriamente",
                    'nombre' => $usuario->getNombre(),
                    'apellidos' => $usuario->getApellidos(),
                    'activo' => $usuario->getActivo(),
                    'id' => $usuario->getId(),
                ));
            } else {
                $page = $this->renderView('usuario/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('usuario/new.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/show", name="usuario_show", methods="GET",options={"expose"=true})
     */
    public function show(Request $request, Usuario $usuario): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW',$usuario);
        return $this->render('usuario/_show.html.twig',['usuario'=>$usuario]);
    }

    /**
     * @Route("/{id}/edit", name="usuario_edit", methods="GET|POST",options={"expose"=true})
    */
    public function edit(Request $request, Usuario $usuario): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('EDIT',$usuario);
        $form = $this->createForm(UsuarioType::class, $usuario, array('action' => $this->generateUrl('usuario_edit', array('id' => $usuario->getId()))));
        $passwordOriginal = $form->getData()->getPassword();
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted())
            if ($form->isValid()) {
                if (null == $usuario->getPassword())
                    $usuario->setPassword($passwordOriginal);
                else
                    $usuario->setPassword($this->get('security.password_encoder')->encodePassword($usuario,$usuario->getPassword()));
                $em->persist($usuario);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El usuario fue actualizado satisfactoriamente",
                    'nombre' => $usuario->getNombre(),
                    'apellidos' => $usuario->getApellidos(),
                    'activo' => $usuario->getActivo(),
                    'id' => $usuario->getId(),
                ));
            } else {
                $page = $this->renderView('usuario/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'usuario_edit',
                    'action' => 'Actualizar'
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('usuario/new.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView(),
            'form_id' => 'usuario_edit',
            'action' => 'Actualizar',
            'title' => 'Editar usuario',
            'eliminable'=>$usuario->getId()!=$this->getUser()->getId() ? $this->esEliminable($usuario) : false
        ]);
    }

    /**
     * @Route("/{id}/delete", name="usuario_delete", options={"expose"=true})
     */
    public function delete(Request $request, Usuario $usuario): Response
    {
        if (!$request->isXmlHttpRequest() || $usuario->getId()==$this->getUser()->getId() || false==$this->esEliminable($usuario))
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('DELETE',$usuario);
        $em = $this->getDoctrine()->getManager();
        $em->remove($usuario);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El usuario fue eliminado satisfactoriamente'));
    }

    /*
     * Funcion que retorna un booleano true o false indicando si un usuario puede o no ser eliminado teniendo en cuenta
     * si el ha realizado o no operaciones en el sistema
     */
    private function esEliminable(Usuario $usuario){
        $em=$this->getDoctrine()->getManager();
        $entidades=['AjusteTarjeta','Hojaruta','LecturaReloj','PlanefectivoCuenta','CierreMesTarjeta','CierremesArea','PlanportadoresArea'];
        foreach ($entidades as $value){
            $objeto=$em->getRepository("App:$value")->findOneBy(['usuario'=>$usuario]);
            if(null!=$objeto)
                return false;
        }
        return true;
    }
}
