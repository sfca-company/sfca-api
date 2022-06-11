<?php

namespace App\Service\Contact;

use ErrorException;
use App\Entity\User;
use App\Entity\Contact\Contact;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ContactService
{

    /**
     * Permet de controler si un user à le droit de requeter un contact
     *
     * @param User $user
     * @param Contact $contact
     * @return @return JsonResponse|null
     */
    public function ressourceRightsGetContact(User $user,Contact $contact) :?JsonResponse
    {
        try {
            if (in_array(User::ROLE_ADMIN, $user->getRoles())) {
                return null;
            }
            $companiesUserConnect = $user->getCompanies();
            $checkCompaniesUserConnectToContactRequest = false;
            $company = $contact->getCompany();
            if(empty($companiesUserConnect) ){
                return new JsonResponse([
                    'exception'=>"contact the administrator, not company associed",
                    'errors' => ["compagnies" => "user connect not have companies"],
                    "code"=>Response::HTTP_BAD_REQUEST
                ]);
            }
            foreach($companiesUserConnect as $companyUserConnect){
                if ($checkCompaniesUserConnectToContactRequest === true) {
                    break;
                }

                if ($companyUserConnect === $company) {
                    $checkCompaniesUserConnectToContactRequest = true;
                }
            }
            if ($checkCompaniesUserConnectToContactRequest === false) {
                return new JsonResponse([
                    'exception'=>"contact the administrator, your rights are not sufficient",
                    'errors' => ['user' => "checkCompaniesUserConnectToContactRequest is false"],
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

    public function validator(User $user,array $body){
        if (in_array(User::ROLE_ADMIN, $user->getRoles())) {
            return true;
        }
        $companiesUserConnect = $user->getCompanies();
        $checkCompaniesUserConnectToContactRequest = false;
        if(!array_key_exists('company',$body)){
            return new JsonResponse([
                'exception'=>"company in body required",
                "code"=>Response::HTTP_BAD_REQUEST
            ]);
        }
        if(empty($user->getCompanies()) ){
            return new JsonResponse([
                'exception'=>"contact the administrator, not company associed",
                'error'=>['user'=>"user not have companies"],
                "code"=>Response::HTTP_BAD_REQUEST
            ]);
        }

        foreach($companiesUserConnect as $companyUserConnect){
            if ($checkCompaniesUserConnectToContactRequest === true) {
                break;
            }

            if ($companyUserConnect === $body['company']) {
                $checkCompaniesUserConnectToContactRequest = true;
            }
        }
        if ($checkCompaniesUserConnectToContactRequest === false) {
            return new JsonResponse([
                'exception'=>"contact the administrator, your rights are not sufficient",
                'error'=>['user'=>"checkCompaniesUserConnectToContactRequest false"],
                "code"=>Response::HTTP_BAD_REQUEST
            ]);
        }
    }
}
