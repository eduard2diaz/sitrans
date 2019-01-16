<?php

namespace App\Security\Voter;

use App\Entity\Cuenta;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CuentaVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['EDIT', 'DELETE'])  && $subject instanceof Cuenta;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'EDIT':
            case 'DELETE':
                return $user->getInstitucion()->getId()==$subject->getInstitucion()->getId();
                break;
        }

        return false;
    }
}
