<?php

namespace App\Service\Company;

use ErrorException;
use App\Entity\User;
use App\Entity\Company\Company;
use Doctrine\ORM\EntityManagerInterface;

class CompanyService
{


    private $em;


    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }

    public function ressourceRightsAdmin(User $user)
    {
        try {
            if (!in_array(User::ROLE_ADMIN, $user->getRoles())) {
                throw new ErrorException("contact the administrator, your rights are not sufficient");
            }
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage());
        }
    }

    public function ressourceRightsGetCompany(User $user, Company $company)
    {
        try {
            if (in_array(User::ROLE_ADMIN, $user->getRoles())) {
                return true;
            }

            if ($user->getCompany()->getId() !== $company->getId()) {
                throw new ErrorException("contact the administrator, your rights are not sufficient");
            }
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage());
        }
    }
}
