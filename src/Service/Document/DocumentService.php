<?php

namespace App\Service\Document;

use App\Entity\Company\Company;
use App\Entity\Contact\Contact;
use App\Entity\Document\Document;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

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

    /**
     * Permet de créer un document sur un contact
     *
     * @param array $body
     * @param object $contact
     * @return Document
     */
    public function create(array $body, object $object): Document
    {
        $document = new Document;
        $document->setName($body['name']);
        $document->setTypeMime($body['typeMime']);
        $document->setExtension($body['extension']);
        $nameOfDossier = null;
        $base64 = $body['base64'];
        $doc = base64_decode($base64);

        switch ($object) {
            case $object instanceof Contact:
                $nameOfDossier = "contact";
                $contact = $object;
                $document->setContact($contact);
                $dossier = "img/$nameOfDossier/" . $object->getId();
                if (!file_exists($dossier)) {
                    mkdir($dossier);
                }
                $dossier = "img/$nameOfDossier/" . $object->getId() . "/" . $document->getId();
                if (!file_exists($dossier)) {
                    mkdir($dossier);
                }
                $url = "$dossier/" . $document->getName() . "." . $document->getExtension();
                break;
            case $object instanceof Company:
                $nameOfDossier = "company";
                $company = $object;
                $company->setLogo($document);
                $dossier = "img/$nameOfDossier/logo/" . $company->getId();
                if (!file_exists($dossier)) {
                    mkdir($dossier);
                }
                $dossier = "img/$nameOfDossier/logo/" . $company->getId();
                if (!file_exists($dossier)) {
                    mkdir($dossier);
                }
                $url = "$dossier/" . $document->getName() . "." . $document->getExtension();
                break;
                default : 
                new Exception("error switch ged");
        }
       

        $document->setUrl($url);
        file_put_contents($url, $doc);
        $document->setTaille(filesize($url));
        $this->em->persist($document);
        $this->em->flush();
        $this->em->persist($object);
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
