<?php

namespace App\Controllers\Site;

use CodeIgniter\Controller;
use App\Models\Admin\GaleriaImagensModel;
use App\Models\Admin\PaginasModel;

class QuemSomos extends Controller
{

    //--------------------------------------------------------------------
    public function sobre()
    {

        $this->modelGaleriaImagens	= new GaleriaImagensModel();
        $this->modelPaginas	= new PaginasModel();

        $this->data =   [
                            'imagens'   => $this->modelGaleriaImagens->where('idGaleria', 1)->find(),
                            'pagina'   => $this->modelPaginas->get(7)
                        ];

        echo view('site/header.php');
        echo view('site/sobre.php', $this->data);
        echo view('site/footer.php');

    }



}
