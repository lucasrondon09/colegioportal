<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Admin\SI_Parcelas_ContratoModel;
use App\Models\Admin\SI_TipoLancamentoModel;
use App\Models\Admin\SI_AlunoModel;
use App\Models\Admin\SI_TurmaModel;
use App\Models\Admin\SI_PaiModel;

class SI_VisaoGeralFinanceiro extends BaseController
{
    protected $parcelaModel;
    protected $tipoLancamentoModel;
    protected $alunoModel;
    protected $turmaModel;
    protected $paiModel;
    protected $session;

    public function __construct()
    {
        helper('auth');
        helper('form');
        permission();

        $this->parcelaModel = new SI_Parcelas_ContratoModel();
        $this->tipoLancamentoModel = new SI_TipoLancamentoModel();
        $this->alunoModel = new SI_AlunoModel();
        $this->turmaModel = new SI_TurmaModel();
        $this->paiModel = new SI_PaiModel();
        $this->session = session();
    }

    /**
     * Página principal - Dashboard + Listagem
     */
    public function index()
    {
        // Capturar filtros da URL
        $filtros = [
            'status' => $this->request->getGet('status'),
            'tipo_lancamento' => $this->request->getGet('tipo_lancamento'),
            'id_aluno' => $this->request->getGet('id_aluno'),
            'id_turma' => $this->request->getGet('id_turma'),
            'id_responsavel' => $this->request->getGet('id_responsavel'),
            'data_vencimento_inicio' => $this->request->getGet('data_inicio'),
            'data_vencimento_fim' => $this->request->getGet('data_fim'),
            'search' => $this->request->getGet('search'),
        ];

        // Remover filtros vazios
        $filtros = array_filter($filtros, function ($value) {
            return $value !== null && $value !== '';
        });

        // Obter resumo financeiro (Dashboard)
        $resumo = $this->parcelaModel->getResumoFinanceiroGeral($filtros);

        // Obter estatísticas por tipo de lançamento
        $estatisticas_tipo = $this->parcelaModel->getEstatisticasPorTipo($filtros);

        // Buscar lançamentos com paginação
        $perPage = 25;
        //$builder = $this->parcelaModel->getAllLancamentosCompletos($filtros);
        // Buscar lançamentos com paginação
        // O método getAllLancamentosCompletos retorna o próprio model com filtros aplicados
        $perPage = 25;
        $builder = $this->parcelaModel->getAllLancamentosCompletos($filtros);
        $lancamentos = $builder->paginate($perPage);
        $pager = $this->parcelaModel->pager;

        // Buscar dados para os filtros
        $tipos_lancamento = $this->tipoLancamentoModel->getTiposAtivos();
        $turmas = $this->turmaModel->orderBy('ano', 'DESC')->orderBy('nome', 'ASC')->findAll();
        
        // Preparar dados para a view
        $data = [
            'titulo' => 'Visão Geral Financeira',
            'resumo' => $resumo,
            'estatisticas_tipo' => $estatisticas_tipo,
            'lancamentos' => $lancamentos,
            'pager' => $pager,
            'tipos_lancamento' => $tipos_lancamento,
            'turmas' => $turmas,
            'filtros' => $filtros,
            'status_opcoes' => [
                1 => 'Aberto',
                2 => 'Pago',
                3 => 'Pago Parcialmente',
                4 => 'Atrasado'
            ]
        ];

        // Renderizar views
        echo view('admin/template/header');
        echo view('admin/template/sidebar');
        echo view('admin/SI_VisaoGeralFinanceiro/index', $data);
        echo view('admin/template/footer');
    }

