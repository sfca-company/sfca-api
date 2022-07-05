<?php

namespace App\Service\Curl;

use ErrorException;
use App\Entity\User;
use App\Entity\Contact\Contact;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class CurlService
{
    private $ch;

    public function __construct()
    {
        $this->ch = curl_init();
    }

    public function get($url)
    {
        // Récupérer le contenu de la page
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);

        //Saisir l'URL et la transmettre à la variable.
        curl_setopt($this->ch, CURLOPT_URL, $url);
        //Désactiver la vérification du certificat puisque waytolearnx utilise HTTPS
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        //Exécutez la requête 
        $result = curl_exec($this->ch);

        return $result;
    }

    public function post($url,?array $body = null,?array $headers = null)
    {
        curl_setopt($this->ch, CURLOPT_URL,$url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($this->ch, CURLOPT_POST,           1 );
        curl_setopt($this->ch, CURLOPT_POSTFIELDS,$body); 
        curl_setopt($this->ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain')); 
        //Désactiver la vérification du certificat puisque waytolearnx utilise HTTPS
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        //Exécutez la requête 
        $result = curl_exec($this->ch);

        return $result;
    }
}
