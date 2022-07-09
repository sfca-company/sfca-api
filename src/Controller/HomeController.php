<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HomeController extends AbstractController
{


    /**
     * @Route("/api/home", methods={"GET"}, name="app_home")
     */
    public function index(): Response
    {
        // Render the HTML as PDF 
        return $this->json([
            'data' => 'Welcome to api sfca',
            'code' => Response::HTTP_OK,
            'version' => '0.1.3'
        ]);
    }
}
