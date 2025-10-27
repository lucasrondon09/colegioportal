<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use App\Models\Admin\UsuarioModel;

class Usuarios extends Controller
{
    //--------------------------------------------------------------------
    public function __construct()
	{
        helper('auth');
        permission();

		$this->model	= new UsuarioModel();
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
                            'table'     => $this->model->like('nome', $search)
                                                        ->orLike('login', $search)
                                                        ->groupBy('','')
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
        echo view('admin/usuarios/index.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    public function create()
    {
        helper('form');
        

        //csrf_hash ();
        
        if($this->request->getMethod() === 'post'){

            if(csrf_hash() === $this->request->getVar('csrf_test_name'))
            {
                $rules = $this->validation->setRules    ([
                                                            'nome'         => ['label' => 'Nome', 'rules' => 'required|min_length[3]|max_length[255]'],
                                                            'login'        => ['label' => 'Login', 'rules' => 'required|min_length[3]|max_length[255]'],
                                                            'senha'        => ['label' => 'Senha', 'rules' => 'required|min_length[6]|max_length[12]'],
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
                    return redirect()->to('/Admin/Usuarios/cadastrar');
                }

            }else{
                $alert = 'error';
                $message = 'Não foi possível salvar o registro, tente novamente!';

                $this->session->setFlashdata($alert, $message);
                return redirect()->to('/Admin/Usuarios/cadastrar');
            }

           

        }

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/usuarios/create.php');
        echo view('admin/template/footer.php');

    }


    //--------------------------------------------------------------------
    private function save()
    {

        $fields = 	[
                    'nome'  => $this->request->getVar('nome'),
                    'login' => $this->request->getVar('login'),
                    'senha' => password_hash($this->request->getVar('senha'), PASSWORD_DEFAULT)
                    ];

        if($this->model->insert($fields)){

            return true;

        }

        return false;

    }

    public function edit($id)
    {

        helper('form');

        if($this->request->getMethod() === 'post'){

            if(!empty($this->request->getVar('senha'))){
                $senha = "min_length[6]|max_length[12]";
            }else{
                $senha = "max_length[12]";
            }

            $rules = $this->validation->setRules    ([
                                                        'nome'         => ['label' => 'Nome', 'rules' => 'required|min_length[3]|max_length[255]'],
                                                        'login'        => ['label' => 'Login', 'rules' => 'required|min_length[3]|max_length[255]'],
                                                        'senha'        => ['senha' => 'Senha', 'rules' => $senha]
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
                return redirect()->to('/Admin/Usuarios/editar/'.$id);

            }
        }

        $this->data = 	[
                        'field'     => $this->model->get($id)
                        ];

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/usuarios/edit.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    private function update($id)
    {
        

        $fields = 	[
                    'nome'  => $this->request->getVar('nome'),
                    'login' => $this->request->getVar('login')
                    ];

        if(!empty($this->request->getVar('senha'))){

            $fields  +=      [
                            'senha' => password_hash($this->request->getVar('senha'), PASSWORD_DEFAULT)
                            ];                    
        }
        
 
        


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
        return redirect()->to('/Admin/Usuarios');

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
