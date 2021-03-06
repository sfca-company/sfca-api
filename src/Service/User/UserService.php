<?php

namespace App\Service\User;

use ErrorException;
use App\Entity\User;
use App\Service\Address\AddressService;
use App\Service\PhoneNumber\PhoneNumberService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserService
{
    private $addressService;
    private $phoneNumberService;
    const ROLE_PROSPECT = "ROLE_PROSPECT";
    const ROLE_ADMIN = "ROLE_ADMIN";
    const ROLE_CLIENT = "ROLE_CLIENT";

    const ACCESS_ALL = 1; // Permet d'avoir tous les droits
    const ACCESS_UPDATE = 2; // Permet d'avoir le READ + CREATE + UPDATE
    const ACCESS_CREATE = 3; // Permet d'avoir le READ + CREATE
    const ACCESS_READ = 4; // Permet d'avoir le read

    const ARRAY_ROLES = [UserService::ROLE_PROSPECT, UserService::ROLE_ADMIN, UserService::ROLE_CLIENT];
    const ARRAY_ACCESS = [UserService::ACCESS_ALL, UserService::ACCESS_UPDATE, UserService::ACCESS_CREATE, UserService::ACCESS_READ];
    public function __construct(
        AddressService $addressService,
        PhoneNumberService $phoneNumberService
    ) {
        $this->addressService = $addressService;
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


    /**
     *  Permet d'ajouter des champs non obligatoire sur l'entité User
     *
     * @param array $body
     * @param User $user
     * @param string|null $method HTTP
     * @return User
     */
    public function addNonMandatoryAttribute(array $body, User $user, ?string $method = null): User
    {
        if (array_key_exists("firstName", $body)) {
            $user->setFirstName($body["firstName"]);
        }
        if (array_key_exists("lastName", $body)) {
            $user->setLastName($body["lastName"]);
        }
        if (array_key_exists("dateOfBirth", $body)) {
            $user->setDateOfBirth(new \Datetime($body["dateOfBirth"]));
        }
        if (array_key_exists("profession", $body)) {
            $user->setProfession($body["profession"]);
        }
        if (array_key_exists("notes", $body)) {
            $user->setNotes($body["notes"]);
        }
        $address = $this->addressService->create($body);
        if (!empty($address)) {
            $user->setAddress($address);
        }
        if ($method !== "POST") {
            $phoneNumber = $this->phoneNumberService->update($body);
            if (!empty($phoneNumber)) {
                $user->setPhoneNumberFavorite($phoneNumber);
            }
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
            if ($method === "POST") {
                $user->setPhoneNumberFavorite($phoneNumber);
            }
        }
        return $user;
    }

    public function getRoles(): array
    {
        return UserService::ARRAY_ROLES;
    }

    public function getAccess(): array
    {
        return UserService::ARRAY_ACCESS;
    }
}
