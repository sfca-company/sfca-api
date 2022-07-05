<?php

namespace App\Controller\WebServices\Sirene;

use App\Service\Curl\CurlService;
use App\Service\Siren\SirenService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SireneController extends AbstractController
{
    private $serializer;
    private $sirenService;

    public function __construct(
        SerializerInterface $serializer,
        SirenService $sirenService
    ) {
        $this->serializer = $serializer;
        $this->sirenService = $sirenService;
    }

    /**
     * @Route("/api/sirene/siret/{siret}",methods={"GET"}, name="get_siret")
     */
    public function getSiret($siret): JsonResponse
    {
        try {
            $result = $this->sirenService->getSiret($siret);
            return new JsonResponse($result, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST, [], false);
        }
    }
}
