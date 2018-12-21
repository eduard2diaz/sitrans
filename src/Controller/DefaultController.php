<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="index", methods="GET|POST")
     */
    public function index(Request $request)
    {
        if($this->isGranted('IS_AUTHENTICATED_FULLY'))
            return $this->redirectToRoute($this->targetPath());

        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('default/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/estatica/{page}", name="estatica", methods="GET")
     */
    public function estatica($page)
    {
        return $this->render('default/static/'.$page.'.html.twig');
    }

    private function targetPath(){
        $path=null;
        if($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_SUPER'))
            $path='usuario_index';
        elseif($this->isGranted('ROLE_JEFETRANSPORTE'))
            $path='hojaruta_index';
        elseif($this->isGranted('ROLE_CAJERO'))
            $path='chip_index';
        elseif($this->isGranted('ROLE_ELECTRICIDAD'))
            $path='lecturareloj_index';
        else
            throw $this->createAccessDeniedException();

        return $path;
    }
}
