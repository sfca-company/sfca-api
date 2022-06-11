<?php

namespace App\Controller\Document;

use App\Entity\Company\Company;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Company\CompanyRepository;
use App\Repository\Contact\ContactRepository;
use App\Repository\Document\DocumentRepository;
use App\Service\Contact\ContactService;
use App\Service\Document\DocumentService;
use App\Service\Security\SecurityService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DocumentController extends AbstractController
{

    private $em;
    private $serializer;
    private $errors;
    private $documentService;
    private $contactService;
    private $securityService;

    public function __construct(
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        DocumentService $documentService,
        ContactService $contactService,
        SecurityService $securityService
    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->errors = ["errors" => [], "code" => Response::HTTP_BAD_REQUEST];
        $this->documentService = $documentService;
        $this->contactService = $contactService;
        $this->securityService = $securityService;
    }

    /**
     * @Route("/api/document/contact/{id}",methods={"POST"}, name="create_document")
     */
    public function create($id, Request $request, ContactRepository $contactRepo): JsonResponse
    {
        try {
            $contact = $contactRepo->findOneBy(["id" => $id]);
            if (empty($contact)) {
                $this->errors['errors'][] = ["id" => "contact not found"];
            }
            $errors = $this->contactService->ressourceRightsGetContact($this->getUser(),$contact);
            if($errors instanceof JsonResponse){
                return $errors;
            }
            $body = json_decode($request->getContent(), true);

            $errors = $this->documentService->validator($body);
            if (!empty($errors)) {
                $this->errors['errors'] = $errors;
            }
            if (count($this->errors['errors']) > 0) {
                return new JsonResponse($this->errors, Response::HTTP_BAD_REQUEST, [], false);
            }
            $errorsProspect = $this->securityService->forbiddenProspect($this->getUser());
            if($errorsProspect instanceof JsonResponse){
                return $errorsProspect;
            }
            $document = $this->documentService->create($body, $contact);
            $json = $this->serializer->serialize(['body' => $document, 'code' => Response::HTTP_OK], 'json', ['groups' => 'document:read']);
            return new JsonResponse($json, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST, [], false);
        }
    }

    /**
     * @Route("/api/document/{id}/base64",methods={"POST"}, name="get_document_base64")
     */
    public function getBase64($id, Request $request, DocumentRepository $documentRepository): JsonResponse
    {
        try {
            $document = $documentRepository->findOneBy(["id" => $id]);
            if (empty($document)) {
                $this->errors['errors'][] = ["id" => "document not found"];
            }
            $errors = $this->contactService->ressourceRightsGetContact($this->getUser(),$document->getContact());
            if($errors instanceof JsonResponse){
                return $errors;
            }
            $errorsProspect = $this->securityService->forbiddenProspect($this->getUser());
            if($errorsProspect instanceof JsonResponse){
                return $errorsProspect;
            }
            $base64 = $this->documentService->searchFile($document);
            $json = $this->serializer->serialize(['body' => $base64, 'code' => Response::HTTP_OK], 'json', ['groups' => 'document:read']);
            return new JsonResponse($json, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST, [], false);
        }
    }

        /**
     * @Route("/api/document/{id}",methods={"DELETE"}, name="delete_document")
     */
    public function delete($id, DocumentRepository $documentRepository): JsonResponse
    {
        try {
            $document = $documentRepository->findOneBy(["id" => $id]);
            if (empty($document)) {
                $this->errors['errors'][] = ["document" => "document not found"];
                return new JsonResponse($this->errors, Response::HTTP_BAD_REQUEST, [], false);
            }
            $errors = $this->contactService->ressourceRightsGetContact($this->getUser(),$document->getContact());
            if($errors instanceof JsonResponse){
                return $errors;
            }
            $errorsProspect = $this->securityService->forbiddenProspect($this->getUser());
            if($errorsProspect instanceof JsonResponse){
                return $errorsProspect;
            }
            unlink($document->getUrl());
            $this->em->remove($document);
            $this->em->flush();
            return new JsonResponse($this->serializer->serialize(['body' => ["$id" => "$id supprimé contact"], 'code' => Response::HTTP_OK], 'json', ["groups" => "contact:read"]), Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST, [], false);
        }
    }
}
