<?php

namespace App\Service\PhoneNumber;

use ErrorException;
use App\Entity\User;
use App\Entity\Adress;
use App\Entity\Company\Company;
use App\Entity\PhoneNumber;
use App\Repository\AdressRepository;
use App\Repository\PhoneNumberRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Security\SecurityService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class PhoneNumberService
{

    private $serializer;
    private $phoneNumberRepo;
    public $types = ["Mobile","House","Partner"];
    public function __construct(
        SerializerInterface $serializer,
        PhoneNumberRepository $phoneNumberRepo
    ) {
        $this->serializer = $serializer;
        $this->phoneNumberRepo = $phoneNumberRepo;
    }
    /**
     * Permet de créer un numéro de téléphone
     *
     * @param array $body
     * @return PhoneNumber|null
     */
    public function create(array $body): ?PhoneNumber
    {
        $phoneNumber = null;
        if (array_key_exists("phoneNumber", $body)) {
            $phoneNumberBody = $body['phoneNumber'];
            $phoneNumber = $this->serializer->deserialize(
                json_encode($phoneNumberBody),
                PhoneNumber::class,
                'json',
                [
                    'groups' => 'phoneNumber:write',
                ]
            );
        }
        if (array_key_exists("phoneNumberFavorite", $body)) {
            $phoneNumberBody = $body['phoneNumberFavorite'];
            $phoneNumber = $this->serializer->deserialize(
                json_encode($phoneNumberBody),
                PhoneNumber::class,
                'json',
                [
                    'groups' => 'phoneNumber:write',
                ]
            );
        }
        return $phoneNumber;
    }

    /**
     * Permet de créer plusieurs numéro de téléphone
     *
     * @param array $body
     * @return array
     */
    public function createMultiple(array $body): array
    {
        $phoneNumbers = [];
        if (array_key_exists("phoneNumbers", $body)) {
            foreach ($body["phoneNumbers"] as $phoneNumberBody) {
                $phoneNumber = $this->serializer->deserialize(
                    json_encode($phoneNumberBody),
                    PhoneNumber::class,
                    'json',
                    [
                        'groups' => 'phoneNumber:write',
                    ]
                );
                $phoneNumbers[] = $phoneNumber;
            }
        }
        return $phoneNumbers;
    }

    /**
     * Permet de créer une adress ou de l'update
     *
     * @param array $body
     * @return Adress|null
     */
    public function update(array $body): ?Adress
    {
        $phoneNumber = null;
        if (array_key_exists("phoneNumber", $body)) {
            if (array_key_exists("id", $body["phoneNumber"])) {
                $phoneNumber = $this->phoneNumberRepo->findOneBy(["id" => $body["phoneNumber"]["id"]]);
                if (empty($phoneNumber)) {
                    return $this->create($body);
                }
                $phoneNumberBody = $body['phoneNumber'];
                $phoneNumber = $this->serializer->deserialize(
                    json_encode($phoneNumberBody),
                    Adress::class,
                    'json',
                    [
                        'groups' => 'phoneNumber:write',
                        'object_to_populate' => $phoneNumber
                    ]
                );
                return $phoneNumber;
            }
            return $this->create($body);
        }
        return $phoneNumber;
    }

    /**
     * Permet de créer un numéro de téléphone ou de l'update
     *
     * @param array $body
     * @return array
     */
    public function updateMultiple(array $body): array
    {
        $phoneNumbers = [];
        if (array_key_exists("phoneNumbers", $body)) {
            foreach ($body["phoneNumbers"] as $phoneNumberBody) {
                if (array_key_exists("id", $phoneNumberBody)) {
                    $phoneNumber = $this->phoneNumberRepo->findOneBy(["id" => $phoneNumberBody["id"]]);
                    if (empty($phoneNumber)) {
                        $multiplePhone = $this->createMultiple($body);
                        foreach ($multiplePhone as $phoneNumber) {
                            $phoneNumbers[] = $phoneNumber;
                        }
                        continue;
                    }

                    $phoneNumber = $this->serializer->deserialize(
                        json_encode($phoneNumberBody),
                        PhoneNumber::class,
                        'json',
                        [
                            'groups' => 'phoneNumber:write',
                            'object_to_populate' => $phoneNumber
                        ]
                    );
                    $phoneNumbers[] = $phoneNumber;
                    continue;
                } else {
                    $multiplePhone = $this->createMultiple($body);
                    foreach ($multiplePhone as $phoneNumber) {
                        $phoneNumbers[] = $phoneNumber;
                    }
                }
            }
        }
        return $phoneNumbers;
    }

    /**
     * Permet de retourner les différents types de téléphone
     *
     * @return array
     */
    public function getTypes() :array
    {
        return $this->types;
    }
}
