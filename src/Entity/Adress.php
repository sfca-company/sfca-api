<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AdressRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AdressRepository::class)
 */
class Adress
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user:read","adress:write"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:read","adress:write"})
     */
    private $adress1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:read","adress:write"})
     */
    private $adress2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:read","adress:write"})
     */
    private $adress3;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:read","adress:write"})
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:read","adress:write"})
     */
    private $state;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:read","adress:write"})
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:read","adress:write"})
     */
    private $postalCode;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdress1(): ?string
    {
        return $this->adress1;
    }

    public function setAdress1(?string $adress1): self
    {
        $this->adress1 = $adress1;

        return $this;
    }

    public function getAdress2(): ?string
    {
        return $this->adress2;
    }

    public function setAdress2(?string $adress2): self
    {
        $this->adress2 = $adress2;

        return $this;
    }

    public function getAdress3(): ?string
    {
        return $this->adress3;
    }

    public function setAdress3(?string $adress3): self
    {
        $this->adress3 = $adress3;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }
}
