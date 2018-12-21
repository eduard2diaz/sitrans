<?php
/**
 * Created by PhpStorm.
 * User: Eduardo
 * Date: 6/7/2018
 * Time: 05:58
 */

namespace App\Security;

use App\Entity\Usuario;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof Usuario) {
            return;
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof Usuario) {
            return;
        }

        if (false==$user->getActivo()) {
            throw new AccountExpiredException('Su cuenta no está activa');
        }
    }

}