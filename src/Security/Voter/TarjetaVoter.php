<?php

namespace App\Security\Voter;

use App\Entity\Tarjeta;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TarjetaVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['EDIT', 'VIEW', 'DELETE']) && $subject instanceof Tarjeta;
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
                    return $token->getUser()->getInstitucion()->getId()==$subject->getTipotarjeta()->getInstitucion()->getId();
                break;
        }

        return false;
    }
}
