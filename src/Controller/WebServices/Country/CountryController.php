<?php

namespace App\Controller\WebServices\Country;

use App\Service\Curl\CurlService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CountryController extends AbstractController
{
    private $serializer;
    private $curlService;

    public function __construct(
        SerializerInterface $serializer,
        CurlService $curlService
    ) {
        $this->serializer = $serializer;
        $this->curlService = $curlService;
    }

    /**
     * @Route("/api/countries",methods={"GET"}, name="get_countries")
     */
    public function getCountries(): JsonResponse
    {
        try {
            $result = $this->curlService->get("https://restcountries.com/v3.1/all");
            return new JsonResponse($result, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST, [], false);
        }
    }
}
