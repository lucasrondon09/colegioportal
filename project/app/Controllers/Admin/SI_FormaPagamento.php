<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use App\Models\Admin\SI_FormaPagamentoModel;

class SI_FormaPagamento extends Controller
{
    protected $model;
    protected $session;
    protected $validation;
    protected $data;

    //--------------------------------------------------------------------
    public function __construct()
	{
        helper('auth');
        permission();

		$this->model	= new SI_FormaPagamentoModel();
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
                                                        ->orLike('descricao', $search)
                                                        ->orderBy('ordem_exibicao', 'ASC')
                                                        ->paginate(10),
                            'pager'      => $this->model->pager                                                            
                            ];

        }else{

            $this->data = 	[
                            'table'     => $this->model->orderBy('ordem_exibicao', 'ASC')->paginate(10),
                            'pager'     => $this->model->pager
                            ];
        }

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_FormaPagamento/index.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    public function create()
    {
        helper('form');
        
        if($this->request->getMethod() === 'post'){

            if(csrf_hash() === $this->request->getVar('csrf_test_name'))
            {
                $rules = $this->validation->setRules([
                    'nome'              => ['label' => 'Nome', 'rules' => 'required|min_length[3]|max_length[50]'],
                    'descricao'         => ['label' => 'Descrição', 'rules' => 'permit_empty'],
                    'taxa_percentual'   => ['label' => 'Taxa (%)', 'rules' => 'permit_empty|decimal'],
                    'prazo_compensacao' => ['label' => 'Prazo de Compensação', 'rules' => 'permit_empty|integer'],
                    'ordem_exibicao'    => ['label' => 'Ordem de Exibição', 'rules' => 'permit_empty|integer']
                ]);

                if ($this->validation->withRequest($this->request)->run()){

                    if($this->save()){
                        $alert = 'success';
                        $message = 'A forma de pagamento foi cadastrada com sucesso!';
                    }else{
                        $alert = 'error';
                        $message = 'Não foi possível salvar o registro, tente novamente!';
                    }

                    $this->session->setFlashdata($alert, $message);
                    return redirect()->to('/Admin/SI_FormaPagamento/cadastrar');
                }

            }else{
                $alert = 'error';
                $message = 'Não foi possível salvar o registro, tente novamente!';

                $this->session->setFlashdata($alert, $message);
                return redirect()->to('/Admin/SI_FormaPagamento/cadastrar');
            }

        }

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_FormaPagamento/create.php');
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    private function save()
    {
        $fields = $this->request->getPost();
        
        // Garantir que ativo seja boolean
        $fields['ativo'] = isset($fields['ativo']) ? 1 : 0;

        if($this->model->insert($fields)){
            return true;
        }

        return false;
    }

    //--------------------------------------------------------------------
    public function edit($id)
    {
        helper('form');

        if($this->request->getMethod() === 'post'){

            $rules = $this->validation->setRules([
                'nome'              => ['label' => 'Nome', 'rules' => 'required|min_length[3]|max_length[50]'],
                'descricao'         => ['label' => 'Descrição', 'rules' => 'permit_empty'],
                'taxa_percentual'   => ['label' => 'Taxa (%)', 'rules' => 'permit_empty|decimal'],
                'prazo_compensacao' => ['label' => 'Prazo de Compensação', 'rules' => 'permit_empty|integer'],
                'ordem_exibicao'    => ['label' => 'Ordem de Exibição', 'rules' => 'permit_empty|integer']
            ]);

            if ($this->validation->withRequest($this->request)->run()){

                if($this->update($id)){
                    $alert = 'success';
                    $message = 'A forma de pagamento foi atualizada com sucesso!';
                }else{
                    $alert = 'error';
                    $message = 'Não foi possível atualizar o registro!';
                }

                $this->session->setFlashdata($alert, $message);
                return redirect()->to('/Admin/SI_FormaPagamento/editar/'.$id);

            }
        }

        $this->data = 	[
                        'field'     => $this->model->find($id)
                        ];

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_FormaPagamento/edit.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    private function update($id)
    {
        $fields = $this->request->getPost();
        
        // Garantir que ativo seja boolean
        $fields['ativo'] = isset($fields['ativo']) ? 1 : 0;

        if($this->model->update($id, $fields)){
            return true;
        }

        return false;
    }

    //--------------------------------------------------------------------
    public function delete($id)
    {
        // Verificar se a forma de pagamento está em uso
        if($this->model->isEmUso($id)){
            $alert = 'error';
            $message = 'Não é possível excluir esta forma de pagamento pois existem pagamentos registrados com ela!';
        }else{
            if($this->deleted($id)){
                $alert = 'success';
                $message = 'Forma de pagamento excluída com sucesso!';
            }else{
                $alert = 'error';
                $message = 'Não foi possível excluir o registro!';
            }
        }

        $this->session->setFlashdata($alert, $message);
        return redirect()->to('/Admin/SI_FormaPagamento');
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

    //--------------------------------------------------------------------
    public function toggleStatus($id)
    {
        $forma = $this->model->find($id);
        
        if($forma){
            $novoStatus = !$forma->ativo;
            
            if($this->model->update($id, ['ativo' => $novoStatus])){
                $alert = 'success';
                $message = 'Status atualizado com sucesso!';
            }else{
                $alert = 'error';
                $message = 'Não foi possível atualizar o status!';
            }
        }else{
            $alert = 'error';
            $message = 'Forma de pagamento não encontrada!';
        }

        $this->session->setFlashdata($alert, $message);
        return redirect()->to('/Admin/SI_FormaPagamento');
    }
}

