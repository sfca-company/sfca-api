<?php

namespace App\Entity\Document;

use App\Entity\Contact\Contact;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Document\DocumentRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=DocumentRepository::class)
 */
class Document
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"document:read","contact:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Groups({"document:read","contact:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Groups({"document:read","contact:read"})
     */
    private $typeMime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *  @Groups({"document:read","contact:read"})
     */
    private $extension;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *  @Groups({"document:read","contact:read"})
     */
    private $taille;

    /**
     * @ORM\ManyToOne(targetEntity=Contact::class, inversedBy="documents")
     */
    private $contact;

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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getTypeMime(): ?string
    {
        return $this->typeMime;
    }

    public function setTypeMime(string $typeMime): self
    {
        $this->typeMime = $typeMime;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getTaille(): ?int
    {
        return $this->taille;
    }

    public function setTaille(?int $taille): self
    {
        $this->taille = $taille;

        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

}
