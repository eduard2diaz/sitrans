<?php

namespace App\Security\Voter;

use App\Entity\Reparacion;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class ReparacionVoter extends Voter
{

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager) {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['EDIT', 'VIEW', 'DELETE']) && $subject instanceof Reparacion;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }


        switch ($attribute) {
            case 'EDIT':
            case 'VIEW':
            case 'DELETE':
                return $this->decisionManager->decide($token, array('ROLE_JEFETRANSPORTE')) && $subject->getInstitucion()->getId()==$token->getUser()->getInstitucion()->getId();
                break;
        }

        return false;
    }
}
