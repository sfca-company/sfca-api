<?php

namespace App\Service\Company;

use ErrorException;
use App\Entity\User;
use App\Entity\Company\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class CompanyService
{


    private $em;

    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }

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

    public function ressourceRightsGetCompany(User $user, Company $company) :?JsonResponse
    {
        try {
            if (in_array(User::ROLE_ADMIN, $user->getRoles())) {
                return null;
            }
            if(empty($user->getCompany()) ){
                return new JsonResponse([
                    'Exception'=>"contact the administrator, not company associed",
                    "code"=>Response::HTTP_BAD_REQUEST
                ]);
            }
            if ($user->getCompany()->getId() !== $company->getId()) {
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
