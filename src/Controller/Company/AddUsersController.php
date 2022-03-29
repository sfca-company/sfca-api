<?php

namespace App\Controller\Company;

use App\Entity\Company\Company;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Company\CompanyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AddUsersController extends AbstractController
{

    private $em;
    private $serializer;
    private $errors;

    public function __construct(
        EntityManagerInterface $em,
        SerializerInterface $serializer
    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->errors = ["errors" => [], "code" => Response::HTTP_BAD_REQUEST];
    }

    /**
     * @Route("/api/company/{id}/users",methods={"PUT"}, name="update_company_users")
     */
    public function update($id, Request $request, CompanyRepository $companyRepo, UserRepository $userRepo): JsonResponse
    {
        try {
            $company = $companyRepo->findOneBy(["id" => $id]);
            if (empty($company)) {
                $this->errors['errors'][] = ["id" => "company not found"];
            }
            $body = json_decode($request->getContent(), true);
            if (!array_key_exists("users", $body)) {
                $this->errors['errors'][] = ["body" => "array of users in body not found"];
            }
            if (count($this->errors['errors']) > 0) {
                return new JsonResponse($this->errors, Response::HTTP_BAD_REQUEST, [], false);
            }
            $idsUsers = $body['users'];
            $users = $userRepo->findBy(["id" => $idsUsers]);
            $company->getUsers()->clear();
            foreach ($users as $user) {
                $company->addUser($user);
            }
            $this->em->persist($company);
            $this->em->flush();

            $json = $this->serializer->serialize(['body' => $company, 'code' => Response::HTTP_OK], 'json', ['groups' => 'company:read']);
            return new JsonResponse($json, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST, [], false);
        }
    }
}
