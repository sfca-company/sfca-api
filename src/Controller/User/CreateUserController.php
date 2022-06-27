<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Entity\Adress;
use App\Service\User\UserService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Security\SecurityService;
use App\Repository\Company\CompanyRepository;
use App\Service\Adress\AdressService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserController extends AbstractController
{
    private $securityService;
    private $userService;
    private $errors = ["errors" => [], "code" => Response::HTTP_BAD_REQUEST, "exception" => []];
    private $em;
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
     * @Route("/api/users", name="create_users", methods={"POST"})
     */
    public function create(UserRepository $userRepo, SerializerInterface $serializer, Request $request, UserPasswordHasherInterface $passwordHasher,CompanyRepository $companyRepo): JsonResponse
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
        $this->validator($body);
        if (count($this->errors['errors']) > 0) {
            return $this->json($this->errors);
        }

        $password = $body['password'];
        $email = $body['email'];
        $roles = $body['roles'];
        $companies = $body['companies'];
        $accees = $body['accees'];
        if (!empty($userRepo->findByEmail($email))) {
            return $this->json([
                "body" => ["errors" => ["email existant"]],
                "code" => Response::HTTP_UNAUTHORIZED
            ]);
        }

        $user = new User();
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $password
        );
        $user->setPassword($hashedPassword);
        $user->setEmail($email);
        $user->setRoles($this->userService->validatorRoles($roles));
        $user->setAccees($accees);
        if (is_array($companies)) {
            foreach ($companies as $companyId) {
                $company = $companyRepo->findOneBy(["id"=>$companyId]);
                if(!empty($company)){
                    $user->addCompany($company);
                }
            }
        }
        $user = $this->userService->addNonMandatoryAttribute($body,$user);
        
        $this->em->persist($user);
        $this->em->flush();
        $json = $serializer->serialize(["body" => $user, "code" => Response::HTTP_OK], 'json', ["groups" => "user:read"]);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    private function validator(array $body): void
    {

        if (!array_key_exists("password", $body)) {
            $error = ["password" => "empty"];
            $this->errors['errors'][] = $error;
        }
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
        if (!array_key_exists("accees", $body)) {
            $error = ["accees" => "empty"];
            $this->errors['errors'][] = $error;
        }
        if (array_key_exists("accees", $body)) {
            $accees = $body['accees'];
            if(!is_int($accees)){
                $error = ["accees" => "the value must be a number "];
                $this->errors['errors'][] = $error;
            }
            if(intval($accees) >= 5){
                $error = ["accees" => "impossible access $accees "];
                $this->errors['errors'][] = $error;
            }

        }
    }
}
