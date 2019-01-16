<?php

namespace App\Security\Voter;

use App\Entity\Elemento;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ElementoVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['EDIT', 'DELETE'])  && $subject instanceof Elemento;
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
                return $user->getInstitucion()->getId()==$subject->getPartida()->getCuenta()->getInstitucion()->getId();
                break;
        }

        return false;
    }
}
