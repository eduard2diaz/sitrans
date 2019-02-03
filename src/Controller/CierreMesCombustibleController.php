<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\CierreMesCombustible;
use App\Form\CierreMesCombustibleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cierremescombustible")
 */
class CierreMesCombustibleController extends Controller
{
    /**
     * @Route("/", name="cierremescombustible_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if($request->isXmlHttpRequest()) {
            $cierremescombustibles = $this->getDoctrine()->getManager()->createQuery('SELECT c.id , c.anno, c.mes FROM App:CierreMesCombustible c join c.institucion i WHERE i.id= :id')->setParameter('id',$this->getUser()->getInstitucion()->getId())->getResult();
            return new JsonResponse(
                $result = [
                    'iTotalRecords'        => count($cierremescombustibles),
                    'iTotalDisplayRecords' => 10,
                    'sEcho'                => 0,
                    'sColumns'             => '',
                    'aaData'               => $cierremescombustibles,
                ]
                );
        }

        return $this->render('cierremescombustible/index.html.twig');
    }

    /**
     * @Route("/new", name="cierremescombustible_new", methods="GET|POST",options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $cierremescombustible = new CierreMesCombustible();
        $cierremescombustible->setInstitucion($this->getUser()->getInstitucion());
        $form = $this->createForm(CierreMesCombustibleType::class, $cierremescombustible, array('action' => $this->generateUrl('cierremescombustible_new')));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($cierremescombustible);
                $em->flush();
                return new JsonResponse(array('mensaje' =>"El cierre fue registrado satisfactoriamente",
                    'anno' => $cierremescombustible->getAnno(),
                    'mes' => $cierremescombustible->getMes(),
                    'id' => $cierremescombustible->getId(),
                ));
            } else {
                $page = $this->renderView('cierremescombustible/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('cierremescombustible/new.html.twig', [
            'cierremescombustible' => $cierremescombustible,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/show", name="cierremescombustible_show", methods="GET|POST",options={"expose"=true})
     */
    public function show(Request $request, CierreMesCombustible $cierremescombustible): Response
    {
        $this->denyAccessUnlessGranted('VIEW',$cierremescombustible);
        return $this->render('cierremescombustible/show.html.twig', [
            'cierremescombustible' => $cierremescombustible,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="cierremescombustible_delete", options={"expose"=true})
     */
    public function delete(Request $request, CierreMesCombustible $cierremescombustible): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('DELETE',$cierremescombustible);
        $em = $this->getDoctrine()->getManager();
        $em->remove($cierremescombustible);
        $em->flush();
        return new JsonResponse(array('mensaje' =>'El cierre fue eliminado satisfactoriamente'));
    }
}
