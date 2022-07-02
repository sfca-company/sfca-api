<?php

namespace App\Entity\Contact;

use App\Entity\Company\Company;
use App\Entity\Document\Document;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Contact\ContactRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ContactRepository::class)
 */
class Contact
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"company:read","contact:read","contact:write","contact:lister"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"company:read","contact:read","contact:write","contact:lister"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"company:read","contact:read","contact:write","contact:lister"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"company:read","contact:read","contact:write","contact:lister"})
     */
    private $dateOfBirth;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="contacts")
     * @Groups({"contact:read","contact:write"})
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity=Document::class, mappedBy="contact")
     * @Groups({"contact:read"})
     */
    private $documents;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

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

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setContact($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getContact() === $this) {
                $document->setContact(null);
            }
        }

        return $this;
    }
}
