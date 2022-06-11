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

    public function ressourceRightsAdmin(User $user): ?JsonResponse
    {
        try {
            if (!in_array(User::ROLE_ADMIN, $user->getRoles())) {
                return new JsonResponse([
                    'Exception' => "contact the administrator, your rights are not sufficient",
                    "code" => Response::HTTP_BAD_REQUEST
                ]);
            }
        } catch (\Exception $e) {
            return new JsonResponse([
                'Exception' => $e->getMessage(),
                "code" => Response::HTTP_BAD_REQUEST
            ]);
        }
    }

    /**
     * 
     *
     * @param User $user
     * @param Company $company
     * @return JsonResponse|null
     */
    public function ressourceRightsGetCompany(User $user, Company $company): ?JsonResponse
    {
        try {
            if (in_array(User::ROLE_ADMIN, $user->getRoles())) {
                return null;
            }
            $companiesUserConnect = $user->getCompanies();
            $checkCompaniesUserConnectToCompanyRequest = false;
            // si l'utilisateur n'as pas de company
            if (empty($companiesUserConnect)) {
                return new JsonResponse([
                    'exception' => "contact the administrator, not company associed",
                    'errors' => ["compagnies" => "user connect not have companies"],
                    "code" => Response::HTTP_BAD_REQUEST
                ]);
            }
            // On check si l'utilisateur connecté possède la company qu'il requete
            foreach ($companiesUserConnect as $companyUserConnect) {
                if ($checkCompaniesUserConnectToCompanyRequest === true) {
                    break;
                }
                if ($companyUserConnect === $company) {
                    $checkCompaniesUserConnectToCompanyRequest = true;
                }
            }
            if ($checkCompaniesUserConnectToCompanyRequest === false) {
                return new JsonResponse([
                    'exception' => "contact the administrator, your rights are not sufficient",
                    'errors' => ['user' => "checkCompaniesUserConnectToCompanyRequest is false"],
                    "code" => Response::HTTP_BAD_REQUEST
                ]);
            }
            return null;
        } catch (\Exception $e) {
            return new JsonResponse([
                'exception' => $e->getMessage(),
                "code" => Response::HTTP_BAD_REQUEST
            ]);
        }
    }
}
