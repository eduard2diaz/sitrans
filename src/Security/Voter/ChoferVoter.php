<?php

namespace App\Security\Voter;

use App\Entity\Chofer;
use App\Tools\InstitucionService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class ChoferVoter extends Voter
{

    private $decisionManager;
    private $institucion_service;

    public function __construct(AccessDecisionManagerInterface $decisionManager,InstitucionService $institucion_service) {
        $this->decisionManager = $decisionManager;
        $this->institucion_service = $institucion_service;
    }


    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['EDIT', 'VIEW','DELETE']) && $subject instanceof Chofer;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        $hijas=$this->institucion_service->obtenerKeyInstitucionesHijas($token->getUser()->getInstitucion()->getId());
        $hijas[]=$token->getUser()->getInstitucion()->getId();
        switch ($attribute) {
            case 'EDIT':
            case 'DELETE':
            case 'VIEW':
            return $this->decisionManager->decide($token, array('ROLE_JEFETRANSPORTE')) && in_array($subject->getInstitucion()->getId(),$hijas);
                break;
        }

        return false;
    }
}
