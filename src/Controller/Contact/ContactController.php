<?php

namespace App\Controller\Contact;

use App\Entity\Contact\Contact;
use App\Service\Contact\ContactService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Security\SecurityService;
use App\Repository\Company\CompanyRepository;
use App\Repository\Contact\ContactRepository;
use App\Service\Company\CompanyService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{

    private $em;
    private $serializer;
    private $errors;
    private $securityService;
    private $contactService;
    private $companyService;

    public function __construct(
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        SecurityService $securityService,
        ContactService $contactService,
        CompanyService $companyService
    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->errors = ["errors" => [], "code" => Response::HTTP_BAD_REQUEST];
        $this->securityService = $securityService;
        $this->contactService = $contactService;
        $this->companyService = $companyService;
    }
    /**
     * @Route("/api/contact", name="get_all_contact", methods={"GET"})
     */
    public function getAll(ContactRepository $contactRepo): JsonResponse
    {
        $errors = $this->securityService->ressourceRightsAdmin($this->getUser());
        if ($errors instanceof JsonResponse) {
            return $errors;
        }
        $json = $this->serializer->serialize(['body' => $contactRepo->findAll(), 'code' => Response::HTTP_OK], 'json', ['groups' => 'contact:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/contact/{id}", name="get_contact", methods={"GET"})
     */
    public function getOne(ContactRepository $contactRepo, $id): JsonResponse
    {
        $contact = $contactRepo->findOneBy(['id' => $id]);
        if (empty($contact)) {
            array_push($this->errors['errors'], ['id' => 'contact not found']);
            return new JsonResponse($this->errors, Response::HTTP_BAD_REQUEST, [], false);
        }
        $errors = $this->contactService->ressourceRightsGetContact($this->getUser(), $contact);
        $errorsProspect = $this->securityService->forbiddenProspect($this->getUser());
        if ($errorsProspect instanceof JsonResponse) {
            return $errorsProspect;
        }
        if ($errors instanceof JsonResponse) {
            return $errors;
        }
        $json = $this->serializer->serialize(['body' => $contact, 'code' => Response::HTTP_OK], 'json', ['groups' => 'contact:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/contact/compagnies/{id}", name="get_contact_companies", methods={"GET"})
     */
    public function getByCompagnies($id,CompanyRepository $companyRepo,ContactRepository $contactRepo){
        $company = $companyRepo->findOneBy(['id' => $id]);
        if (empty($company)) {
            array_push($this->errors['errors'], ['id' => 'company not found']);
            return new JsonResponse($this->errors, Response::HTTP_BAD_REQUEST, [], false);
        }
        $errors = $this->companyService->ressourceRightsGetCompany($this->getUser(),$company);
        $errorsProspect = $this->securityService->forbiddenProspect($this->getUser());
        if($errors instanceof JsonResponse){
            return $errors;
        }
        if($errorsProspect instanceof JsonResponse){
            return $errorsProspect;
        }
        $result = $contactRepo->findByCompany($company);
        $json = $this->serializer->serialize(["body"=>$result,"code"=>Response::HTTP_OK],"json",["groups"=>"contact:lister"]);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }


    /**
     * @Route("/api/contact", name="create_contact", methods={"POST"})
     */
    public function post(Request $request, CompanyRepository $companyRepository): JsonResponse
    {
        $body = json_decode($request->getContent(), true);
        $contact = $this->serializer->deserialize(
            $request->getContent(),
            Contact::class,
            'json',
            ['groups' => 'contact:write']
        );
        if (array_key_exists("company", $body)) {
            $company = $companyRepository->findOneById($body['company']);
            if (!empty($company)) {
                $contact->setCompany($company);
            }
        }
        $errors = $this->contactService->validator($this->getUser(), $body, $request->getMethod());
        $errorsProspect = $this->securityService->forbiddenProspect($this->getUser());
        if ($errorsProspect instanceof JsonResponse) {
            return $errorsProspect;
        }
        if ($errors instanceof JsonResponse) {
            return $errors;
        }
        $this->em->persist($contact);
        $this->em->flush();
        return new JsonResponse($this->serializer->serialize(['body' => $contact, 'code' => Response::HTTP_CREATED], 'json', ["groups" => "contact:read"]), Response::HTTP_CREATED, [], true);
    }

    /**
     * @Route("/api/contact/{id}",methods={"PUT"}, name="update_contact")
     */
    public function update($id, Request $request, ContactRepository $contactRepo, CompanyRepository $companyRepo): JsonResponse
    {
        try {
            $contact = $contactRepo->findOneBy(["id" => $id]);
            $body = json_decode($request->getContent(), true);
            if (empty($contact)) {
                $this->errors['errors'][] = ["id" => "contact not found"];
                return new JsonResponse($this->errors, Response::HTTP_BAD_REQUEST, [], false);
            }
            $contact = $this->serializer->deserialize(
                $request->getContent(),
                contact::class,
                'json',
                [
                    'groups' => 'contact:write',
                    'object_to_populate' => $contact
                ]
            );
            if (array_key_exists("company", $body)) {
                $company = $companyRepo->findOneById($body['company']);
                if (!empty($company)) {
                    $contact->setCompany($company);
                }
            }
            $errors = $this->contactService->validator($this->getUser(), $body, $request->getMethod());
            $errorsProspect = $this->securityService->forbiddenProspect($this->getUser());
            if ($errorsProspect instanceof JsonResponse) {
                return $errorsProspect;
            }
            if ($errors instanceof JsonResponse) {
                return $errors;
            }
            $this->em->persist($contact);
            $this->em->flush();
            return new JsonResponse($this->serializer->serialize(['body' => $contact, 'code' => Response::HTTP_OK], 'json', ["groups" => "contact:read"]), Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST, [], false);
        }
    }

    /**
     * @Route("/api/contact/{id}",methods={"DELETE"}, name="delete_contact")
     */
    public function delete($id, ContactRepository $contactRepo, Request $request): JsonResponse
    {
        try {
            $contact = $contactRepo->findOneBy(["id" => $id]);
            if (empty($contact)) {
                $this->errors['errors'][] = ["contact" => "contact not found"];
                return new JsonResponse($this->errors, Response::HTTP_BAD_REQUEST, [], false);
            }
            $errors = $this->contactService->ressourceRightsGetContact($this->getUser(), $contact, $request->getMethod());
            $errorsProspect = $this->securityService->forbiddenProspect($this->getUser());
            if ($errorsProspect instanceof JsonResponse) {
                return $errorsProspect;
            }
            if ($errors instanceof JsonResponse) {
                return $errors;
            }
            $this->em->remove($contact);
            $this->em->flush();
            return new JsonResponse($this->serializer->serialize(['body' => ["$id" => "$id supprim?? contact"], 'code' => Response::HTTP_OK], 'json', ["groups" => "contact:read"]), Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST, [], false);
        }
    }
}
