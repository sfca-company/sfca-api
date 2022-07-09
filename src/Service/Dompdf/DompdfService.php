<?php

namespace App\Service\DompdfService;

use Dompdf\Dompdf;

class DompdfService
{
    private $dompdf;
    public function __construct(

    ) {
        $this->dompdf = new Dompdf();
    }

    public function generateFactureBase64() :string
    {
        $html = file_get_contents("./html/facture.html");
        md5(rand());
        $this->dompdf->loadHtml($html);
        $this->dompdf->render();
        $this->dompdf->setPaper('A4', 'landscape');
        $output =  $this->dompdf->output();
        $date = new \Datetime('now');
        $path = "./facture/facture-".md5($date->format('Y-m-d H:i:s')).".pdf";
        file_put_contents($path, $output);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . "pdf" . ';base64,' . base64_encode($data);
        return  $base64;
    }
}
