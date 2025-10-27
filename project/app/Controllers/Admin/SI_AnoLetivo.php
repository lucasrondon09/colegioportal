<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use App\Models\Admin\SI_ParametroModel;

class SI_AnoLetivo extends Controller
{
    public $model, $session, $validation, $data, $anoAtual, $anoLetivo, $listaAnos;

    //--------------------------------------------------------------------
    public function __construct()
	{
        helper('auth');
        permission();

		$this->model	= new SI_ParametroModel();
        $this->session  = session();
        $this->validation = \Config\Services::validation();
        $this->anoAtual = date("Y");  

	}

    public function getListaAnos() {

        for ($ano = 2005; $ano <= ($this->anoAtual + 2); $ano++) {

            $this->listaAnos[] = $ano;

        }

        return $this->listaAnos;

    }


    //--------------------------------------------------------------------
    public function index()
    {
        helper('form');

        $this->data['listaAnos'] = $this->getListaAnos();

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_AnoLetivo/index.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    public function save(){

        $anoLetivo = $this->request->getPost('anoLetivo');

        $fields = ['valor' => $anoLetivo];

        $update = $this->model->update(1, $fields);

        if($update){
            $alert = 'success';
            $message = 'O registro foi cadastrado com sucesso!';
            }else{
            $alert = 'error';
            $message = 'Não foi possível salvar o registro tente novamente!';
            }

            $this->session->setFlashdata($alert, $message);
            return redirect()->to('/Admin/Ano-Letivo');
    }

}
