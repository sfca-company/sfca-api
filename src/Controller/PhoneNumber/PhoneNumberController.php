<?php

namespace App\Controller\PhoneNumber;

use App\Entity\Contact\Contact;
use App\Service\Contact\ContactService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Security\SecurityService;
use App\Repository\Company\CompanyRepository;
use App\Repository\Contact\ContactRepository;
use App\Service\PhoneNumber\PhoneNumberService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PhoneNumberController extends AbstractController
{

    private $em;
    private $serializer;
    private $errors;
    private $securityService;
    private $contactService;
    private $phoneNumberService;


    public function __construct(
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        SecurityService $securityService,
        ContactService $contactService,
        PhoneNumberService $phoneNumberService
    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->errors = ["errors" => [], "code" => Response::HTTP_BAD_REQUEST];
        $this->securityService = $securityService;
        $this->contactService = $contactService;
        $this->phoneNumberService = $phoneNumberService;
    }
    /**
     * @Route("/api/phoneNumbers/types", name="get_all_phone_numbers_types", methods={"GET"})
     */
    public function getTypes(): JsonResponse
    {
        $json = $this->serializer->serialize(['body' => $this->phoneNumberService->getTypes(), 'code' => Response::HTTP_OK], 'json');
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
}
