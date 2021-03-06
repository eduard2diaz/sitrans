<?php

namespace App\Security\Voter;

use App\Entity\Chip;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ChipVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['DELETE', 'VIEW']) && $subject instanceof Chip;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'DELETE':
            case 'VIEW':
                return $token->getUser()->getInstitucion()->getId()==$subject->getTarjeta()->getTipotarjeta()->getInstitucion()->getId();
            break;
        }

        return false;
    }
}
