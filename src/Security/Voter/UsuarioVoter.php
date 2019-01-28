<?php

namespace App\Security\Voter;

use App\Entity\Usuario;
use App\Tools\InstitucionService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class UsuarioVoter extends Voter
{
    private $decisionManager;
    private $institucion_service;

    public function __construct(AccessDecisionManagerInterface $decisionManager,InstitucionService $institucion_service) {
        $this->decisionManager = $decisionManager;
        $this->institucion_service = $institucion_service;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['EDIT', 'VIEW','DELETE'])  && $subject instanceof Usuario;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        $institucion=$token->getUser()->getInstitucion() ? $token->getUser()->getInstitucion()->getId() : null;
        $hijas=$this->institucion_service->obtenerKeyInstitucionesHijas($institucion);

        if($this->decisionManager->decide($token, array('ROLE_ADMIN')))
            $hijas[]=$token->getUser()->getInstitucion()->getId();

        switch ($attribute) {
            case 'EDIT':
            case 'VIEW':
                return $subject->getId()==$token->getUser()->getId() || $this->decisionManager->decide($token, array('ROLE_SUPERADMIN')) || ($this->decisionManager->decide($token, array('ROLE_ADMIN')) && in_array($subject->getInstitucion()->getId(),$hijas));
            break;
            case 'DELETE':
                return $subject->getId()!=$token->getUser()->getId() && ($this->decisionManager->decide($token, array('ROLE_SUPERADMIN')) || ($this->decisionManager->decide($token, array('ROLE_ADMIN')) && in_array($subject->getInstitucion()->getId(),$hijas)));
            break;
        }

        return false;
    }
}
