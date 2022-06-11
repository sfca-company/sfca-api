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
     * @return JsonResponse|null
     */
    public function ressourceRightsAdmin(User $user) :?JsonResponse
    {
        try {
            if (!in_array(User::ROLE_ADMIN, $user->getRoles())) {
                return new JsonResponse([
                    'exception'=>"contact the administrator, your rights are not sufficient",
                    'errors'=>['user'=>"admin only"],
                    "code"=>Response::HTTP_BAD_REQUEST
                ]);
            }
            return null;
        } catch (\Exception $e) {
            return new JsonResponse([
                'exception'=>$e->getMessage(),
                "code"=>Response::HTTP_BAD_REQUEST
            ]);
        }
    }

    /**
     *  Si l'utilisateur est un prospect alors il n'a pas accès à la ressource
     *
     * @param User $user
     * @return JsonResponse|null
     */
    public function forbiddenProspect(User $user) :?JsonResponse
    {
        try {
            if (in_array(User::ROLE_PROSPECT, $user->getRoles())) {
                return new JsonResponse([
                    'exception'=>"contact the administrator, your rights are not sufficient",
                    'errors'=>['user'=>"forbiddenProspect"],
                    "code"=>Response::HTTP_BAD_REQUEST
                ]);
            }
            return null;
        } catch (\Exception $e) {
            return new JsonResponse([
                'exception'=>$e->getMessage(),
                "code"=>Response::HTTP_BAD_REQUEST
            ]);
        }
    }
}
