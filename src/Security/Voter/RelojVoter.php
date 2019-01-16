<?php

namespace App\Security\Voter;

use App\Entity\Reloj;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class RelojVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['EDIT', 'VIEW','DELETE'])  && $subject instanceof Reloj;
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
            return $user->getInstitucion()->getId()==$subject->getArea()->getCcosto()->getCuenta()->getInstitucion()->getId();
                break;
        }

        return false;
    }
}
