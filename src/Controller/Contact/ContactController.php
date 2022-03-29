<?php

namespace App\Controller\Contact;

use App\Entity\Contact\Contact;
use App\Repository\Company\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Contact\ContactRepository;
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

    public function __construct(
        EntityManagerInterface $em,
        SerializerInterface $serializer
    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->errors = ["errors" => [], "code" => Response::HTTP_BAD_REQUEST];
    }
    /**
     * @Route("/api/contact", name="get_all_contact", methods={"GET"})
     */
    public function getAll(ContactRepository $contactRepo): JsonResponse
    {
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
        $json = $this->serializer->serialize(['body' => $contact, 'code' => Response::HTTP_OK], 'json', ['groups' => 'contact:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/contact", name="create_contact", methods={"POST"})
     */
    public function post(Request $request,CompanyRepository $companyRepository): JsonResponse
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
            if(!empty($company)){
                $contact->setCompany($company);
            }
        }
        $this->em->persist($contact);
        $this->em->flush();
        return new JsonResponse($this->serializer->serialize(['body' => $contact, 'code' => Response::HTTP_CREATED], 'json', ["groups" => "contact:read"]), Response::HTTP_CREATED, [], true);
    }

    /**
     * @Route("/api/contact/{id}",methods={"PUT"}, name="update_contact")
     */
    public function update($id, Request $request, ContactRepository $contactRepo,CompanyRepository $companyRepo): JsonResponse
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
                if(!empty($company)){
                    $contact->setCompany($company);
                }
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
    public function delete($id, ContactRepository $contactRepo): JsonResponse
    {
        try {
            $contact = $contactRepo->findOneBy(["id" => $id]);
            if (empty($contact)) {
                $this->errors['errors'][] = ["contact" => "contact not found"];
                return new JsonResponse($this->errors, Response::HTTP_BAD_REQUEST, [], false);
            }
            $this->em->remove($contact);
            $this->em->flush();
            return new JsonResponse($this->serializer->serialize(['body' => ["$id"=>"$id supprimé contact"], 'code' => Response::HTTP_OK], 'json', ["groups" => "contact:read"]), Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST, [], false);
        }
    }
}
