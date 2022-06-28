<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\Company\CompanyRepository;
use App\Service\User\UserService;
use App\Repository\UserRepository;
use App\Service\Adress\AdressService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Security\SecurityService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UpdateUserController extends AbstractController
{
    private $securityService;
    private $userService;
    private $errors = ["errors" => [], "code" => Response::HTTP_BAD_REQUEST, "exception" => []];
    private $em;
    private $adressService;
    public function __construct(
        SecurityService $securityService,
        UserService $userService,
        EntityManagerInterface $em,
        AdressService $adressService
    ) {
        $this->securityService = $securityService;
        $this->userService = $userService;
        $this->em = $em;
        $this->adressService = $adressService;
    }
    /**
     * @Route("/api/users/{idUser}", name="update_users", methods={"PUT"})
     */
    public function update($idUser, UserRepository $userRepo, SerializerInterface $serializer, Request $request, UserPasswordHasherInterface $passwordHasher, CompanyRepository $companyRepo): JsonResponse
    {
        $errors = $this->securityService->ressourceRightsAdmin($this->getUser());
        if ($errors instanceof JsonResponse) {
            return $errors;
        }
        $body = json_decode($request->getContent(), true);
        if (empty($body)) {
            return $this->json([
                "body" => ["errors" => ["body invalide"]],
                "code" => Response::HTTP_UNAUTHORIZED
            ]);
        }
        $user = $userRepo->findOneBy(["id" => $idUser]);
        $this->validator($body, $user);
        if (count($this->errors['errors']) > 0) {
            return $this->json($this->errors);
        }
        $email = $body['email'];
        $roles = $body['roles'];
        $companies = $body['companies'];
        $acceess = $body['acceess'];
        
        if (!empty($userRepo->findByEmail($email))) {
            if ($user->getEmail() !== $email) {
                return $this->json([
                    "body" => ["errors" => ["email existant"]],
                    "code" => Response::HTTP_UNAUTHORIZED
                ]);
            }
        }

        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setAcceess($acceess);

        if (is_array($companies)) {
            foreach ($user->getCompanies() as  $company) {
                $user->removeCompany($company);
            }
            foreach ($companies as $companyId) {
                $company = $companyRepo->findOneBy(["id" => $companyId]);
                if (!empty($company)) {
                    $user->addCompany($company);
                }
            }
        }
        $user = $this->userService->addNonMandatoryAttribute($body,$user);
        $this->em->persist($user);
        $this->em->flush();
        $this->em->refresh($user);
        $json = $serializer->serialize(["body" => $user, "code" => Response::HTTP_OK], 'json', ["groups" => "user:read"]);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/users/{idUser}/password", name="update_users_password", methods={"PUT"})
     */
    public function updatePassword($idUser, UserRepository $userRepo, SerializerInterface $serializer, Request $request, UserPasswordHasherInterface $passwordHasher, CompanyRepository $companyRepo): JsonResponse
    {
        $errors = $this->securityService->ressourceRightsAdmin($this->getUser());
        if ($errors instanceof JsonResponse) {
            return $errors;
        }
        $body = json_decode($request->getContent(), true);
        if (empty($body)) {
            return $this->json([
                "errors" => ["body invalide"],
                "code" => Response::HTTP_UNAUTHORIZED
            ]);
        }
        $user = $userRepo->findOneBy(["id" => $idUser]);
        if (!array_key_exists("password", $body)) {
            $error = ["password" => "empty"];
            $this->errors['errors'][] = $error;
        }
        if (count($this->errors['errors']) > 0) {
            return $this->json($this->errors);
        }
        $password = $body['password'];
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $password
        );
        $user->setPassword($hashedPassword);
        $this->em->persist($user);
        $this->em->flush();
        $json = $serializer->serialize(["body" => $user, "code" => Response::HTTP_OK], 'json', ["groups" => "user:read"]);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    private function validator(array $body, ?User $user): void
    {

        if (!array_key_exists("email", $body)) {
            $error = ["email" => "empty"];
            $this->errors['errors'][] = $error;
        }
        if (!array_key_exists("roles", $body)) {
            $error = ["roles" => "empty"];
            $this->errors['errors'][] = $error;
        }
        if (!array_key_exists("companies", $body)) {
            $error = ["companies" => "empty"];
            $this->errors['errors'][] = $error;
        }
        if (empty($user)) {
            $error = ["user" => "not found"];
            $this->errors['errors'][] = $error;
        }
        if (!array_key_exists("acceess", $body)) {
            $error = ["acceess" => "empty"];
            $this->errors['errors'][] = $error;
        }
        if (array_key_exists("acceess", $body)) {
            $acceess = $body['acceess'];
            if(!is_int($acceess)){
                $error = ["acceess" => "the value must be a number "];
                $this->errors['errors'][] = $error;
            }
            if(intval($acceess) >= 5){
                $error = ["acceess" => "impossible access $acceess "];
                $this->errors['errors'][] = $error;
            }

        }
    }

}
