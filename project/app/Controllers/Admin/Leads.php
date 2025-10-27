<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use App\Models\Admin\LeadsModel;

class Leads extends Controller
{
    //--------------------------------------------------------------------
    public function __construct()
	{
        helper('auth');
        permission();

		$this->model	= new LeadsModel();
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
                                                        ->orLike('email', $search)
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
        echo view('admin/leads/index.php', $this->data);
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
                                                            'email'         => ['label' => 'E-mail', 'rules' => 'required|min_length[3]|max_length[255]']
                                                        ]);

                if ($this->validation->withRequest($this->request)->run()){



                    if($this->model->where('email', $this->request->getVar('email'))->find()){

                        $alert = 'error';
                        $message = 'O E-mail informado já está cadastrado no sistema!';

                    }else{

                        if($this->save()){
                            $alert = 'success';
                            $message = 'O registro foi cadastrado com sucesso!';
                        }else{
                            $alert = 'error';
                            $message = 'Não foi possível salvar o registro tente novamente!';
                        }
                    }

                    $this->session->setFlashdata($alert, $message);
                    return redirect()->to('/Admin/Leads/cadastrar');
                }

            }else{
                $alert = 'error';
                $message = 'Não foi possível salvar o registro, tente novamente!';

                $this->session->setFlashdata($alert, $message);
                return redirect()->to('/Admin/Leads/cadastrar');
            }

           

        }

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/leads/create.php');
        echo view('admin/template/footer.php');

    }


    //--------------------------------------------------------------------
    private function save()
    {

        $fields = 	$this->request->getVar();

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
                                                        'email'         => ['label' => 'E-mail', 'rules' => 'required|min_length[3]|max_length[255]'],
                                                        
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
                return redirect()->to('/Admin/Leads/editar/'.$id);

            }
        }

        $this->data = 	[
                        'field'     => $this->model->get($id)
                        ];

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/leads/edit.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    private function update($id)
    {

        $fields = 	$this->request->getVar();

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
        return redirect()->to('/Admin/Leads');

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
