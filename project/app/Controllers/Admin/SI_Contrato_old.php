<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use App\Models\Admin\SI_ContratoModel;
use App\Models\Admin\SI_AlunoModel;
use App\Models\Admin\SI_PaiModel;
use App\Models\Admin\SI_TurmaModel;
use App\Models\Admin\SI_Parcelas_ContratoModel;


class SI_Contrato extends Controller
{
    public $contratoModel, $parcelaModel, $turmaModel, $paisModel, $alunoModel, $session, $validation, $data, $anoAtual, $anoLetivo, $listaAnos;

    //--------------------------------------------------------------------
    public function __construct()
	{
        helper('auth');
        helper('parametros');
        helper('form');
        permission();

		$this->contratoModel	= new SI_ContratoModel();
        $this->turmaModel	= new SI_TurmaModel();
        $this->parcelaModel = new SI_Parcelas_ContratoModel();
        $this->paisModel = new SI_PaiModel();
        $this->alunoModel	= new SI_AlunoModel();
        $this->session  = session();
        $this->validation = \Config\Services::validation();
        $this->anoAtual = date("Y");  

	}

    //--------------------------------------------------------------------
    public function index()
    {
        $contratos = $this->contratoModel
            ->select('si_contrato.*, si_aluno.nome as aluno_nome, si_turma.nome as turma_nome, si_turma.ano as turma_ano, si_pai.rm_resp_financeiro_nome as responsavel_nome')
            ->join('si_pai', 'si_pai.id = si_contrato.id_responsavel')
            ->join('si_aluno', 'si_aluno.id = si_contrato.id_aluno')
            ->join('si_turma', 'si_turma.id = si_contrato.id_turma')
            ->orderBy('si_turma.ano', 'desc')
            ->findAll();

        $this->data['table'] = $contratos;

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Contrato/index.php', $this->data);
        echo view('admin/template/footer.php');
    }


    //--------------------------------------------------------------------
    public function create($id_turma, $id_aluno)
    {
        $this->data['modo'] = 'cadastrar';
        $this->data['action'] = base_url('/Admin/Contrato/save');

        $contrato_vigente = $this->contratoModel->where(['id_aluno' => $id_aluno, 'id_turma' => $id_turma])->first();

        if($contrato_vigente){
            
            $this->data['modo'] = 'editar';
            $this->data['action'] = base_url('/Admin/Contrato/update/' . $contrato_vigente->id);   

            
            

            $this->data['fields'] = $contrato_vigente;
            $this->data['fields']->valor_total = monetarioExibir($this->data['fields']->valor_total);

        }

        

        

        $this->data += 	[
                        'turma'     => $this->turmaModel->find($id_turma),
                        'aluno'     => $this->alunoModel->find($id_aluno),
                        ];
        $this->data['responsavel'] =  $this->paisModel->find($this->data['aluno']->fk_pai);                

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Contrato/create.php', $this->data);
        echo view('admin/template/footer.php');

    }
    

    //--------------------------------------------------------------------
    public function save()
    {
        helper('form');

        $alert = 'danger';
        $message = 'Não foi possível gerar o contrato!';

        if($this->request->getMethod() === 'post'){

            $rules = $this->validation->setRules    ([
                                                        'data_inicio'      => ['label' => 'Data Inicio', 'rules' => 'required'],
                                                        'data_fim'         => ['label' => 'Data Final', 'rules' => 'required'],
                                                        'parcelas'         => ['label' => 'Parcelas', 'rules' => 'required'],
                                                        'dia_vencimento'   => ['label' => 'Dia Vencimento', 'rules' => 'required'],
                                                        'valor_total'      => ['label' => 'Valor Total', 'rules' => 'required'],
                                                        'id_aluno'         => ['label' => 'Aluno', 'rules' => 'required'],
                                                        'id_responsavel'   => ['label' => 'Responsável Financeiro', 'rules' => 'required'],
                                                        'id_turma'         => ['label' => 'Turma', 'rules' => 'required'],
                                                        'tipo_contrato'    => ['label' => 'Tipo do Contrato', 'rules' => 'required']
                                                    ]);                                                  

            if ($this->validation->withRequest($this->request)->run()){

                $fields = 	$this->request->getVar();   
                
                $fields['valor_total'] = number_format((float)preg_replace('/[^\d]/', '', $fields['valor_total']) / 100, 2, '.', '');
                $fields['status'] = 1; // Aberto 
                $fields['numero_contrato'] = $fields['id_aluno'] . $fields['id_responsavel'] . $fields['id_turma'] . date('Y');

                if($this->contratoModel->insert($fields)){
                    $alert = 'success';
                    $message = 'O contrato foi gerado com sucesso!';
                }


            }    
        }  

        $this->session->setFlashdata($alert, $message);
        
        return redirect()->back();
    }

