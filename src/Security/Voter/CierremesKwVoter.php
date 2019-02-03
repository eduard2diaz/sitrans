<?php

namespace App\Security\Voter;

use App\Entity\CierremesKw;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CierremesKwVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['DELETE', 'VIEW']) && $subject instanceof CierremesKw;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'DELETE':
            case 'VIEW':
                return $subject->getInstitucion()->getId()==$token->getUser()->getInstitucion()->getId();
                break;
        }

        return false;
    }
}
