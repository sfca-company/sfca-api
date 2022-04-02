<?php

namespace App\Service\User;

use ErrorException;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserService
{

    public function ressourceRightsGetUser(User $user,User $userRequest): ?JsonResponse
    {
        try {
            if (in_array(User::ROLE_ADMIN, $user->getRoles())) {
                return null;
            }
            if ($user->getCompany()->getId() !== $userRequest->getCompany()->getId()) {
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
