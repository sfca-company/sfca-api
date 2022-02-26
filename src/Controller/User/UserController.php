<?php

namespace App\Controller\User;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends AbstractController
{
 /**
     * @Route("/api/users", name="get_user", methods={"GET"})
     */
    public function getUsers(Request $request,UserRepository $userRepo,SerializerInterface $serializer) :JsonResponse
    {
        $json = $serializer->serialize(['body'=>$userRepo->findAll(),'code'=>Response::HTTP_OK],'json',['groups'=>'user:read']);
        return new JsonResponse($json,Response::HTTP_OK,[],true);
    }
}
