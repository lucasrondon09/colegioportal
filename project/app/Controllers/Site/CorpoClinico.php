<?php

namespace App\Controllers\Site;

use CodeIgniter\Controller;
use App\Models\Admin\PaginasModel;

class CorpoClinico extends Controller
{

    //--------------------------------------------------------------------
    public function index()
    {

        $this->modelPaginas	= new PaginasModel();

        $this->data =   [
                            'fields'   => $this->modelPaginas->where('idCategoria', 1)->find()
                        ];

        echo view('site/header.php');
        echo view('site/corpo-clinico.php', $this->data);
        echo view('site/footer.php');

    }

    //--------------------------------------------------------------------
    public function details($id)
    {

        $this->modelPaginas	= new PaginasModel();

        $this->data =   [
                            'fields'   => $this->modelPaginas->get($id)
                        ];

        echo view('site/header.php');
        echo view('site/corpo-clinico-detalhes.php', $this->data);
        echo view('site/footer.php');

    }

}
