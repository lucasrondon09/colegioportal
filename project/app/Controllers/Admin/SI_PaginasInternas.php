<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use App\Models\Admin\SITE_ArquivoModel;
use App\Models\Admin\SITE_PaginaArquivoModel;
use App\Models\Admin\SITE_PaginaModel;

class SI_PaginasInternas extends Controller
{
    public $modelPagina, $modelArquivo, $modelPaginaArquivo, $data, $pagina, $arquivo;

    //--------------------------------------------------------------------
    public function __construct()
	{
        helper(['auth', 'form']);
        permission();

        $this->modelPagina = new SITE_PaginaModel();
        $this->modelArquivo = new SITE_ArquivoModel();
        $this->modelPaginaArquivo = new SITE_PaginaArquivoModel();


	}

    //--------------------------------------------------------------------
    public function index($paginaId, $arquivo = null)
    {
        helper('form');

        $arquivos = $this->getPaginaArquivo($paginaId);
        

        if(empty($arquivo)){
            
            foreach($arquivos as $arquivosItem){
                $this->data['arquivos'][] = $this->getArquivo($arquivosItem->fk_arquivo);
            }

        }else{

            $this->data['arquivos'] = null;
            
        }

        
        

        $this->data['pagina'] = $this->getPagina($paginaId);

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_PaginasInternas/index.php', $this->data);
        echo view('admin/template/footer.php');

    }

    public function getPagina($paginaId){
        
        return $this->modelPagina->find($paginaId);

    }

    public function getPaginaArquivo($paginaId){
        return $this->modelPaginaArquivo->where('fk_pagina', $paginaId)->findAll();
    }

    public function getArquivo($arquivoId){
        return $this->modelArquivo->find($arquivoId);
    }

    public function getPaginasInternas(){
        return $this->modelPagina->findAll();
    }

    public function create($paginaId){
        
        $this->data['pagina'] = $this->getPagina($paginaId);

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_PaginasInternas/create.php', $this->data);
        echo view('admin/template/footer.php');
        
    }

    public function delete($paginaId, $arquivoId){

        $deletePaginaArquivo = $this->modelPaginaArquivo->where('fk_arquivo', $arquivoId)->delete();

        if($deletePaginaArquivo){
            $deleteArquivo = $this->modelArquivo->delete($arquivoId);

            if($deleteArquivo){
                $alert = 'success';
                $message = 'Registro excluído com sucesso!';
    
            }else{
                $alert = 'error';
                $message = 'Não foi possível excluir o registro!';
    
            }
    
            session()->setFlashdata($alert, $message);
            return redirect()->to(base_url('Admin/Paginas-Internas/').'/'.$paginaId); 
            return redirect()->to('/Admin/Noticias');
        }
        
        return false;
        
    }
    
    

    //--------------------------------------------------------------------
    public function save(){

        $paginaId = $this->request->getPost('paginaId');
        $descricao = $this->request->getPost('descricao');
        $ordem = $this->request->getPost('ordem');
        $dataSave = date('Y-m-d H:i:s');
        $file = $this->request->getFile('nome_arquivo');

        $validation = \Config\Services::validation();

        $validation->setRules    ([
                                    'descricao'     => ['label' => 'Descrição', 'rules' => 'required|min_length[3]|max_length[255]']
                                ]);

        if ($validation->withRequest($this->request)->run()){

            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(FCPATH . 'uploads/arquivo_download/', $newName);

                $fields = $this->request->getPost();
                $fields['arquivo_nome'] = $newName;

                $fields =   [
                                'descricao'     => $descricao,
                                'nome_arquivo'  => $newName,
                                'data'          => $dataSave,
                                'post'          => '',
                                'ordem'         => $ordem
                            ];

                $save       = $this->modelArquivo->insert($fields, false);
                

                if($save){

                    $insertedID = $this->modelArquivo->insertID();
                    if(!empty($insertedID)){
                        $fields =   [
                                    'fk_pagina'     => $paginaId,
                                    'fk_arquivo'    => $insertedID
                                    ];
                        $savePaginaArquivo = $this->modelPaginaArquivo->insert($fields);
                    }
                    $alert = 'success';
                    $message = 'O registro foi cadastrado com sucesso!';
                }else{
                    $alert = 'error';
                    $message = 'Não foi possível salvar o registro tente novamente!';
                }    

                session()->setFlashdata($alert, $message);
                return redirect()->to(base_url('Admin/Paginas-Internas/').'/'.$paginaId);                        
            }else {
                return redirect()->back()->with('error', $file->getErrorString());
            }
        }else {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

    }

}
