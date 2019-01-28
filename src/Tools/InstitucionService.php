<?php

namespace App\Tools;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class InstitucionService
{
    private $authorizationChecker;
    private $token;
    private $doctrine;

    public function __construct(TokenStorageInterface $token, AuthorizationCheckerInterface $authorizationChecker, ManagerRegistry $doctrine)
    {
        $this->token = $token;
        $this->authorizationChecker = $authorizationChecker;
        $this->doctrine = $doctrine;
    }

    /**
     * @return ManagerRegistry
     */
    public function getDoctrine(): ManagerRegistry
    {
        return $this->doctrine;
    }

    /*
     *Devuelve el arbol jerarquico de instituciones a partir de una institución padre
     */
    public function obtenerArbolInstitucional($institucion = null)
    {
        $em = $this->getDoctrine()->getManager();
        if (null == $institucion)
            if ($this->authorizationChecker->isGranted('ROLE_SUPERADMIN'))
                return $em->getRepository('App:Institucion')->findAll();
            elseif (null != $this->token->getToken()->getUser()->getInstitucion())
                $institucion = $this->token->getToken()->getUser()->getInstitucion()->getId();

        $obj = $em->getRepository('App:Institucion')->find($institucion);
        if (!$obj)
            throw new \Exception('La institución seleccionada no existe');

        $array = [$obj];
        $hijas = $this->obtenerInstitucionesHijas($institucion);
        return array_merge($array, $hijas);
    }

    /*
     *Devuelve las instituciones hijas d euna determinado institución
     */
    public function obtenerInstitucionesHijas($institucion)
    {
        $keys = $this->obtenerKeyInstitucionesHijas($institucion);
        $em = $this->getDoctrine()->getManager();
        return $em->createQuery('SELECT i FROM App:Institucion i WHERE i.id IN (:lista)')->setParameter('lista', $keys)->getResult();
    }

    /*
     *Devuelve las llaves primarias(id) de las instituciones hijas d euna determinada institución
     */
    public function obtenerKeyInstitucionesHijas($institucion)
    {
        $array = array();
        $em = $this->getDoctrine()->getManager();

        $instituciones = $em->createQuery('SELECT i.id FROM App:Institucion i JOIN i.institucionpadre p WHERE p.id= :padre')->setParameter('padre', $institucion)->getResult();
        foreach ($instituciones as $value) {
            $array[] = $value['id'];
            $array = array_merge($array, $this->obtenerKeyInstitucionesHijas($value['id']));

        }
        return $array;
    }


    /*
     * Función que realiza la búsqueda a partir del usuario indicado o el actual respectivamente
     * y devuelve el listado de usuarios subordinados
     */
    public function obtenerUsuariosSubordinados($myself = null)
    {
        $em = $this->getDoctrine()->getManager();
        if (null == $myself)
            $myself = $this->token->getToken()->getUser()->getId();

        $obj = $em->getRepository('App:Usuario')->find($myself);
        if (!$obj)
            throw new \Exception('Usuario indicado no existe');

        $roles = $obj->getRoles();
        if (!in_array('ROLE_SUPERADMIN', $roles) && !in_array('ROLE_ADMIN', $roles))
            throw new \Exception('El usuario indicado no tiene permisos administrativos');

        if (in_array('ROLE_SUPERADMIN', $roles)) {
            $consulta = $em->createQuery('SELECT u.id, u.nombre, u.apellidos, u.activo FROM App:Usuario u WHERE u.id<> :id');
            $consulta->setParameter('id', $myself);
        } elseif (null == $obj->getInstitucion())
            throw new \Exception('El usuario indicado no posee institución');
        else {
            $mi_institucion = $obj->getInstitucion()->getId();
            $instituciones = $this->obtenerKeyInstitucionesHijas($mi_institucion);
            $instituciones[] = $mi_institucion;
            $consulta = $em->createQuery('SELECT u.id, u.nombre, u.apellidos, u.activo FROM App:Usuario u JOIN u.institucion i WHERE u.id<> :id AND i.id IN (:institucion)');
            $consulta->setParameters(['id' => $myself, 'institucion' => $instituciones]);
        }

        return $consulta->getResult();
    }

