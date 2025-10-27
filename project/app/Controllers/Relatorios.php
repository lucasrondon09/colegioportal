<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Mpdf\Mpdf;

class Relatorios extends BaseController
{
    public function index()
    {
        
        $mpdf = new Mpdf();
		$mpdf->WriteHTML('<h1>Olá, mundo!</h1>');
		$mpdf->Output('teste.pdf', 'I');
    }
}
