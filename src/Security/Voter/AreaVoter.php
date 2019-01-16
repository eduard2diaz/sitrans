<?php

namespace App\Security\Voter;

use App\Entity\Area;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AreaVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['VIEW','EDIT', 'DELETE'])  && $subject instanceof Area;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'DELETE':
            case 'EDIT':
            case 'VIEW':
                return $user->getInstitucion()->getId()==$subject->getCcosto()->getCuenta()->getInstitucion()->getId();
                break;
        }

        return false;
    }
}
