<?php

namespace App\Service\Contact;

use App\Entity\Contact\Contact;
use ErrorException;
use App\Entity\User;

class ContactService
{

    /**
     * Permet de controler si un user à le droit de requeter un contact
     *
     * @param User $user
     * @param Contact $contact
     * @return void
     */
    public function ressourceRightsGetContact(User $user,Contact $contact)
    {
        try {
            if (in_array(User::ROLE_ADMIN, $user->getRoles())) {
                return true;
            }
            if ($user->getCompany()->getId() !== $contact->getCompany()->getId()) {
                throw new ErrorException("contact the administrator, your rights are not sufficient");
            }
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage());
        }
    }

    public function validator(User $user,array $body){
        if (in_array(User::ROLE_ADMIN, $user->getRoles())) {
            return true;
        }
        if(!array_key_exists('company',$body)){
            throw new ErrorException("contact the administrator, your body is incorrect, company is required");
        }
        if ($user->getCompany()->getId() !== $body['company']) {
            throw new ErrorException("contact the administrator, your rights are not sufficient");
        }
    }
}
