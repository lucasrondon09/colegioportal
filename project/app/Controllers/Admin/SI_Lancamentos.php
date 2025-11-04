<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Admin\SI_Parcelas_ContratoModel;
use App\Models\Admin\SI_TipoLancamentoModel;
use App\Models\Admin\SI_PagamentoModel;
use App\Models\Admin\SI_FormaPagamentoModel;
use App\Models\Admin\SI_AlunoModel;
use App\Models\Admin\SI_PaiModel;
use App\Models\Admin\SI_TurmaModel;

class SI_Lancamentos extends BaseController
{
    protected $parcelaModel;
    protected $tipoLancamentoModel;
    protected $pagamentoModel;
    protected $formaPagamentoModel;
    protected $alunoModel;
    protected $paiModel;
    protected $turmaModel;

    public function __construct()
    {
        helper('auth');
        permission();

        $this->parcelaModel = new SI_Parcelas_ContratoModel();
        $this->tipoLancamentoModel = new SI_TipoLancamentoModel();
        $this->pagamentoModel = new SI_PagamentoModel();
        $this->formaPagamentoModel = new SI_FormaPagamentoModel();
        $this->alunoModel = new SI_AlunoModel();
        $this->paiModel = new SI_PaiModel();
        $this->turmaModel = new SI_TurmaModel();
    }

    /**
     * Listagem de lançamentos
     */
    public function index()
    {
        // Capturar filtros
        $filtros = [
            'status' => $this->request->getGet('status'),
            'tipo_lancamento' => $this->request->getGet('tipo_lancamento'),
            'id_aluno' => $this->request->getGet('id_aluno'),
            'data_vencimento_inicio' => $this->request->getGet('data_inicio'),
            'data_vencimento_fim' => $this->request->getGet('data_fim'),
            'search' => $this->request->getGet('search'),
            'boleto_gerado' => $this->request->getGet('boleto_gerado'),
            'origem' => $this->request->getGet('origem')
        ];

        // Remover filtros vazios
        $filtros = array_filter($filtros, function ($value) {
            return $value !== null && $value !== '';
        });

        // Buscar com paginação
        $perPage = 20;

        // Aplicar filtros manualmente
        $query = $this->parcelaModel;

        if (!empty($filtros['search'])) {
            $query = $query->groupStart()
                ->like('numero_documento', $filtros['search'])
                ->orLike('descricao', $filtros['search'])
                ->groupEnd();
        }

        if (!empty($filtros['status'])) {
            $query = $query->where('status', $filtros['status']);
        }

        if (!empty($filtros['tipo_lancamento'])) {
            $query = $query->where('tipo_lancamento', $filtros['tipo_lancamento']);
        }

        if (!empty($filtros['data_inicio'])) {
            $query = $query->where('data_vencimento >=', $filtros['data_inicio']);
        }

        if (!empty($filtros['data_fim'])) {
            $query = $query->where('data_vencimento <=', $filtros['data_fim']);
        }

        // Paginar
        $lancamentos = $query->orderBy('data_vencimento', 'DESC')
            ->paginate($perPage);
        $pager = $this->parcelaModel->pager;


        // Buscar resumo financeiro
        $resumo = $this->parcelaModel->getResumoFinanceiro($filtros);

        // Buscar tipos de lançamento para filtro
        $tipos_lancamento = $this->tipoLancamentoModel->getTiposAtivos();

        $data = [
            'titulo' => 'Gestão de Lançamentos Financeiros',
            'lancamentos' => $lancamentos,
            'pager' => $pager,
            'resumo' => $resumo,
            'tipos_lancamento' => $tipos_lancamento,
            'filtros' => $filtros
        ];

        return view('admin/template/header')
            . view('admin/template/sidebar')
            . view('admin/SI_Lancamentos/index', $data)
            . view('admin/template/footer');
    }