    public function update($id)
    {
        helper('form');

        $alert = 'danger';
        $message = 'Não foi possível atualizar o contrato!';

        if ($this->request->getMethod() === 'post') {

            $rules = $this->validation->setRules([
                'data_inicio'      => ['label' => 'Data Inicio', 'rules' => 'required'],
                'data_fim'         => ['label' => 'Data Final', 'rules' => 'required'],
                'parcelas'         => ['label' => 'Parcelas', 'rules' => 'required'],
                'dia_vencimento'   => ['label' => 'Dia Vencimento', 'rules' => 'required'],
                'valor_total'      => ['label' => 'Valor Total', 'rules' => 'required'],
                'id_aluno'         => ['label' => 'Aluno', 'rules' => 'required'],
                'id_responsavel'   => ['label' => 'Responsável Financeiro', 'rules' => 'required'],
                'id_turma'         => ['label' => 'Turma', 'rules' => 'required'],
                'tipo_contrato'    => ['label' => 'Tipo do Contrato', 'rules' => 'required']
            ]);

            if ($this->validation->withRequest($this->request)->run()) {

                $fields = $this->request->getVar();
                $fields['valor_total'] = monetarioSalvar($fields['valor_total']);

                if ($this->contratoModel->update($id, $fields)) {
                    $alert = 'success';
                    $message = 'O contrato foi atualizado com sucesso!';
                }
            }
        }

        $this->session->setFlashdata($alert, $message);

        return redirect()->back();
    }

    //--------------------------------------------------------------------
    public function lancamentos($id_contrato)
    {


        $this->data['contrato'] = $this->contratoModel
                                        ->select('si_contrato.*, si_aluno.nome as aluno_nome, si_pai.rm_resp_financeiro_nome as responsavel_nome, si_turma.nome as turma_nome, si_turma.ano as turma_ano, si_turma.id_periodo as id_periodo')
                                        ->join('si_aluno', 'si_aluno.id = si_contrato.id_aluno', 'left')
                                        ->join('si_turma', 'si_turma.id = si_contrato.id_turma', 'left')
                                        ->join('si_pai', 'si_pai.id = si_contrato.id_responsavel', 'left')
                                        ->where('si_contrato.id', $id_contrato)
                                        ->first();

        $this->data['idContrato'] = $id_contrato;      
        
        $this->data['lancamentos'] = $this->parcelaModel->getParcelasByContrato($id_contrato);
        $this->data['valorTotalParcelas'] = $this->parcelaModel->valorTotalParcelas($id_contrato);
        $this->data['totalParcelas'] = $this->parcelaModel->countParcelasByContrato($id_contrato);

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Contrato/lancamentos.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    public function lancamentosCadastrar($id_contrato)
    {
        helper('form');

        $this->data['contrato'] = $this->contratoModel
                                        ->select('si_contrato.*, si_aluno.nome as aluno_nome, si_pai.rm_resp_financeiro_nome as responsavel_nome, si_turma.nome as turma_nome, si_turma.ano as turma_ano, si_turma.id_periodo as id_periodo')
                                        ->join('si_aluno', 'si_aluno.id = si_contrato.id_aluno', 'left')
                                        ->join('si_turma', 'si_turma.id = si_contrato.id_turma', 'left')
                                        ->join('si_pai', 'si_pai.id = si_contrato.id_responsavel', 'left')
                                        ->where('si_contrato.id', $id_contrato)
                                        ->first();

        $this->data['idContrato'] = $id_contrato;

        

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Contrato/lancamentos_cadastrar.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    public function lancamentosSalvar($id_contrato)
    {
        helper('form');

        $alert = 'danger';
        $message = 'Não foi possível salvar os lançamentos!';

        if($this->request->getMethod() === 'post'){

            $rules = $this->validation->setRules    ([
                                                        'tipo_lancamento'      => ['label' => 'Tipo de Lançamento', 'rules' => 'required'],
                                                        'parcelas'             => ['label' => 'Parcelas', 'rules' => 'required'],
                                                        'data_vencimento'      => ['label' => 'Data de Vencimento', 'rules' => 'required'],
                                                        'valor_parcela'        => ['label' => 'Valor da Parcela', 'rules' => 'required'],
                                                    ]);                                                  

            if ($this->validation->withRequest($this->request)->run()){

                $fields = 	$this->request->getVar();   
                $valor_parcela = number_format((float)preg_replace('/[^\d]/', '', $fields['valor_parcela']) / 100, 2, '.', '');
                $data_vencimento = new \DateTime($fields['data_vencimento']);
                
                for($i = 0; $i < (int)$fields['parcelas']; $i++){

                    $data_parcela = clone $data_vencimento;
                    $data_parcela->modify('+' . $i . ' month');

                    $lancamento = [
                        'id_contrato'       => $id_contrato,
                        'id_forma_pagamento'=> 1,
                        'tipo_lancamento'   => $fields['tipo_lancamento'],
                        'numero_parcela'    => $i + 1,
                        'data_vencimento'   => $data_parcela->format('Y-m-d'),
                        'valor_parcela'     => $valor_parcela,
                        'status'            => 1, // Aberto
                        'data_pagamento'    => null,
                        'valor_pago'        => null,
                    ];

                    $this->parcelaModel->insert($lancamento);

                }

                $alert = 'success';
                $message = 'Os lançamentos foram salvos com sucesso!';

            }    
        }  

        $this->session->setFlashdata($alert, $message);
        
        return redirect()->to(base_url('/Admin/Contrato/lancamentos/' . $id_contrato));
    }

    

}
