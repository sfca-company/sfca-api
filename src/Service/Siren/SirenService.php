<?php

namespace App\Service\Siren;

use ErrorException;
use App\Entity\User;
use App\Entity\Contact\Contact;
use App\Service\Curl\CurlService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonDecode;

class SirenService
{

    public $url = "https://api.insee.fr/entreprises/sirene/V3";
    public $headers = [];
    private $curlService;
    private $ch;
    public $token;

    public function __construct(CurlService $curlService)
    {
        $this->curlService = $curlService;
        $this->ch = curl_init();
    }

    public function getSiret(string $siret)
    {
        $result = null;
        $result = $this->postIdentifiant($this->url);
        $result = json_decode($result, true);
        $token = $result["access_token"];
        $this->token = $token;
        $result = $this->siret($siret);
        return $result;
    }

    public function postIdentifiant($url)
    {
        curl_setopt($this->ch, CURLOPT_URL, "https://api.insee.fr/token?grant_type=client_credentials");
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_POST,           1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, null);
        curl_setopt($this->ch, CURLOPT_USERPWD, "gaOZQjyoIJrtchS_jfoB2f5AACwa:Ca4l4nnlp766munFAnmSbtCZStEa");
        curl_setopt($this->ch, CURLOPT_HTTPHEADER,     array('Accept: */*'));
        //Désactiver la vérification du certificat puisque waytolearnx utilise HTTPS
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        //Exécutez la requête 
        $result = curl_exec($this->ch);
        return $result;
    }

    public function siret(string $siret)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $authorization = "Authorization: Bearer " . $this->token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization));
        //Saisir l'URL et la transmettre à la variable.
        $url = "https://api.insee.fr/entreprises/sirene/V3/siret/" . $siret;
        curl_setopt($ch, CURLOPT_URL,$url);
        //Désactiver la vérification du certificat puisque waytolearnx utilise HTTPS
        //curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //Exécutez la requête 
        $result = curl_exec($ch);
        return $result;
    }
}
