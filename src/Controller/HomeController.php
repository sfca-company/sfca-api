<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="app_home")
     */
    public function index(): Response
    {
        return $this->json([
            'data' => 'Welcome to api sfca',
            'code' => Response::HTTP_OK,
            'version'=>'0.1.0'
        ]);
    }
}
