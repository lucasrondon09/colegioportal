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
    public $contratoModel, $parcelaModel, $pagamentoModel, $formaPagamentoModel, $turmaModel, $paisModel, $alunoModel, $session, $validation, $data, $anoAtual, $anoLetivo, $listaAnos;

    public function __construct()
    {
        helper('auth');
        helper('parametros');
        helper('parcelas_adicional');
        helper('form');
        permission();

        $this->contratoModel    = new SI_ContratoModel();
        $this->turmaModel       = new SI_TurmaModel();
        $this->parcelaModel     = new SI_Parcelas_ContratoModel();
        $this->paisModel        = new SI_PaiModel();
        $this->alunoModel       = new SI_AlunoModel();

        // ADICIONAR ESTAS LINHAS:
        $this->pagamentoModel   = new \App\Models\Admin\SI_PagamentoModel();
        $this->formaPagamentoModel = new \App\Models\Admin\SI_FormaPagamentoModel();

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

        if ($contrato_vigente) {

            $this->data['modo'] = 'editar';
            $this->data['action'] = base_url('/Admin/Contrato/update/' . $contrato_vigente->id);




            $this->data['fields'] = $contrato_vigente;
            $this->data['fields']->valor_total = monetarioExibir($this->data['fields']->valor_total);
        }





        $this->data +=     [
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

                $fields =     $this->request->getVar();

                $fields['valor_total'] = number_format((float)preg_replace('/[^\d]/', '', $fields['valor_total']) / 100, 2, '.', '');
                $fields['status'] = 1; // Aberto 
                $fields['numero_contrato'] = $fields['id_aluno'] . $fields['id_responsavel'] . $fields['id_turma'] . date('Y');

                if ($this->contratoModel->insert($fields)) {
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

    public function lancamentos($id_contrato)
    {
        // Carregar models necessários
        if (!isset($this->pagamentoModel)) {
            $this->pagamentoModel = new \App\Models\Admin\SI_PagamentoModel();
        }
        if (!isset($this->formaPagamentoModel)) {
            $this->formaPagamentoModel = new \App\Models\Admin\SI_FormaPagamentoModel();
        }

        // Dados do contrato
        $this->data['contrato'] = $this->contratoModel
            ->select('si_contrato.*, si_aluno.nome as aluno_nome, si_aluno.id as aluno_id, si_pai.rm_resp_financeiro_nome as responsavel_nome, si_turma.nome as turma_nome, si_turma.id as turma_id,si_turma.ano as turma_ano, si_turma.id_periodo as id_periodo')
            ->join('si_aluno', 'si_aluno.id = si_contrato.id_aluno', 'left')
            ->join('si_turma', 'si_turma.id = si_contrato.id_turma', 'left')
            ->join('si_pai', 'si_pai.id = si_contrato.id_responsavel', 'left')
            ->where('si_contrato.id', $id_contrato)
            ->first();

        $this->data['idContrato'] = $id_contrato;

        // Buscar parcelas com informações de pagamento
        $this->data['lancamentos'] = $this->parcelaModel->getParcelasByContratoComPagamentos($id_contrato);

        // Cálculos financeiros
        $this->data['valorTotalParcelas'] = $this->parcelaModel->valorTotalParcelas($id_contrato);
        $this->data['valorTotalRecebido'] = $this->parcelaModel->valorTotalRecebido($id_contrato);
        $this->data['valorTotalVencido'] = $this->parcelaModel->valorTotalVencido($id_contrato);
        $this->data['valorTotalAReceber'] = $this->parcelaModel->valorTotalAReceber($id_contrato);

        // Contadores
        $this->data['totalParcelas'] = $this->parcelaModel->countParcelasByContrato($id_contrato);
        $this->data['totalParcelasAbertas'] = $this->parcelaModel->countParcelasByStatus($id_contrato, 1);
        $this->data['totalParcelasPagas'] = $this->parcelaModel->countParcelasByStatus($id_contrato, 2);
        $this->data['totalParcelasAtrasadas'] = $this->parcelaModel->countParcelasByStatus($id_contrato, 4);

        // Buscar pagamentos do contrato
        $this->data['pagamentos'] = $this->pagamentoModel->getPagamentosByContrato($id_contrato);

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

        if ($this->request->getMethod() === 'post') {

            $rules = $this->validation->setRules([
                'tipo_lancamento'      => ['label' => 'Tipo de Lançamento', 'rules' => 'required'],
                'parcelas'             => ['label' => 'Parcelas', 'rules' => 'required'],
                'data_emissao'         => ['label' => 'Data da Emissão', 'rules' => 'required'],
                'data_vencimento'      => ['label' => 'Data de Vencimento', 'rules' => 'required'],
                'valor_parcela'        => ['label' => 'Valor da Parcela', 'rules' => 'required'],
            ]);

            if ($this->validation->withRequest($this->request)->run()) {

                $fields =     $this->request->getVar();
                $valor_parcela = number_format((float)preg_replace('/[^\d]/', '', $fields['valor_parcela']) / 100, 2, '.', '');
                $data_emissao = new \DateTime($fields['data_emissao']);
                $data_vencimento = new \DateTime($fields['data_vencimento']);

                for ($i = 0; $i < (int)$fields['parcelas']; $i++) {

                    $data_parcela = clone $data_vencimento;
                    $data_parcela->modify('+' . $i . ' month');

                    $lancamento = [
                        'id_contrato'       => $id_contrato,
                        'tipo_lancamento'   => $fields['tipo_lancamento'],
                        'numero_parcela'    => $i + 1,
                        'data_emissao'      => $data_emissao->format('Y-m-d'),
                        'data_vencimento'   => $data_parcela->format('Y-m-d'),
                        'valor_parcela'     => $valor_parcela,
                        'status'            => 1, // Aberto
                        'data_pagamento'    => null,
                        'valor_pago'        => null,
                        'descricao'        => $fields['descricao' ?? null]
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


    public function registrarPagamento($id_parcela)
    {
        helper('form');

        // Carregar models necessários
        if (!isset($this->pagamentoModel)) {
            $this->pagamentoModel = new \App\Models\Admin\SI_PagamentoModel();
        }
        if (!isset($this->formaPagamentoModel)) {
            $this->formaPagamentoModel = new \App\Models\Admin\SI_FormaPagamentoModel();
        }

        $parcela = $this->parcelaModel->find($id_parcela);

        if (!$parcela) {
            $this->session->setFlashdata('error', 'Parcela não encontrada!');
            return redirect()->back();
        }

        // Buscar contrato
        $contrato = $this->contratoModel
            ->select('si_contrato.*, si_aluno.nome as aluno_nome')
            ->join('si_aluno', 'si_aluno.id = si_contrato.id_aluno', 'left')
            ->where('si_contrato.id', $parcela->id_contrato)
            ->first();

        if ($this->request->getMethod() === 'post') {

            $rules = $this->validation->setRules([
                'id_forma_pagamento' => ['label' => 'Forma de Pagamento', 'rules' => 'required|integer'],
                'data_pagamento'     => ['label' => 'Data de Pagamento', 'rules' => 'required|valid_date'],
                'valor_pago'         => ['label' => 'Valor Pago', 'rules' => 'required'],
                'desconto_aplicado'  => ['label' => 'Desconto', 'rules' => 'permit_empty'],
                'juros_aplicado'     => ['label' => 'Juros', 'rules' => 'permit_empty'],
                'multa_aplicada'     => ['label' => 'Multa', 'rules' => 'permit_empty']
            ]);

            if ($this->validation->withRequest($this->request)->run()) {

                $fields = $this->request->getVar();

                // Converter valores monetários
                $valor_pago = monetarioSalvar($fields['valor_pago']);
                $desconto = !empty($fields['desconto_aplicado']) ? monetarioSalvar($fields['desconto_aplicado']) : 0;
                $juros = !empty($fields['juros_aplicado']) ? monetarioSalvar($fields['juros_aplicado']) : 0;
                $multa = !empty($fields['multa_aplicada']) ? monetarioSalvar($fields['multa_aplicada']) : 0;

                // Calcular valor líquido
                $valor_liquido = $valor_pago - $desconto + $juros + $multa;

                $pagamento = [
                    'id_parcela'         => $id_parcela,
                    'id_forma_pagamento' => $fields['id_forma_pagamento'],
                    'data_pagamento'     => $fields['data_pagamento'],
                    'valor_pago'         => $valor_pago,
                    'desconto_aplicado'  => $desconto,
                    'juros_aplicado'     => $juros,
                    'multa_aplicada'     => $multa,
                    'valor_liquido'      => $valor_liquido,
                    'status'             => 'CONFIRMADO',
                    'observacao'         => $fields['observacao'] ?? null,
                    'created_by'         => session()->get('user_id') ?? null
                ];

                if ($this->pagamentoModel->insert($pagamento)) {
                    // Atualizar status da parcela
                    $this->parcelaModel->atualizarStatusParcela($id_parcela);

                    $alert = 'success';
                    $message = 'Pagamento registrado com sucesso!';
                } else {
                    $alert = 'error';
                    $message = 'Não foi possível registrar o pagamento!';
                }

                $this->session->setFlashdata($alert, $message);
                return redirect()->to(base_url('/Admin/Contrato/lancamentos/' . $parcela->id_contrato));
            }
        }

        // Buscar pagamentos já realizados desta parcela
        $pagamentos_parcela = $this->pagamentoModel->getPagamentosByParcela($id_parcela);
        $total_pago = $this->pagamentoModel->getTotalPagoParcela($id_parcela);
        $valor_restante = $parcela->valor_parcela - $total_pago;

        $this->data = [
            'parcela'            => $parcela,
            'contrato'           => $contrato,
            'formas_pagamento'   => $this->formaPagamentoModel->getFormasAtivas(),
            'pagamentos_parcela' => $pagamentos_parcela,
            'total_pago'         => $total_pago,
            'valor_restante'     => $valor_restante
        ];

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Contrato/registrar_pagamento.php', $this->data);
        echo view('admin/template/footer.php');
    }

    /**
     * Excluir uma parcela
     */
    public function excluirParcela($id_parcela)
    {
        // Carregar model de pagamentos
        if (!isset($this->pagamentoModel)) {
            $this->pagamentoModel = new \App\Models\Admin\SI_PagamentoModel();
        }

        $parcela = $this->parcelaModel->find($id_parcela);

        if (!$parcela) {
            $this->session->setFlashdata('error', 'Parcela não encontrada!');
            return redirect()->back();
        }

        // Verificar se há pagamentos registrados
        $pagamentos = $this->pagamentoModel->getPagamentosByParcela($id_parcela);

        if (!empty($pagamentos)) {
            $this->session->setFlashdata('error', 'Não é possível excluir uma parcela que possui pagamentos registrados!');
            return redirect()->to(base_url('/Admin/Contrato/lancamentos/' . $parcela->id_contrato));
        }

        if ($this->parcelaModel->delete($id_parcela)) {
            $alert = 'success';
            $message = 'Parcela excluída com sucesso!';
        } else {
            $alert = 'error';
            $message = 'Não foi possível excluir a parcela!';
        }

        $this->session->setFlashdata($alert, $message);
        return redirect()->to(base_url('/Admin/Contrato/lancamentos/' . $parcela->id_contrato));
    }

    /**
     * Visualizar detalhes de uma parcela
     */
    public function detalhesParcela($id_parcela)
    {
        // Carregar models necessários
        if (!isset($this->pagamentoModel)) {
            $this->pagamentoModel = new \App\Models\Admin\SI_PagamentoModel();
        }

        $parcela = $this->parcelaModel->find($id_parcela);

        if (!$parcela) {
            $this->session->setFlashdata('error', 'Parcela não encontrada!');
            return redirect()->back();
        }

        // Buscar contrato
        $contrato = $this->contratoModel
            ->select('si_contrato.*, si_aluno.nome as aluno_nome, si_pai.rm_resp_financeiro_nome as responsavel_nome')
            ->join('si_aluno', 'si_aluno.id = si_contrato.id_aluno', 'left')
            ->join('si_pai', 'si_pai.id = si_contrato.id_responsavel', 'left')
            ->where('si_contrato.id', $parcela->id_contrato)
            ->first();

        // Buscar pagamentos desta parcela
        $pagamentos = $this->pagamentoModel->getPagamentosByParcela($id_parcela);
        $total_pago = $this->pagamentoModel->getTotalPagoParcela($id_parcela);
        $valor_restante = $parcela->valor_parcela - $total_pago;

        $this->data = [
            'parcela'        => $parcela,
            'contrato'       => $contrato,
            'pagamentos'     => $pagamentos,
            'total_pago'     => $total_pago,
            'valor_restante' => $valor_restante
        ];

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Contrato/detalhes_parcela.php', $this->data);
        echo view('admin/template/footer.php');
    }


    /**
     * Editar Pagamento
     */
    public function editarPagamento($id_pagamento = null)
    {
        if (empty($id_pagamento)) {
            return redirect()->to(base_url('Admin/Contrato'))->with('error', 'Pagamento não encontrado!');
        }

        // Buscar pagamento
        $pagamento = $this->pagamentoModel->find($id_pagamento);

        if (empty($pagamento)) {
            return redirect()->to(base_url('Admin/Contrato'))->with('error', 'Pagamento não encontrado!');
        }

        // Buscar parcela relacionada
        $parcela = $this->parcelaModel->find($pagamento->id_parcela);

        if (empty($parcela)) {
            return redirect()->to(base_url('Admin/Contrato'))->with('error', 'Parcela não encontrada!');
        }

        // Buscar contrato
        $contrato = $this->contratoModel->getContratoById($parcela->id_contrato);

        if (empty($contrato)) {
            return redirect()->to(base_url('Admin/Contrato'))->with('error', 'Contrato não encontrado!');
        }

        // Processar formulário
        if ($this->request->getMethod() === 'post') {

            $rules = [
                'id_forma_pagamento' => 'required',
                'data_pagamento'     => 'required|valid_date',
                'valor_pago'         => 'required',
            ];

            if ($this->validate($rules)) {

                // Converter valores monetários
                $valor_pago = monetarioSalvar($this->request->getPost('valor_pago'));
                $desconto = monetarioSalvar($this->request->getPost('desconto_aplicado') ?? '0,00');
                $juros = monetarioSalvar($this->request->getPost('juros_aplicado') ?? '0,00');
                $multa = monetarioSalvar($this->request->getPost('multa_aplicada') ?? '0,00');

                // Calcular valor líquido
                $valor_liquido = $valor_pago - $desconto + $juros + $multa;

                $data = [
                    'id_forma_pagamento' => $this->request->getPost('id_forma_pagamento'),
                    'data_pagamento'     => $this->request->getPost('data_pagamento'),
                    'valor_pago'         => $valor_pago,
                    'desconto_aplicado'  => $desconto,
                    'juros_aplicado'     => $juros,
                    'multa_aplicada'     => $multa,
                    'valor_liquido'      => $valor_liquido,
                    'observacao'         => $this->request->getPost('observacao'),
                    'updated_at'         => date('Y-m-d H:i:s'),
                ];

                if ($this->pagamentoModel->update($id_pagamento, $data)) {

                    // Atualizar status da parcela
                    $this->parcelaModel->atualizarStatusParcela($parcela->id);

                    return redirect()
                        ->to(base_url('Admin/Contrato/detalhesParcela/' . $parcela->id))
                        ->with('success', 'Pagamento atualizado com sucesso!');
                } else {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Erro ao atualizar pagamento!');
                }
            } else {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Preencha todos os campos obrigatórios!');
            }
        }

        // Buscar formas de pagamento ativas
        $formas_pagamento = $this->formaPagamentoModel->getFormasAtivas();

        // Calcular totais
        $total_pago = $this->pagamentoModel->getTotalPagoParcela($parcela->id);
        $valor_restante = $parcela->valor_parcela - $total_pago + $pagamento->valor_pago; // Soma o valor do pagamento atual

        $data = [
            'titulo'             => 'Editar Pagamento',
            'pagamento'          => $pagamento,
            'parcela'            => $parcela,
            'contrato'           => $contrato,
            'formas_pagamento'   => $formas_pagamento,
            'total_pago'         => $total_pago,
            'valor_restante'     => $valor_restante,
        ];
        
        echo view('admin/template/header');
        echo view('admin/template/sidebar');
        echo view('admin/SI_Contrato/editar_pagamento', $data);
        echo view('admin/template/footer');
    }

    /**
     * Excluir Pagamento
     */
    public function excluirPagamento($id_pagamento = null)
    {
        if (empty($id_pagamento)) {
            return redirect()->to(base_url('Admin/Contrato'))->with('error', 'Pagamento não encontrado!');
        }

        // Buscar pagamento
        $pagamento = $this->pagamentoModel->find($id_pagamento);

        if (empty($pagamento)) {
            return redirect()->to(base_url('Admin/Contrato'))->with('error', 'Pagamento não encontrado!');
        }

        // Buscar parcela relacionada
        $parcela = $this->parcelaModel->find($pagamento->id_parcela);

        if (empty($parcela)) {
            return redirect()->to(base_url('Admin/Contrato'))->with('error', 'Parcela não encontrada!');
        }

        // Verificar se pode excluir (apenas se status for CONFIRMADO ou PENDENTE)
        if (isset($pagamento->status) && $pagamento->status == 'ESTORNADO') {
            return redirect()
                ->to(base_url('Admin/Contrato/detalhesParcela/' . $parcela->id))
                ->with('error', 'Não é possível excluir um pagamento estornado!');
        }

        // Excluir pagamento (soft delete)
        if ($this->pagamentoModel->delete($id_pagamento)) {

            // Atualizar status da parcela
            $this->parcelaModel->atualizarStatusParcela($parcela->id);

            return redirect()
                ->to(base_url('Admin/Contrato/detalhesParcela/' . $parcela->id))
                ->with('success', 'Pagamento excluído com sucesso!');
        } else {
            return redirect()
                ->to(base_url('Admin/Contrato/detalhesParcela/' . $parcela->id))
                ->with('error', 'Erro ao excluir pagamento!');
        }
    }

    /**
     * Estornar Pagamento
     */
    public function estornarPagamento($id_pagamento = null)
    {
        if (empty($id_pagamento)) {
            return redirect()->to(base_url('Admin/Contrato'))->with('error', 'Pagamento não encontrado!');
        }

        // Buscar pagamento
        $pagamento = $this->pagamentoModel->find($id_pagamento);

        if (empty($pagamento)) {
            return redirect()->to(base_url('Admin/Contrato'))->with('error', 'Pagamento não encontrado!');
        }

        // Buscar parcela relacionada
        $parcela = $this->parcelaModel->find($pagamento->id_parcela);

        if (empty($parcela)) {
            return redirect()->to(base_url('Admin/Contrato'))->with('error', 'Parcela não encontrada!');
        }

        // Verificar se já está estornado
        if (isset($pagamento->status) && $pagamento->status == 'ESTORNADO') {
            return redirect()
                ->to(base_url('Admin/Contrato/detalhesParcela/' . $parcela->id))
                ->with('error', 'Este pagamento já foi estornado!');
        }

        // Processar formulário de estorno
        if ($this->request->getMethod() === 'post') {

            $motivo = $this->request->getPost('motivo_estorno');

            if (empty($motivo)) {
                return redirect()
                    ->back()
                    ->with('error', 'Informe o motivo do estorno!');
            }

            // Estornar pagamento
            if ($this->pagamentoModel->estornarPagamento($id_pagamento, $motivo)) {

                // Atualizar status da parcela
                $this->parcelaModel->atualizarStatusParcela($parcela->id);

                return redirect()
                    ->to(base_url('Admin/Contrato/detalhesParcela/' . $parcela->id))
                    ->with('success', 'Pagamento estornado com sucesso!');
            } else {
                return redirect()
                    ->back()
                    ->with('error', 'Erro ao estornar pagamento!');
            }
        }

        // Buscar contrato
        $contrato = $this->contratoModel->getContratoCompleto($parcela->id_contrato);

        $data = [
            'titulo'   => 'Estornar Pagamento',
            'pagamento' => $pagamento,
            'parcela'   => $parcela,
            'contrato'  => $contrato,
        ];

        echo view('admin/template/header');
        echo view('admin/template/navbar');
        echo view('admin/template/sidebar');
        echo view('admin/SI_Contrato/estornar_pagamento', $data);
        echo view('admin/template/footer');
    }
}
