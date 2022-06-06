<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthController extends AbstractController
{
    private $em;
    function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
        $this->errors = ["errors" => [], "code" => Response::HTTP_BAD_REQUEST];
    }
    /**
     * @Route("/api/login/register", name="register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder, UserRepository $userRepo)
    {
        // $body = json_decode($request->getContent(), true);
        // $password = $body['password'];
        // $email = $body['email'];
        // if (empty($password) || empty($email)) {
        //     return $this->json([
        //         "body" => ["errors" => ["email ou password invalide"]],
        //         "code" => Response::HTTP_UNAUTHORIZED
        //     ]);
        // }
        // if (!empty($userRepo->findByEmail($email))) {
        //     return $this->json([
        //         "body" => ["errors" => ["email existant"]],
        //         "code" => Response::HTTP_UNAUTHORIZED
        //     ]);
        // }
        // $user = new User();
        // $user->setPassword($encoder->encodePassword($user, $password));
        // $user->setEmail($email);
        // $roles = ["ROLE_PROSPECT"];
        // $user->setRoles($roles);
        // $this->em->persist($user);
        // $this->em->flush();
        return $this->json([
            "body" => ['user' => ['id' => rand(), 'email' => base64_encode(rand())]],
            "code" => Response::HTTP_CREATED
        ], Response::HTTP_CREATED);
    }
}
