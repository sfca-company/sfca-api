<?php

namespace App\Controller\Company;

use App\Entity\Company\Company;
use App\Repository\UserRepository;
use App\Service\Company\CompanyService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Company\CompanyRepository;
use App\Service\Security\SecurityService;
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
    private $companyService;
    private $securityService;

    public function __construct(
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        CompanyService $companyService,
        SecurityService $securityService
    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->errors = ["errors" => [], "code" => Response::HTTP_BAD_REQUEST];
        $this->companyService = $companyService;
        $this->securityService = $securityService;
    }
    /**
     * @Route("/api/companies", name="get_companys", methods={"GET"})
     */
    public function getAll(CompanyRepository $companyRepo): JsonResponse
    {
        $errors = $this->securityService->ressourceRightsAdmin($this->getUser());
        if($errors instanceof JsonResponse){
            return $errors;
        }
        $json = $this->serializer->serialize(['body' => $companyRepo->findAll(), 'code' => Response::HTTP_OK], 'json', ['groups' => 'company:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/compagnies/{id}", name="get_company", methods={"GET"})
     */
    public function getOne(CompanyRepository $companyRepo, $id): JsonResponse
    {
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
        $json = $this->serializer->serialize(['body' => $company, 'code' => Response::HTTP_OK], 'json', ['groups' => 'company:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/companies", name="create_company", methods={"POST"})
     */
    public function post(Request $request): JsonResponse
    {
        $errors = $this->securityService->ressourceRightsAdmin($this->getUser());
        if($errors instanceof JsonResponse){
            return $errors;
        }
        $company = $this->serializer->deserialize(
            $request->getContent(),
            Company::class,
            'json',
            ['groups' => 'company:write']
        );

        $this->em->persist($company);
        $this->em->flush();

        $body = json_decode($request->getContent(),true);
        $company = $this->companyService->addLogo($company,$body);
        $this->em->refresh($company);
        return new JsonResponse($this->serializer->serialize(['body' => $company, 'code' => Response::HTTP_CREATED], 'json', ["groups" => "company:read"]), Response::HTTP_CREATED, [], true);
    }

    /**
     * @Route("/api/companies/{id}",methods={"PUT"}, name="update_company")
     */
    public function update($id, Request $request, CompanyRepository $companyRepo): JsonResponse
    {
        try {
            $company = $companyRepo->findOneBy(["id" => $id]);
            if (empty($company)) {
                $this->errors['errors'][] = ["id" => "company not found"];
                return new JsonResponse($this->errors, Response::HTTP_BAD_REQUEST, [], false);
            }
            $errors = $this->companyService->ressourceRightsGetCompany($this->getUser(),$company,$request->getMethod());
            $errorsProspect = $this->securityService->forbiddenProspect($this->getUser());
            if($errors instanceof JsonResponse){
                return $errors;
            }
            if($errorsProspect instanceof JsonResponse){
                return $errorsProspect;
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
            
            $body = json_decode($request->getContent(),true);
            $company = $this->companyService->addLogo($company,$body);
            $this->em->refresh($company);

            return new JsonResponse($this->serializer->serialize(['body' => $company, 'code' => Response::HTTP_OK], 'json', ["groups" => "company:read"]), Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST, [], false);
        }
    }

       /**
     * @Route("/api/companies/{id}",methods={"DELETE"}, name="delete_company")
     */
    public function delete($id, CompanyRepository $companyRepo): JsonResponse
    {
        try {
            $company = $companyRepo->findOneBy(["id" => $id]);
            if (empty($company)) {
                $this->errors['errors'][] = ["company" => "company not found"];
                return new JsonResponse($this->errors, Response::HTTP_BAD_REQUEST, [], false);
            }
            $errors = $this->securityService->ressourceRightsAdmin($this->getUser());
            if($errors instanceof JsonResponse){
                return $errors;
            }
            $this->em->remove($company);
            $this->em->flush();
            return new JsonResponse($this->serializer->serialize(['body' => ["$id"=>"$id supprimé company"], 'code' => Response::HTTP_OK], 'json', ["groups" => "company:read"]), Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST, [], false);
        }
    }
}
