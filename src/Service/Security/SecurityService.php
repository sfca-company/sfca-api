<?php

namespace App\Service\Security;

use ErrorException;
use App\Entity\User;

class SecurityService
{

    /**
     * Permet de vérifier si l'user à le droit admin
     *
     * @param User $user
     * @return void
     */
    public function ressourceRightsAdmin(User $user)
    {
        try {
            if (!in_array(User::ROLE_ADMIN, $user->getRoles())) {
                throw new ErrorException("contact the administrator, your rights are not sufficient");
            }
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage());
        }
    }
}