    /*
    * Función que realiza la búsqueda a partir del usuario indicado o el actual respectivamente
    * y devuelve el listado de choferes subordinados
    */
    public function obtenerChoferesSubordinados($usuario = null)
    {
        $em = $this->getDoctrine()->getManager();

        if (null == $usuario)
            $usuario = $this->token->getToken()->getUser()->getId();

        $obj = $em->getRepository('App:Usuario')->find($usuario);
        if (!$obj)
            throw new \Exception('Usuario indicado no existe');

        $roles = $obj->getRoles();
        if (!in_array('ROLE_ADMIN', $roles) && !in_array('ROLE_JEFETRANSPORTE', $roles))
            throw new \Exception('El usuario indicado no tiene permisos administrativos');

        elseif (null == $obj->getInstitucion())
            throw new \Exception('El usuario indicado no posee institución');
        else {
            $mi_institucion = $obj->getInstitucion()->getId();
            $instituciones = $this->obtenerKeyInstitucionesHijas($mi_institucion);
            $instituciones[] = $mi_institucion;
            $consulta = $em->createQuery('SELECT ch.id, ch.nombre, ch.apellido, ch.ci FROM App:Chofer ch JOIN ch.institucion i WHERE i.id IN (:institucion)');
            $consulta->setParameters(['institucion' => $instituciones]);
        }

        return $consulta->getResult();
    }

    /*
     * Función que realiza la búsqueda a partir del usuario indicado o el actual respectivamente
     * y devuelve el listado de responsables subordinados
     */
    public function obtenerResponsablesSubordinados($usuario = null)
    {
        $em = $this->getDoctrine()->getManager();

        if (null == $usuario)
            $usuario = $this->token->getToken()->getUser()->getId();

        $obj = $em->getRepository('App:Usuario')->find($usuario);
        if (!$obj)
            throw new \Exception('Usuario indicado no existe');

        $roles = $obj->getRoles();
        if (!in_array('ROLE_ADMIN', $roles) && !in_array('ROLE_JEFETRANSPORTE', $roles))
            throw new \Exception('El usuario indicado no tiene permisos administrativos');

        elseif (null == $obj->getInstitucion())
            throw new \Exception('El usuario indicado no posee institución');
        else {
            $mi_institucion = $obj->getInstitucion()->getId();
            $instituciones = $this->obtenerKeyInstitucionesHijas($mi_institucion);
            $instituciones[] = $mi_institucion;
            $consulta = $em->createQuery('SELECT r.id, r.nombre, r.apellidos, r.ci, a.nombre as area FROM App:Responsable r JOIN r.area a JOIN r.institucion i WHERE i.id IN (:institucion)');
            $consulta->setParameters(['institucion' => $instituciones]);
        }

        return $consulta->getResult();
    }


    /*
     * Función que realiza la búsqueda a partir del usuario indicado o el actual respectivamente
     * y devuelve el listado de vehiculos subordinados
     */
    public function obtenerVehiculosSubordinados($usuario = null)
    {
        $em = $this->getDoctrine()->getManager();

        if (null == $usuario)
            $usuario = $this->token->getToken()->getUser()->getId();

        $obj = $em->getRepository('App:Usuario')->find($usuario);
        if (!$obj)
            throw new \Exception('Usuario indicado no existe');

        $roles = $obj->getRoles();
        if (!in_array('ROLE_ADMIN', $roles) && !in_array('ROLE_JEFETRANSPORTE', $roles))
            throw new \Exception('El usuario indicado no tiene permisos administrativos');

        elseif (null == $obj->getInstitucion())
            throw new \Exception('El usuario indicado no posee institución');

        $mi_institucion = $obj->getInstitucion()->getId();
        $instituciones = $this->obtenerKeyInstitucionesHijas($mi_institucion);
        $instituciones[] = $mi_institucion;
        $consulta = $em->createQuery('SELECT v.id, v.matricula , v.marca, tc.nombre as tipocombustible FROM App:Vehiculo v JOIN v.tipocombustible tc JOIN v.institucion i  WHERE i.id IN (:institucion)');
        $consulta->setParameters(['institucion' => $instituciones]);

        return $consulta->getResult();
    }

}