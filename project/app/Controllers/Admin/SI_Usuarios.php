<?php

namespace App\Controllers\Admin;

use App\Models\Admin\SI_ProfessorTurmaModel;
use CodeIgniter\Controller;
use App\Models\Admin\SI_UsuarioModel;

class SI_Usuarios extends Controller
{
    private $model, $session, $validation, $data, $ProfessorTurmaDisciplinamodel;

    

    //--------------------------------------------------------------------
    public function __construct()
	{
        helper('auth');
        permission();

		$this->model	= new SI_UsuarioModel();
		$this->ProfessorTurmaDisciplinamodel	= new SI_ProfessorTurmaModel();
        $this->session  = session();
        $this->validation = \Config\Services::validation();

        define('PERMISSAO', [
                                '1' => 'Direção',
                                '2' => 'Secretaria',
                                '3' => 'Professor',
                                '4' => 'Coordenação'
                            ]);
    $this->data['permissao'] = PERMISSAO;

	}

    //--------------------------------------------------------------------
    public function index()
    {
        helper('form');

        if($search = $this->request->getVar('search')){

            $this->data += 	[
                            'table'     => $this->model->where('status', 1)
                                                        ->groupStart()
                                                            ->like('nome', $search)
                                                            ->orLike('usuario', $search)
                                                        ->groupEnd()
                                                        ->paginate(10),

                            'pager'      => $this->model->pager                                                            
                            ];

        }else{

            $this->data += 	[
                            'table'     => $this->model->where('status', 1)->paginate(10),
                            'pager'     => $this->model->pager
                            ];
        }

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Usuarios/index.php', $this->data);
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
                                                            'nome'         => ['label' => 'Nome', 'rules' => 'required|min_length[3]|max_length[255]'],
                                                            'usuario'      => ['label' => 'Usuário', 'rules' => 'required|min_length[3]|max_length[255]'],
                                                            'senha'        => ['label' => 'Senha', 'rules' => 'required|min_length[6]|max_length[45]'],
                                                            'permissao'    => ['label' => 'Permissão', 'rules' => 'required'],
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
        echo view('admin/SI_Usuarios/create.php', $this->data);
        echo view('admin/template/footer.php');

    }


    //--------------------------------------------------------------------
    private function save()
    {

        $fields = 	$this->request->getVar();
        $fields['status'] =  1;

        return $this->model->insert($fields);

    }


    public function visualizar($id){

        $this->data['visualizar'] = true;

        $this->edit($id);

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
                                                        'usuario'      => ['label' => 'Usuário', 'rules' => 'required|min_length[3]|max_length[255]'],
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

        $this->data += 	[
                        'field'     => $this->model->find($id)
                        ];


        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Usuarios/edit.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    private function update($id)
    {

        $fields = 	[
                    'nome'  => $this->request->getVar('nome'),
                    'usuario' => $this->request->getVar('usuario'),
                    'senha' => $this->request->getVar('senha'),
                    'permissao' => $this->request->getVar('permissao')
                    ];       
                    
                    
        if(!empty($this->request->getVar('turma_disciplina'))){

            $turmaDisciplina = $this->request->getVar('turma_disciplina');

            $fieldsPTD['fk_professor'] = $id;

            foreach($turmaDisciplina as $turmaDisciplinaItem){

                $arr =  explode("_", $turmaDisciplinaItem);
                $fieldsPTD['fk_turma'] = $arr[0];
                $fieldsPTD['id_disciplina'] = $arr[1];

                
                $this->ProfessorTurmaDisciplinamodel->insert($fieldsPTD);
        
            }
            
        }                    

        return $this->model->update($id, $fields);


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

        $fields['status'] = 2;

        return $this->model->update($id, $fields);

    }


}
