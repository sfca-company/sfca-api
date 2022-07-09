<?php

namespace App\Controller\Document;

use App\Entity\Company\Company;
use App\Repository\UserRepository;
use App\Service\Contact\ContactService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Document\DocumentService;
use App\Service\Security\SecurityService;
use App\Service\DompdfService\DompdfService;
use App\Repository\Company\CompanyRepository;
use App\Repository\Contact\ContactRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\Document\DocumentRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FactureController extends AbstractController
{

    private $dompdfService;
    private $errors;

    public function __construct(
    ) {
        $this->errors = ["errors" => [], "code" => Response::HTTP_BAD_REQUEST];
        $this->dompdfService = new DompdfService();
    }
    /**
     * @Route("/api/facture",methods={"POST"}, name="create_facture")
     */
    public function create(): JsonResponse
    {
        try {
            $return = [
                "body"=>$this->dompdfService->generateFactureBase64(),
                "code"=> Response::HTTP_CREATED
            ];
            return new JsonResponse($return, Response::HTTP_CREATED, [], false);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST, [], false);
        }
    }
}
