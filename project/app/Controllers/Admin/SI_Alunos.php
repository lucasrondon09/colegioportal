<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use App\Models\Admin\SI_AlunoModel;
use App\Models\Admin\SI_TurmaModel;
use App\Models\Admin\SI_ParametroModel;

class SI_Alunos extends Controller
{
    public $modelSI_Aluno, $data, $validation, $session;
    protected $helpers = ['form',  'auth'];
    //--------------------------------------------------------------------
    public function __construct()
	{
        helper($this->helpers);
        permission();
        $this->modelSI_Aluno = new SI_AlunoModel();
        $this->validation = \Config\Services::validation();
        $this->session  = session();
	}

    //--------------------------------------------------------------------
    public function index($idPai = null)
    {

        

        helper('form');

        if($search = $this->request->getVar('search')){
        

            $this->data = 	[
                            'table'     => $this->modelSI_Aluno->where('status', 1)
                                                                ->like('nome', $search)
                                                                ->orLike('email', $search)
                                                                ->paginate(10),

                            'pager'      => $this->modelSI_Aluno->pager                                                            
                            ];

        }else{

            if(!empty($idPai)){

                if(session()->sistema == 'sispai'){

                    if($idPai <> session()->userId){

                        return redirect()->back();

                    }
                }

                $this->data['table'] = $this->modelSI_Aluno->where('fk_pai', $idPai)->where('status', 1)->paginate(10);
                $this->data['idPai'] = $idPai;
            }else{
                $this->data['table'] = $this->modelSI_Aluno->where('status', 1)->paginate(10);
            }

            $this->data['pager'] = $this->modelSI_Aluno->pager;
        }

        $turmaModel = new SI_TurmaModel();
        $parametroModel = new SI_ParametroModel();
        $anoAtual = $parametroModel->find(1);
        $anoAtual = $anoAtual->valor;
        
        $this->data['turma'] = $turmaModel->where('ano > '.$anoAtual)
                                            ->where('status = 1')
                                            ->findAll();

    
        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Alunos/index.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    public function create($idPai)
    {
        helper('system');
        authSystem();
        helper('form');
        
        if($this->request->getMethod() === 'post'){

            if(csrf_hash() === $this->request->getVar('csrf_test_name'))
            {
                $rules = $this->validation->setRules    ([
                                                            'nome'         => ['label' => 'Nome', 'rules' => 'required|min_length[3]|max_length[255]']
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
                    return redirect()->to('/Admin/Alunos/cadastrar/'.$idPai);
                }

            }else{
                $alert = 'error';
                $message = 'Não foi possível salvar o registro, tente novamente!';

                $this->session->setFlashdata($alert, $message);
                return redirect()->to('/Admin/Alunos/cadastrar/'.$idPai);
            }

           

        }

        $this->data['idPai'] = $idPai;

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Alunos/create.php', $this->data);
        echo view('admin/template/footer.php');

    }


    //--------------------------------------------------------------------
    private function save()
    {

        $ultimo_registro = $this->modelSI_Aluno->select('id')->orderBy('id', 'DESC')->first();

        $ultimo_registro = (int)$ultimo_registro->id+1;

        $fields = 	$this->request->getVar();
        $foto   = 	$this->request->getFile('foto');

        if(!empty($foto->isFile())){
            $foto = $this->upload($foto) ;

            $fields['arquivo_foto'] = $foto;
        }
    
        
        $fields += 	[
                    'matricula' => $ultimo_registro.date('Y'),
                    'status'    => '1',
                    ];

                    

        return $this->modelSI_Aluno->insert($fields);                    

    }

    public function upload($img)
    {

        $img = $this->request->getFile('foto');

    
        if (!$img->hasMoved()) {
            $newName = $img->getRandomName();
            $img->move(FCPATH . '/assets/dist/img/arquivo_foto_aluno/', $newName);
            return $newName;
        }

    }

    //--------------------------------------------------------------------
    public function details($id)
    {

        $this->data = 	[
                        'field'     => $this->modelSI_Aluno->find($id)
                        ];

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Alunos/details.php', $this->data);
        echo view('admin/template/footer.php');

    }


    //--------------------------------------------------------------------
    public function edit($id)
    {
        helper('system');
        authSystem();

        helper('form');

        if($this->request->getMethod() === 'post'){

            $rules = $this->validation->setRules    ([
                                                        'nome'      => ['label' => 'Nome', 'rules' => 'required|min_length[3]|max_length[255]']
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
                return redirect()->to('/Admin/Alunos/editar/'.$id);

            }
        }

        $this->data = 	[
                        'field'     => $this->modelSI_Aluno->find($id)
                        ];

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Alunos/edit.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    private function update($id)
    {

        $fields = 	$this->request->getVar();        

        $foto   = 	$this->request->getFile('foto');

        if(!empty($foto->isFile())){
            $foto = $this->upload($foto) ;

            $fields['arquivo_foto'] = $foto;
        }


        return $this->modelSI_Aluno->update($id, $fields);


    }

    //--------------------------------------------------------------------
    public function delete($id)
    {
        helper('system');
        authSystem();

        if($this->deleted($id)){
            $alert = 'success';
            $message = 'Registro excluído com sucesso!';

        }else{
            $alert = 'error';
            $message = 'Não foi possível excluir o registro!';

        }

        $this->session->setFlashdata($alert, $message);
        return redirect()->to('/Admin/Alunos');

    }

    //--------------------------------------------------------------------
    private function deleted($id)
    {

        $fields['status'] = 2;

        return $this->modelSI_Aluno->update($id, $fields);

    }


}
