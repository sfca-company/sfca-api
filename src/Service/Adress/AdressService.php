<?php

namespace App\Service\Adress;

use ErrorException;
use App\Entity\User;
use App\Entity\Adress;
use App\Entity\Company\Company;
use App\Repository\AdressRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Security\SecurityService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class AdressService
{

    private $serializer;
    private $adressRepo;
    public function __construct(
        SerializerInterface $serializer,
        AdressRepository $adressRepo
    ) {
        $this->serializer = $serializer;
        $this->adressRepo = $adressRepo;
    }
    /**
     * Permet de créer une adress
     *
     * @param array $body
     * @return Adress|null
     */
    public function create(array $body) :?Adress
    {
        $adress = null;
        if(array_key_exists("adress",$body)){
            $adressBody = $body['adress'];
            $adress = $this->serializer->deserialize(
                json_encode($adressBody),
                Adress::class,
                'json',
                [
                    'groups' => 'adress:write',
                ]
            );
        }
        return $adress;
    }

        /**
     * Permet de créer une adress ou de l'update
     *
     * @param array $body
     * @return Adress|null
     */
    public function update(array $body) :?Adress
    {
        $adress = null;
        if(array_key_exists("adress",$body)){
            if(array_key_exists("id",$body["adress"])){
                $adress = $this->adressRepo->findOneBy(["id"=>$body["adress"]["id"]]);
                if(empty($adress)){
                    return $this->create($body);
                }
                $adressBody = $body['adress'];
                $adress = $this->serializer->deserialize(
                    json_encode($adressBody),
                    Adress::class,
                    'json',
                    [
                        'groups' => 'adress:write',
                        'object_to_populate' => $adress
                    ]
                );
                return $adress;
            }
            return $this->create($body);
            

        }
        return $adress;
    }
}