    /**
     * Criar novo lançamento avulso
     */
    public function cadastrar()
    {
        if ($this->request->getMethod() === 'post') {
            $dados = $this->request->getPost();

            // Validar
            $regras = [
                'tipo_lancamento' => 'required|integer',
                'id_aluno' => 'required|integer',
                'descricao' => 'required|min_length[3]',
                'valor_parcela' => 'required',
                'data_vencimento' => 'required|valid_date'
            ];

            if (!$this->validate($regras)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Buscar dados do aluno
            $aluno = $this->alunoModel->find($dados['id_aluno']);
            $aluno = !is_array($aluno) ? (array)$aluno : $aluno;
            if (!$aluno) {
                return redirect()->back()->withInput()->with('error', 'Aluno não encontrado');
            }

            // Buscar responsável
            $responsavel = null;
            
            if (!empty($aluno['id_pai'])) {
                $responsavel = $this->paiModel->find($aluno['id_pai']);
            }

            // Preparar dados
            $lancamento = [
                'id_contrato' => null, // Lançamento avulso
                'id_aluno' => $dados['id_aluno'],
                'id_pai' => $aluno['id_pai'] ?? null,
                'nome_pagador' => $responsavel['nome'] ?? $aluno['nome'],
                'tipo_lancamento' => $dados['tipo_lancamento'],
                'descricao' => $dados['descricao'],
                'data_emissao' => $dados['data_emissao'] ?? date('Y-m-d'),
                'data_vencimento' => $dados['data_vencimento'],
                'valor_parcela' => str_replace(',', '.', str_replace('.', '', $dados['valor_parcela'])),
                'valor_desconto' => !empty($dados['valor_desconto']) ? str_replace(',', '.', str_replace('.', '', $dados['valor_desconto'])) : 0,
                'gerar_boleto' => isset($dados['gerar_boleto']) ? 1 : 0,
                'observacoes' => $dados['observacoes'] ?? null,
                'status' => 1, // ABERTO
                'created_by' => session()->get('id')
            ];
            

            // Inserir
            $id = $this->parcelaModel->insert($lancamento);

            if ($id) {
                // Se marcou para gerar boleto, redireciona para geração
                if ($lancamento['gerar_boleto']) {
                    return redirect()->to(base_url('Admin/Lancamentos/gerarBoleto/' . $id))
                        ->with('success', 'Lançamento criado! Gerando boleto...');
                }

                return redirect()->to(base_url('Admin/Lancamentos/detalhes/' . $id))
                    ->with('success', 'Lançamento criado com sucesso!');
            }

            return redirect()->back()->withInput()->with('error', 'Erro ao criar lançamento');
        }

        // GET - Exibir formulário
        $data = [
            'titulo' => 'Novo Lançamento',
            'tipos_lancamento' => $this->tipoLancamentoModel->getTiposAtivos()
        ];

        return view('admin/template/header')
            . view('admin/template/sidebar')
            . view('admin/SI_Lancamentos/create', $data)
            . view('admin/template/footer');
    }

    /**
     * Editar lançamento
     */
    public function editar($id)
    {
        $lancamento = $this->parcelaModel->find($id);

        if (!$lancamento) {
            return redirect()->to(base_url('Admin/Lancamentos'))
                ->with('error', 'Lançamento não encontrado');
        }

        // Não permite editar lançamentos pagos
        if ($lancamento['status'] == 2) {
            return redirect()->back()->with('error', 'Não é possível editar lançamento já pago');
        }

        if ($this->request->getMethod() === 'post') {
            $dados = $this->request->getPost();

            // Validar
            $regras = [
                'tipo_lancamento' => 'required|integer',
                'descricao' => 'required|min_length[3]',
                'valor_parcela' => 'required|decimal',
                'data_vencimento' => 'required|valid_date'
            ];

            if (!$this->validate($regras)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Preparar dados
            $atualizar = [
                'tipo_lancamento' => $dados['tipo_lancamento'],
                'descricao' => $dados['descricao'],
                'data_vencimento' => $dados['data_vencimento'],
                'valor_parcela' => str_replace(',', '.', str_replace('.', '', $dados['valor_parcela'])),
                'valor_desconto' => !empty($dados['valor_desconto']) ? str_replace(',', '.', str_replace('.', '', $dados['valor_desconto'])) : 0,
                'observacoes' => $dados['observacoes'] ?? null
            ];

            if ($this->parcelaModel->update($id, $atualizar)) {
                return redirect()->to(base_url('Admin/Lancamentos/detalhes/' . $id))
                    ->with('success', 'Lançamento atualizado com sucesso!');
            }

            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar lançamento');
        }

        // GET - Exibir formulário
        $data = [
            'titulo' => 'Editar Lançamento',
            'lancamento' => $lancamento,
            'tipos_lancamento' => $this->tipoLancamentoModel->getTiposAtivos()
        ];

        return view('admin/template/header')
            . view('admin/template/sidebar')
            . view('admin/SI_Lancamentos/edit', $data)
            . view('admin/template/footer');
    }

    /**
     * Detalhes do lançamento
     */
    public function detalhes($id)
    {
        $lancamento = $this->parcelaModel->getLancamentoCompleto($id);

        if (!$lancamento) {
            return redirect()->to(base_url('Admin/Lancamentos'))
                ->with('error', 'Lançamento não encontrado');
        }

        // Buscar pagamentos
        $pagamentos = $this->pagamentoModel->getPagamentosByParcela($id);

        $data = [
            'titulo' => 'Detalhes do Lançamento',
            'lancamento' => $lancamento,
            'pagamentos' => $pagamentos
        ];

        return view('admin/template/header')
            . view('admin/template/sidebar')
            . view('admin/SI_Lancamentos/detalhes', $data)
            . view('admin/template/footer');
    }

    /**
     * Excluir lançamento
     */
    public function excluir($id)
    {
        $lancamento = $this->parcelaModel->find($id);

        if (!$lancamento) {
            return redirect()->to(base_url('Admin/Lancamentos'))
                ->with('error', 'Lançamento não encontrado');
        }

        // Verificar se tem pagamentos
        $pagamentos = $this->pagamentoModel->getPagamentosByParcela($id);
        if (count($pagamentos) > 0) {
            return redirect()->back()
                ->with('error', 'Não é possível excluir lançamento com pagamentos registrados');
        }

        if ($this->parcelaModel->delete($id)) {
            return redirect()->to(base_url('Admin/Lancamentos'))
                ->with('success', 'Lançamento excluído com sucesso!');
        }

        return redirect()->back()->with('error', 'Erro ao excluir lançamento');
    }

    /**
     * Registrar pagamento
     */
    public function registrarPagamento($id)
    {
        $lancamento = $this->parcelaModel->getLancamentoCompleto($id);

        if (!$lancamento) {
            return redirect()->to(base_url('Admin/Lancamentos'))
                ->with('error', 'Lançamento não encontrado');
        }

        if ($this->request->getMethod() === 'post') {
            $dados = $this->request->getPost();

            // Validar
            $regras = [
                'id_forma_pagamento' => 'required|integer',
                'data_pagamento' => 'required|valid_date',
                'valor_pago' => 'required|decimal'
            ];

            if (!$this->validate($regras)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Preparar pagamento
            $valorPago = str_replace(',', '.', str_replace('.', '', $dados['valor_pago']));
            $desconto = !empty($dados['desconto_aplicado']) ? str_replace(',', '.', str_replace('.', '', $dados['desconto_aplicado'])) : 0;
            $juros = !empty($dados['juros_aplicado']) ? str_replace(',', '.', str_replace('.', '', $dados['juros_aplicado'])) : 0;
            $multa = !empty($dados['multa_aplicada']) ? str_replace(',', '.', str_replace('.', '', $dados['multa_aplicada'])) : 0;

            $pagamento = [
                'id_parcela' => $id,
                'id_forma_pagamento' => $dados['id_forma_pagamento'],
                'data_pagamento' => $dados['data_pagamento'],
                'valor_pago' => $valorPago,
                'desconto_aplicado' => $desconto,
                'juros_aplicado' => $juros,
                'multa_aplicada' => $multa,
                'valor_liquido' => $valorPago - $desconto + $juros + $multa,
                'status' => 'CONFIRMADO',
                'observacao' => $dados['observacao'] ?? null,
                'created_by' => session()->get('id')
            ];

            if ($this->pagamentoModel->insert($pagamento)) {
                // Atualizar parcela
                $this->parcelaModel->atualizarAposPagamento($id);

                return redirect()->to(base_url('Admin/Lancamentos/detalhes/' . $id))
                    ->with('success', 'Pagamento registrado com sucesso!');
            }

            return redirect()->back()->withInput()->with('error', 'Erro ao registrar pagamento');
        }

        // GET - Exibir formulário
        $pagamentosAnteriores = $this->pagamentoModel->getPagamentosByParcela($id);

        $data = [
            'titulo' => 'Registrar Pagamento',
            'lancamento' => $lancamento,
            'pagamentos_anteriores' => $pagamentosAnteriores,
            'formas_pagamento' => $this->formaPagamentoModel->getFormasAtivas()
        ];

        return view('admin/template/header')
            . view('admin/template/sidebar')
            . view('admin/SI_Lancamentos/registrar_pagamento', $data)
            . view('admin/template/footer');
    }

    /**
     * Gerar lançamentos em lote
     */
    public function gerarLote()
    {
        if ($this->request->getMethod() === 'post') {
            $dados = $this->request->getPost();

            // Validar
            $regras = [
                'tipo_lancamento' => 'required|integer',
                'descricao' => 'required|min_length[3]',
                'valor' => 'required|decimal',
                'data_vencimento' => 'required|valid_date',
                'filtro_tipo' => 'required|in_list[turma,nivel,todos]'
            ];

            if (!$this->validate($regras)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Buscar alunos baseado no filtro
            $alunos = [];

            if ($dados['filtro_tipo'] == 'turma' && !empty($dados['id_turma'])) {
                $alunos = $this->alunoModel->getAlunosPorTurma($dados['id_turma']);
            } elseif ($dados['filtro_tipo'] == 'nivel' && !empty($dados['nivel'])) {
                $alunos = $this->alunoModel->getAlunosPorNivel($dados['nivel']);
            } elseif ($dados['filtro_tipo'] == 'todos') {
                $alunos = $this->alunoModel->getAlunosAtivos();
            }

            if (empty($alunos)) {
                return redirect()->back()->withInput()
                    ->with('error', 'Nenhum aluno encontrado com os filtros selecionados');
            }

            // Preparar dados do lançamento
            $dadosLote = [
                'tipo_lancamento' => $dados['tipo_lancamento'],
                'descricao' => $dados['descricao'],
                'data_emissao' => $dados['data_emissao'] ?? date('Y-m-d'),
                'data_vencimento' => $dados['data_vencimento'],
                'valor' => str_replace(',', '.', str_replace('.', '', $dados['valor'])),
                'desconto' => !empty($dados['desconto']) ? str_replace(',', '.', str_replace('.', '', $dados['desconto'])) : 0,
                'gerar_boleto' => isset($dados['gerar_boleto']) ? 1 : 0
            ];

            // Gerar lançamentos
            $resultado = $this->parcelaModel->gerarLote($dadosLote, $alunos);

            if ($resultado['sucesso'] > 0) {
                $mensagem = "{$resultado['sucesso']} lançamento(s) gerado(s) com sucesso!";
                if (!empty($resultado['erros'])) {
                    $mensagem .= " " . count($resultado['erros']) . " erro(s).";
                }

                return redirect()->to(base_url('Admin/Lancamentos'))
                    ->with('success', $mensagem);
            }

            return redirect()->back()->withInput()
                ->with('error', 'Erro ao gerar lançamentos em lote');
        }

        // GET - Exibir formulário
        $data = [
            'titulo' => 'Gerar Lançamentos em Lote',
            'tipos_lancamento' => $this->tipoLancamentoModel->getTiposAtivos(),
            'turmas' => $this->turmaModel->getTurmasAtivas()
        ];

        return view('admin/template/header')
            . view('admin/template/sidebar')
            . view('admin/SI_Lancamentos/gerar_lote', $data)
            . view('admin/template/footer');
    }

    /**
     * Dashboard financeiro
     */
    public function dashboard()
    {
        // Período padrão: mês atual
        $dataInicio = $this->request->getGet('data_inicio') ?? date('Y-m-01');
        $dataFim = $this->request->getGet('data_fim') ?? date('Y-m-t');

        $filtros = [
            'data_vencimento_inicio' => $dataInicio,
            'data_vencimento_fim' => $dataFim
        ];

        // Resumo geral
        $resumo = $this->parcelaModel->getResumoFinanceiro($filtros);

        // Lançamentos por tipo
        $porTipo = $this->parcelaModel->getLancamentosPorTipo($filtros);

        // Lançamentos por status
        $porStatus = $this->parcelaModel->getLancamentosPorStatus($filtros);

        // A vencer (próximos 7 dias)
        $aVencer = $this->parcelaModel->getLancamentosAVencer(7, 10);

        // Vencidos
        $vencidos = $this->parcelaModel->getLancamentosVencidos(10);

        // Maiores devedores
        $devedores = $this->parcelaModel->getMaioresDevedores(10);

        $data = [
            'titulo' => 'Dashboard Financeiro',
            'resumo' => $resumo,
            'por_tipo' => $porTipo,
            'por_status' => $porStatus,
            'a_vencer' => $aVencer,
            'vencidos' => $vencidos,
            'devedores' => $devedores,
            'filtros' => $filtros
        ];

        return view('admin/template/header')
            . view('admin/template/sidebar')
            . view('admin/SI_Lancamentos/dashboard', $data)
            . view('admin/template/footer');
    }

    /**
     * Exportar para Excel/CSV
     */
    public function exportar()
    {
        // Capturar mesmos filtros da listagem
        $filtros = [
            'status' => $this->request->getGet('status'),
            'tipo_lancamento' => $this->request->getGet('tipo_lancamento'),
            'id_aluno' => $this->request->getGet('id_aluno'),
            'data_vencimento_inicio' => $this->request->getGet('data_inicio'),
            'data_vencimento_fim' => $this->request->getGet('data_fim'),
            'search' => $this->request->getGet('search')
        ];

        $filtros = array_filter($filtros);

        // Buscar dados
        $dados = $this->parcelaModel->exportarParaArray($filtros);

        // Gerar CSV
        $filename = 'lancamentos_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // BOM para UTF-8
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Cabeçalho
        if (!empty($dados)) {
            fputcsv($output, array_keys($dados[0]), ';');

            // Dados
            foreach ($dados as $row) {
                fputcsv($output, $row, ';');
            }
        }

        fclose($output);
        exit;
    }

    /**
     * Buscar alunos (AJAX)
     */
    public function buscarAlunos()
    {
        $termo = $this->request->getVar('search');
        
        // Buscar alunos (máximo 20)
        $query = $this->alunoModel;
        
        if (!empty($termo)) {
            $query = $query->groupStart()
                        ->like('nome', $termo)
                        ->orLike('matricula', $termo)
                        ->groupEnd();
        }
        
        $alunos = $query->where('status', 1)
                        ->orderBy('nome', 'ASC')
                        ->limit(20)
                        ->findAll();
        
        // Formatar para Select2
        $results = [];
        foreach ($alunos as $aluno) {
            $id = is_object($aluno) ? $aluno->id : $aluno['id'];
            $nome = is_object($aluno) ? $aluno->nome : $aluno['nome'];
            $matricula = is_object($aluno) ? $aluno->matricula : $aluno['matricula'];
            
            $results[] = [
                'id' => $id,
                'text' => $nome . ' - Mat: ' . $matricula
            ];
        }
        
        // Retornar formato Select2
        return $this->response->setJSON([
            'results' => $results,
            'pagination' => ['more' => false]
        ]);
    }


    /**
     * Dados do aluno (AJAX)
     */
    public function dadosAluno($id)
    {
        $aluno = $this->alunoModel->find($id);

        if (!$aluno) {
            return $this->response->setJSON(['error' => 'Aluno não encontrado']);
        }

        $responsavel = null;
        if (!empty($aluno['id_pai'])) {
            $responsavel = $this->paiModel->find($aluno['id_pai']);
        }

        return $this->response->setJSON([
            'aluno' => $aluno,
            'responsavel' => $responsavel
        ]);
    }

    /**
     * Marcar lançamentos vencidos (CRON)
     */
    public function marcarVencidos()
    {
        if (!is_cli()) {
            die('Acesso negado');
        }

        $total = $this->parcelaModel->marcarVencidos();
        echo "{$total} lançamento(s) marcado(s) como vencido(s)\n";
    }
}
