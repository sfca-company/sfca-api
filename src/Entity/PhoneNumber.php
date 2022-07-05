<?php

namespace App\Entity;

use App\Entity\Company\Company;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PhoneNumberRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PhoneNumberRepository::class)
 */
class PhoneNumber
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *  @Groups({"user:read","phoneNumber:write"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *  @Groups({"user:read","phoneNumber:write"})
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *  @Groups({"user:read","phoneNumber:write"})
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="phoneNumbers")
     *  
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="phoneNumbers")
     */
    private $company;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }
}
