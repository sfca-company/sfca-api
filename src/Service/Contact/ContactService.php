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
     * @return void
     */
    public function ressourceRightsGetContact(User $user,Contact $contact) :?JsonResponse
    {
        try {
            if (in_array(User::ROLE_ADMIN, $user->getRoles())) {
                return null;
            }
            if ($user->getCompany()->getId() !== $contact->getCompany()->getId()) {
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

    public function validator(User $user,array $body){
        if (in_array(User::ROLE_ADMIN, $user->getRoles())) {
            return true;
        }
        if(!array_key_exists('company',$body)){
            return new JsonResponse([
                'Exception'=>"company in body required",
                "code"=>Response::HTTP_BAD_REQUEST
            ]);
        }
        if ($user->getCompany()->getId() !== $body['company']) {
            return new JsonResponse([
                'Exception'=>"contact the administrator, your rights are not sufficient",
                "code"=>Response::HTTP_BAD_REQUEST
            ]);
        }
    }
}