    /**
     * Dashboard isolado (pode ser usado em iframe ou AJAX)
     */
    public function dashboard()
    {
        // Capturar filtros
        $filtros = [
            'status' => $this->request->getGet('status'),
            'tipo_lancamento' => $this->request->getGet('tipo_lancamento'),
            'id_aluno' => $this->request->getGet('id_aluno'),
            'id_turma' => $this->request->getGet('id_turma'),
            'data_vencimento_inicio' => $this->request->getGet('data_inicio'),
            'data_vencimento_fim' => $this->request->getGet('data_fim'),
        ];

        $filtros = array_filter($filtros, function ($value) {
            return $value !== null && $value !== '';
        });

        // Obter resumo financeiro
        $resumo = $this->parcelaModel->getResumoFinanceiroGeral($filtros);
        $estatisticas_tipo = $this->parcelaModel->getEstatisticasPorTipo($filtros);

        $data = [
            'resumo' => $resumo,
            'estatisticas_tipo' => $estatisticas_tipo,
        ];

        echo view('admin/SI_VisaoGeralFinanceiro/dashboard', $data);
    }

    /**
     * Exportar relatório em CSV
     */
    public function exportarCSV()
    {
        // Capturar filtros
        $filtros = [
            'status' => $this->request->getGet('status'),
            'tipo_lancamento' => $this->request->getGet('tipo_lancamento'),
            'id_aluno' => $this->request->getGet('id_aluno'),
            'id_turma' => $this->request->getGet('id_turma'),
            'data_vencimento_inicio' => $this->request->getGet('data_inicio'),
            'data_vencimento_fim' => $this->request->getGet('data_fim'),
            'search' => $this->request->getGet('search'),
        ];

        $filtros = array_filter($filtros, function ($value) {
            return $value !== null && $value !== '';
        });

        // Buscar todos os lançamentos (sem paginação)
        $lancamentos = $this->parcelaModel->getAllLancamentosCompletos($filtros)->findAll();

        // Preparar CSV
        $filename = 'lancamentos_financeiros_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Cabeçalhos
        fputcsv($output, [
            'ID',
            'Contrato',
            'Aluno',
            'Matrícula',
            'Turma',
            'Responsável',
            'CPF Responsável',
            'Tipo Lançamento',
            'Nº Parcela',
            'Descrição',
            'Data Vencimento',
            'Valor Parcela',
            'Valor Pago',
            'Saldo',
            'Status'
        ], ';');
        
        // Status map
        $status_map = [
            1 => 'Aberto',
            2 => 'Pago',
            3 => 'Pago Parcialmente',
            4 => 'Atrasado'
        ];
        
        // Dados
        foreach ($lancamentos as $lanc) {
            $total_pago = $lanc->total_pago ?? 0;
            $saldo = $lanc->valor_parcela - $total_pago;
            
            fputcsv($output, [
                $lanc->id,
                $lanc->numero_contrato ?? '',
                $lanc->aluno_nome ?? '',
                $lanc->aluno_matricula ?? '',
                $lanc->turma_nome ?? '',
                $lanc->responsavel_nome ?? '',
                $lanc->responsavel_cpf ?? '',
                $lanc->tipo_lancamento_nome ?? '',
                $lanc->numero_parcela ?? '',
                $lanc->descricao ?? '',
                date('d/m/Y', strtotime($lanc->data_vencimento)),
                number_format($lanc->valor_parcela, 2, ',', '.'),
                number_format($total_pago, 2, ',', '.'),
                number_format($saldo, 2, ',', '.'),
                $status_map[$lanc->status] ?? ''
            ], ';');
        }
        
        fclose($output);
        exit;
    }

    /**
     * API JSON para dashboard (para uso com AJAX)
     */
    public function apiResumo()
    {
        $filtros = [
            'status' => $this->request->getGet('status'),
            'tipo_lancamento' => $this->request->getGet('tipo_lancamento'),
            'id_aluno' => $this->request->getGet('id_aluno'),
            'id_turma' => $this->request->getGet('id_turma'),
            'data_vencimento_inicio' => $this->request->getGet('data_inicio'),
            'data_vencimento_fim' => $this->request->getGet('data_fim'),
        ];

        $filtros = array_filter($filtros, function ($value) {
            return $value !== null && $value !== '';
        });

        $resumo = $this->parcelaModel->getResumoFinanceiroGeral($filtros);

        return $this->response->setJSON([
            'success' => true,
            'data' => $resumo
        ]);
    }
}
