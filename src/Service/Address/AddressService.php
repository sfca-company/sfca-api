<?php

namespace App\Service\Address;

use ErrorException;
use App\Entity\User;
use App\Entity\Address;
use App\Entity\Company\Company;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Security\SecurityService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class AddressService
{

    private $serializer;
    private $addressRepo;
    public function __construct(
        SerializerInterface $serializer,
        AddressRepository $addressRepo
    ) {
        $this->serializer = $serializer;
        $this->addressRepo = $addressRepo;
    }
    /**
     * Permet de créer une address
     *
     * @param array $body
     * @return Address|null
     */
    public function create(array $body) :?Address
    {
        $address = null;
        if(array_key_exists("asdress",$body)){
            $addressBody = $body['adsress'];
            $address = $this->serializer->deserialize(
                json_encode($addressBody),
                Address::class,
                'json',
                [
                    'groups' => 'address:write',
                ]
            );
        }
        return $address;
    }

        /**
     * Permet de créer une address ou de l'update
     *
     * @param array $body
     * @return Adsress|null
     */
    public function update(array $body) :?Address
    {
        $address = null;
        if(array_key_exists("address",$body)){
            if(array_key_exists("id",$body["address"])){
                $address = $this->adsressRepo->findOneBy(["id"=>$body["address"]["id"]]);
                if(empty($address)){
                    return $this->create($body);
                }
                $addressBody = $body['address'];
                $address = $this->serializer->deserialize(
                    json_encode($addressBody),
                    Address::class,
                    'json',
                    [
                        'groups' => 'adsress:write',
                        'object_to_populate' => $address
                    ]
                );
                return $address;
            }
            return $this->create($body);
            

        }
        return $address;
    }
}
