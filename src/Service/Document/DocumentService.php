<?php

namespace App\Service\Document;

use App\Entity\Contact\Contact;
use App\Entity\Document\Document;
use Doctrine\ORM\EntityManagerInterface;


class DocumentService
{


    private $em;


    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }
    /**
     * validator
     *
     * @param array $body
     * @return array
     */
    public function validator(array $body): array
    {
        $errors = [];
        if (!array_key_exists("base64", $body)) {
            $errors[] = ["body" => "base64 in body not found"];
        }
        if (!array_key_exists("name", $body)) {
            $errors[] = ["body" => "name in body not found"];
        }
        if (!array_key_exists("typeMime", $body)) {
            $errors[] = ["body" => "typeMime in body not found"];
        }
        if (!array_key_exists("extension", $body)) {
            $errors[] = ["body" => "extension in body not found"];
        }
        return $errors;
    }

    public function create(array $body, Contact $contact): Document
    {

        $document = new Document;
        $document->setName($body['name']);
        $document->setTypeMime($body['typeMime']);
        $document->setExtension($body['extension']);
        $document->setUrl("tempory");
        $document->setContact($contact);
        $base64 = $body['base64'];
        $doc = base64_decode($base64);

        $this->em->persist($document);
        $this->em->flush();

        $dossier = "img/" . $contact->getId();
        if (!file_exists($dossier)) {
            mkdir($dossier);
        }
        $dossier = "img/" . $contact->getId() . "/" . $document->getId();
        if (!file_exists($dossier)) {
            mkdir($dossier);
        }
        $url = "$dossier/" . $document->getName() . "." . $document->getExtension();

        $document->setUrl($url);
        file_put_contents($url, $doc);
        $document->setTaille(filesize($url));
        $this->em->persist($document);
        $this->em->flush();
        return $document;
    }



    public function searchFile(Document $document): string
    {

        $path = realpath($document->getUrl());

        if (!is_dir($path)) {

            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        return '';
    }
}
