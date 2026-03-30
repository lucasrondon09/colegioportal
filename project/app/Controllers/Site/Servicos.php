<?php

namespace App\Controllers\Site;

use CodeIgniter\Controller;
use App\Models\Admin\ServicoModel;

class Servicos extends Controller
{

    //--------------------------------------------------------------------
    public function index()
    {


        $this->model = new ServicoModel();

        $this->data =   [
                        'fields'    =>$this->model->where('status', 1)
                                                  ->paginate(12),
                        'pager'     =>$this->model->pager   
                        ];


        echo view('site/header.php');
        echo view('site/servicos.php', $this->data);
        echo view('site/footer.php');

    }

    //--------------------------------------------------------------------
    public function servicos($id)
    {

        switch ($id){
            case 'consultoria':
                $id = 22;
                break;
            case 'engenharia-de-software':
                $id = 23;
                break;
            case 'engenharia-sanitaria':
                $id = 24;
                break;
            case 'geologia':
                $id = 25;
                break;
        }

        $this->model = new ServicoModel();

        $this->data =   [
                        'fields'    =>$this->model->get($id)
                        ];


        echo view('site/header.php');
        echo view('site/servicos-detalhes.php', $this->data);
        echo view('site/footer.php');

    }




}
