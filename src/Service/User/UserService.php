<?php

namespace App\Service\User;

use ErrorException;
use App\Entity\User;
use App\Service\Adress\AdressService;
use App\Service\PhoneNumber\PhoneNumberService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserService
{
    private $adressService;
    private $phoneNumberService;
    const ROLE_PROSPECT = "ROLE_PROSPECT";
    const ROLE_ADMIN = "ROLE_ADMIN";
    const ROLE_CLIENT = "ROLE_CLIENT";

    const ACCEESS_ALL = 1; // Permet d'avoir tous les droits
    const ACCEESS_UPDATE = 2; // Permet d'avoir le READ + CREATE + UPDATE
    const ACCEESS_CREATE = 3; // Permet d'avoir le READ + CREATE
    const ACCEESS_READ = 4; // Permet d'avoir le read

    const ARRAY_ROLES = [UserService::ROLE_PROSPECT, UserService::ROLE_ADMIN, UserService::ROLE_CLIENT];
    const ARRAY_ACCEESS = [UserService::ACCEESS_ALL, UserService::ACCEESS_UPDATE, UserService::ACCEESS_CREATE, UserService::ACCEESS_READ];
    public function __construct(
        AdressService $adressService,
        PhoneNumberService $phoneNumberService
    ) {
        $this->adressService = $adressService;
        $this->phoneNumberService = $phoneNumberService;
    }
    public function ressourceRightsGetUser(User $user, User $userRequest): ?JsonResponse
    {
        try {
            // Si l'utilisateur est un admin alors pas besoin de controler 
            if (in_array(User::ROLE_ADMIN, $user->getRoles())) {
                return null;
            }
            $companiesUserConnect = $user->getCompanies();
            $compagniesUserRequest = $userRequest->getCompanies();
            $checkCompaniesUserConnectToUserRequest = false;

            if (empty($companiesUserConnect)) {
                // Si l'utilisateur connecté n'as pas de compagnies alors on controle si il requete uniquement son user
                if ($user->getId() !== $userRequest->getId()) {
                    return new JsonResponse([
                        'exception' => "contact the administrator, your rights are not sufficient",
                        'errors' => ["user" => "user !== user request"],
                        "code" => Response::HTTP_BAD_REQUEST
                    ]);
                } else {
                    return null;
                }
            }

            if (!empty($companiesUserConnect) && !empty($compagniesUserRequest)) {
                // On match si l'utilisateur connecté à la même compagnies que l'utilisateur qu'il requête
                foreach ($compagniesUserRequest as $companyUserRequest) {
                    foreach ($companiesUserConnect as $companyUserConnect) {
                        if ($checkCompaniesUserConnectToUserRequest === true) {
                            break;
                        }
                        if ($companyUserConnect === $companyUserRequest) {
                            $checkCompaniesUserConnectToUserRequest = true;
                            break;
                        }
                    }
                }
            }
            // Si l'utilisateur connecté ne possede pas les meme companies que l'utilisateur requete alors errors
            if ($checkCompaniesUserConnectToUserRequest === false) {

                return new JsonResponse([
                    'exception' => "contact the administrator, your rights are not sufficient",
                    'errors' => ['user' => "checkCompaniesUserConnectToUserRequest is false"],
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


    /**
     * Retourne uniquement les roles admis
     *
     * @param array $roles
     * @return array
     */
    public function validatorRoles(array $roles): array
    {
        $acceptedRoles = [];

        foreach ($roles as $role) {
            if (in_array($role, UserService::ARRAY_ROLES)) {
                $acceptedRoles[] = $role;
            }
        }
        return $acceptedRoles;
    }

    public function addNonMandatoryAttribute(array $body, User $user): User
    {
        if (array_key_exists("firstName", $body)) {
            $user->setFirstName($body["firstName"]);
        }
        if (array_key_exists("lastName", $body)) {
            $user->setLastName($body["lastName"]);
        }
        if (array_key_exists("dateOfBith", $body)) {
            $user->setDateOfBith(new \Datetime($body["dateOfBith"]));
        }
        if (array_key_exists("profession", $body)) {
            $user->setProfession($body["profession"]);
        }
        if (array_key_exists("notes", $body)) {
            $user->setNotes($body["notes"]);
        }
        $adress = $this->adressService->create($body);
        if (!empty($adress)) {
            $user->setAdress($adress);
        }
        $phoneNumber = $this->phoneNumberService->create($body);
        if (!empty($phoneNumber)) {
            $user->setPhoneNumberFavorite($phoneNumber);
        }
        $phoneNumbers = $user->getPhoneNumbers();
        foreach ($phoneNumbers as $number) {
            $user->removePhoneNumber($number);
        }
        $phoneNumbers = $this->phoneNumberService->updateMultiple($body, $user);
        if (!empty($phoneNumbers)) {
            foreach ($phoneNumbers as $phoneNumber) {
                $user->addPhoneNumber($phoneNumber);
            }
        }
        return $user;
    }

    public function getRoles(): array
    {
        return UserService::ARRAY_ROLES;
    }

    public function getAcceess(): array
    {
        return UserService::ARRAY_ACCEESS;
    }
}
