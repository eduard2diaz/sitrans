<?php

namespace App\Controller;

use App\Entity\Somaton;
use App\Form\SomatonType;
use App\Repository\SomatonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/somaton")
 */
class SomatonController extends Controller
{
    /**
     * @Route("/", name="somaton_index", methods="GET")
     */
    public function index(SomatonRepository $somatonRepository): Response
    {
        return $this->render('somaton/index.html.twig', ['somatons' => $somatonRepository->findAll()]);
    }

    /**
     * @Route("/new", name="somaton_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $somaton = new Somaton();
        $form = $this->createForm(SomatonType::class, $somaton);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($somaton);
            $em->flush();

            return $this->redirectToRoute('somaton_index');
        }

        return $this->render('somaton/new.html.twig', [
            'somaton' => $somaton,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="somaton_show", methods="GET")
     */
    public function show(Somaton $somaton): Response
    {
        return $this->render('somaton/show.html.twig', ['somaton' => $somaton]);
    }

    /**
     * @Route("/{id}/edit", name="somaton_edit", methods="GET|POST")
     */
    public function edit(Request $request, Somaton $somaton): Response
    {
        $form = $this->createForm(SomatonType::class, $somaton);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('somaton_edit', ['id' => $somaton->getId()]);
        }

        return $this->render('somaton/edit.html.twig', [
            'somaton' => $somaton,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="somaton_delete", methods="DELETE")
     */
    public function delete(Request $request, Somaton $somaton): Response
    {
        if ($this->isCsrfTokenValid('delete'.$somaton->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($somaton);
            $em->flush();
        }

        return $this->redirectToRoute('somaton_index');
    }
}
