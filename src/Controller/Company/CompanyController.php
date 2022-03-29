<?php

namespace App\Controller\Company;

use App\Entity\Company\Company;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Company\CompanyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CompanyController extends AbstractController
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
     * @Route("/api/company", name="get_companys", methods={"GET"})
     */
    public function getAll(CompanyRepository $companyRepo): JsonResponse
    {
        $json = $this->serializer->serialize(['body' => $companyRepo->findAll(), 'code' => Response::HTTP_OK], 'json', ['groups' => 'company:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/company/{id}", name="get_company", methods={"GET"})
     */
    public function getOne(CompanyRepository $companyRepo, $id): JsonResponse
    {
        $company = $companyRepo->findOneBy(['id' => $id]);
        if (empty($company)) {
            array_push($this->errors['errors'], ['id' => 'company not found']);
            return new JsonResponse($this->errors, Response::HTTP_BAD_REQUEST, [], false);
        }
        $json = $this->serializer->serialize(['body' => $company, 'code' => Response::HTTP_OK], 'json', ['groups' => 'company:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/company", name="create_company", methods={"POST"})
     */
    public function post(Request $request): JsonResponse
    {
        $company = $this->serializer->deserialize(
            $request->getContent(),
            Company::class,
            'json',
            ['groups' => 'company:write']
        );
        $this->em->persist($company);
        $this->em->flush();
        return new JsonResponse($this->serializer->serialize(['body' => $company, 'code' => Response::HTTP_CREATED], 'json', ["groups" => "company:read"]), Response::HTTP_CREATED, [], true);
    }

    /**
     * @Route("/api/company/{id}",methods={"PUT"}, name="update_company")
     */
    public function update($id, Request $request, CompanyRepository $companyRepo): JsonResponse
    {
        try {
            $company = $companyRepo->findOneBy(["id" => $id]);
            if (empty($company)) {
                $this->errors['errors'][] = ["id" => "company not found"];
                return new JsonResponse($this->errors, Response::HTTP_BAD_REQUEST, [], false);
            }
            $company = $this->serializer->deserialize(
                $request->getContent(),
                Company::class,
                'json',
                [
                    'groups' => 'company:write',
                    'object_to_populate' => $company
                ]
            );
            $this->em->persist($company);
            $this->em->flush();
            return new JsonResponse($this->serializer->serialize(['body' => $company, 'code' => Response::HTTP_OK], 'json', ["groups" => "company:read"]), Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST, [], false);
        }
    }

       /**
     * @Route("/api/company/{id}",methods={"DELETE"}, name="delete_company")
     */
    public function delete($id, CompanyRepository $companyRepo): JsonResponse
    {
        try {
            $company = $companyRepo->findOneBy(["id" => $id]);
            if (empty($company)) {
                $this->errors['errors'][] = ["company" => "company not found"];
                return new JsonResponse($this->errors, Response::HTTP_BAD_REQUEST, [], false);
            }
            $this->em->remove($company);
            $this->em->flush();
            return new JsonResponse($this->serializer->serialize(['body' => ["$id"=>"$id supprimé company"], 'code' => Response::HTTP_OK], 'json', ["groups" => "company:read"]), Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST, [], false);
        }
    }
}
