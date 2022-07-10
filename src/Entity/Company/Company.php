<?php

namespace App\Entity\Company;

use App\Entity\Address;
use App\Entity\Contact\Contact;
use App\Entity\Document\Document;
use App\Entity\PhoneNumber;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use App\Repository\Company\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CompanyRepository::class)
 */
class Company
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"company:read","user:read","contact:read","contact:write"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"company:read","user:read","company:write","contact:read"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Contact::class, mappedBy="company")
     * @Groups({"company:read"})
     */
    private $contacts;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="companies")
     */
    private $users;

    /**
     * @ORM\OneToOne(targetEntity=Address::class, cascade={"persist", "remove"})
     *  @Groups({"company:read","company:write"})
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"company:read","company:write"})
     */
    private $linkSociete;

    /**
     * @ORM\Column(type="string", length=14, nullable=true)
     * @Groups({"company:read","company:write"})
     */
    private $siret;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"company:read","company:write"})
     */
    private $citySiret;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"company:read","company:write"})
     */
    private $orias;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"company:read","company:write"})
     */
    private $webSite;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"company:read","company:write"})
     */
    private $currency;

    /**
     * @ORM\OneToMany(targetEntity=PhoneNumber::class, mappedBy="company")
     * @Groups({"company:read","company:write"})
     */
    private $phoneNumbers;

    /**
     * @ORM\OneToOne(targetEntity=PhoneNumber::class, cascade={"persist", "remove"})
     * @Groups({"company:read","company:write"})
     */
    private $phoneNumberFavorite;

    /**
     * @ORM\OneToOne(targetEntity=Document::class, cascade={"persist", "remove"})
     *  @Groups({"company:read"})
     */
    private $logo;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->phoneNumbers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setCompany($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getCompany() === $this) {
                $contact->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addCompany($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeCompany($this);
        }

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getLinkSociete(): ?string
    {
        return $this->linkSociete;
    }

    public function setLinkSociete(?string $linkSociete): self
    {
        $this->linkSociete = $linkSociete;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getCitySiret(): ?string
    {
        return $this->citySiret;
    }

    public function setCitySiret(?string $citySiret): self
    {
        $this->citySiret = $citySiret;

        return $this;
    }

    public function getOrias(): ?string
    {
        return $this->orias;
    }

    public function setOrias(?string $orias): self
    {
        $this->orias = $orias;

        return $this;
    }

    public function getWebSite(): ?string
    {
        return $this->webSite;
    }

    public function setWebSite(?string $webSite): self
    {
        $this->webSite = $webSite;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return Collection<int, PhoneNumber>
     */
    public function getPhoneNumbers(): Collection
    {
        return $this->phoneNumbers;
    }

    public function addPhoneNumber(PhoneNumber $phoneNumber): self
    {
        if (!$this->phoneNumbers->contains($phoneNumber)) {
            $this->phoneNumbers[] = $phoneNumber;
            $phoneNumber->setCompany($this);
        }

        return $this;
    }

    public function removePhoneNumber(PhoneNumber $phoneNumber): self
    {
        if ($this->phoneNumbers->removeElement($phoneNumber)) {
            // set the owning side to null (unless already changed)
            if ($phoneNumber->getCompany() === $this) {
                $phoneNumber->setCompany(null);
            }
        }

        return $this;
    }

    public function getphoneNumberFavorite(): ?PhoneNumber
    {
        return $this->phoneNumberFavorite;
    }

    public function setPhoneNumberFavorite(?PhoneNumber $phoneNumberFavorite): self
    {
        $this->phoneNumberFavorite = $phoneNumberFavorite;

        return $this;
    }

    public function getLogo(): ?Document
    {
        return $this->logo;
    }

    public function setLogo(?Document $logo): self
    {
        $this->logo = $logo;

        return $this;
    }
}
