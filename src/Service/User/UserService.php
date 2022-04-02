<?php

namespace App\Service\User;

use ErrorException;
use App\Entity\User;

class UserService
{

    public function ressourceRightsGetUser(User $user,User $userRequest)
    {
        try {
            if (in_array(User::ROLE_ADMIN, $user->getRoles())) {
                return true;
            }
            if ($user->getCompany()->getId() !== $userRequest->getCompany()->getId()) {
                throw new ErrorException("contact the administrator, your rights are not sufficient");
            }
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage());
        }
    }
}
