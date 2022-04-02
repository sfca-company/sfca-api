<?php

namespace App\Service\Security;

use ErrorException;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class SecurityService
{

    /**
     * Permet de vérifier si l'user à le droit admin
     *
     * @param User $user
     * @return void
     */
    public function ressourceRightsAdmin(User $user) :?JsonResponse
    {
        try {
            if (!in_array(User::ROLE_ADMIN, $user->getRoles())) {
                return new JsonResponse([
                    'Exception'=>"contact the administrator, your rights are not sufficient",
                    "code"=>Response::HTTP_BAD_REQUEST
                ]);
            }
        } catch (\Exception $e) {
            return new JsonResponse([
                'Exception'=>$e->getMessage(),
                "code"=>Response::HTTP_BAD_REQUEST
            ]);
        }
    }
}
