<?php

namespace App\Controller\User;

use App\Entity\Company\Company;
use App\Repository\Company\CompanyRepository;
use App\Repository\UserRepository;
use App\Service\Company\CompanyService;
use App\Service\Security\SecurityService;
use App\Service\User\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    private $securityService;
    private $userService;
    private $companyService;

    public function __construct(
        SecurityService $securityService,
        UserService $userService,
        CompanyService $companyService
    ) {
        $this->securityService = $securityService;
        $this->userService = $userService;
        $this->companyService = $companyService;
    }
    /**
     * @Route("/api/users", name="get_users", methods={"GET"})
     */
    public function getUsers(UserRepository $userRepo, SerializerInterface $serializer): JsonResponse
    {
        $errors = $this->securityService->ressourceRightsAdmin($this->getUser());
        if ($errors instanceof JsonResponse) {
            return $errors;
        }
        $json = $serializer->serialize(['body' => $userRepo->findAll(), 'code' => Response::HTTP_OK], 'json', ['groups' => 'user:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/users/roles", name="get_roles", methods={"GET"})
     */
    public function getRoles(SerializerInterface $serializer): JsonResponse
    {
     
        $json = $serializer->serialize(['body' => $this->userService->getRoles(), 'code' => Response::HTTP_OK], 'json');
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/users/access", name="get_access", methods={"GET"})
     */
    public function getAccess(UserRepository $userRepo, SerializerInterface $serializer): JsonResponse
    {

        $json = $serializer->serialize(['body' => $this->userService->getAccess(), 'code' => Response::HTTP_OK], 'json');
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/me", name="get_users_connect", methods={"GET"})
     */
    public function getInfoUserConnect(UserRepository $userRepo, SerializerInterface $serializer): JsonResponse
    {
        $json = $serializer->serialize(['body' => $this->getUser(), 'code' => Response::HTTP_OK], 'json', ['groups' => 'user:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/users/{id}", name="get_user", methods={"GET"})
     */
    public function getUserById($id, Request $request, UserRepository $userRepo, SerializerInterface $serializer): JsonResponse
    {
        $user = $userRepo->findOneById($id);
        if (empty($user)) {
            return new JsonResponse(['errors' => ['user' => 'user not found'], "code" => 400], Response::HTTP_BAD_REQUEST, [], false);
        }
        $errors = $this->userService->ressourceRightsGetUser($this->getUser(), $user);
        if ($errors instanceof JsonResponse) {
            return $errors;
        }
        $errorsProspect = $this->securityService->forbiddenProspect($this->getUser());
        if ($errorsProspect instanceof JsonResponse) {
            return $errorsProspect;
        }
        $json = $serializer->serialize(['body' => $user, 'code' => Response::HTTP_OK], 'json', ['groups' => 'user:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/users/companies/{idCompany}", name="get_user_by_companies", methods={"GET"})
     */
    public function getUserByCompanies(?int $idCompany, Request $request, UserRepository $userRepo, SerializerInterface $serializer, CompanyRepository $companyRepo): JsonResponse
    {
        $company =  $companyRepo->findOneBy(["id" => $idCompany]);
        if (empty($company)) {
            return new JsonResponse([
                'exception' => "",
                'errors' => ["company" => "id company not found"],
                "code" => Response::HTTP_BAD_REQUEST
            ]);
        }
        $errors = $this->companyService->ressourceRightsGetCompany($this->getUser(), $company);
        $users = $userRepo->findByCompany($idCompany);
        if ($errors instanceof JsonResponse) {
            return $errors;
        }
        $errorsProspect = $this->securityService->forbiddenProspect($this->getUser());
        if ($errorsProspect instanceof JsonResponse) {
            return $errorsProspect;
        }
        $json = $serializer->serialize(['body' => $users, 'code' => Response::HTTP_OK], 'json', ['groups' => 'user:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
}
