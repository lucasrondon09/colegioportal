<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use App\Models\Admin\PaginasModel;
use App\Models\Admin\PaginasCategoriaModel;


class Paginas extends Controller
{
    protected $model;
    protected $modelCategoria;
    protected $session;
    protected $validation;
    protected $data;

    //--------------------------------------------------------------------
    public function __construct()
	{
        helper('auth');
        permission();

		$this->model	= new PaginasModel();
        $this->modelCategoria = new PaginasCategoriaModel();
        $this->session  = session();
        $this->validation = \Config\Services::validation();
	}

    //--------------------------------------------------------------------
    public function index()
    {
        helper('form');

        if($this->request->getMethod() === 'post'){
            $search = $this->request->getVar('search');

            $this->data = 	[
                            'table'     => $this->model->like('titulo', $search)
                                                        ->paginate(10),
                            'pager'      => $this->model->pager                                                            
                            ];

        }else{

            $this->data = 	[
                            'table'     => $this->model->paginate(10),
                            'pager'     => $this->model->pager
                            ];
        }

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/paginas/index.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    public function create()
    {
        helper('form');

       
        
        if($this->request->getMethod() === 'post'){

            if(csrf_hash() === $this->request->getVar('csrf_test_name'))
            {
                $rules = $this->validation->setRules    ([
                                                            'titulo'         => ['label' => 'Título', 'rules' => 'required|min_length[3]|max_length[255]'],
                                                            'idCategoria'     => ['label' => 'Categoria', 'rules' => 'required']
                                                        ]);

                if ($this->validation->withRequest($this->request)->run()){

                    if($this->save()){
                        $alert = 'success';
                        $message = 'O registro foi cadastrado com sucesso!';
                    }else{
                        $alert = 'error';
                        $message = 'Não foi possível salvar o registro tente novamente!';
                    }

                    $this->session->setFlashdata($alert, $message);
                    return redirect()->to('/Admin/Paginas/cadastrar');
                }

            }else{
                $alert = 'error';
                $message = 'Não foi possível salvar o registro, tente novamente!';

                $this->session->setFlashdata($alert, $message);
                return redirect()->to('/Admin/Paginas/cadastrar');
            }

           

        }

        $this->data =   [
                        'categoria' => $this->modelCategoria->get()
                        ];

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/paginas/create.php', $this->data);
        echo view('admin/template/footer.php');

    }


    //--------------------------------------------------------------------
    private function save()
    {

        $fields = 	$this->request->getPost();

        if($this->model->insert($fields)){

            return true;

        }

        return false;

    }

    public function edit($id)
    {

        helper('form');

        if($this->request->getMethod() === 'post'){


            $rules = $this->validation->setRules    ([
                                                        'titulo'         => ['label' => 'Título', 'rules' => 'required|min_length[3]|max_length[255]'],
                                                        'idCategoria'    => ['label' => 'Categoria', 'rules' => 'required']
                                                        
                                                    ]);                                                  

            if ($this->validation->withRequest($this->request)->run()){

                if($this->update($id)){
                    $alert = 'success';
                    $message = 'O registro foi atualizado com sucesso!';
                }else{
                    $alert = 'error';
                    $message = 'Não foi possível atualizar o registro!';
                }

                $this->session->setFlashdata($alert, $message);
                return redirect()->to('/Admin/Paginas/editar/'.$id);

            }
        }

        $this->data = 	[
                        'field'     => $this->model->get($id),
                        'categoria' => $this->modelCategoria->get()
                        ];

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/paginas/edit.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    private function update($id)
    {

        $fields = 	$this->request->getPost();

        if($this->model->update($id, $fields)){

            return true;

        }

        return false;

    }

    //--------------------------------------------------------------------
    public function delete($id)
    {

        if($this->deleted($id)){
            $alert = 'success';
            $message = 'Registro excluído com sucesso!';

        }else{
            $alert = 'error';
            $message = 'Não foi possível excluir o registro!';

        }

        $this->session->setFlashdata($alert, $message);
        return redirect()->to('/Admin/Paginas');

    }

    //--------------------------------------------------------------------
    private function deleted($id)
    {

        $delete = $this->model->delete($id);

        if($delete){

            return true;

        }
    
        return false;

    }
}
