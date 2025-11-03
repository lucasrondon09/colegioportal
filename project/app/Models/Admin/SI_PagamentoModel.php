<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class SI_PagamentoModel extends Model
{
    protected $table = 'si_pagamentos';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_parcela',
        'id_forma_pagamento',
        'data_pagamento',
        'valor_pago',
        'desconto_aplicado',
        'juros_aplicado',
        'multa_aplicada',
        'valor_liquido',
        'status',
        'comprovante',
        'observacao',
        'data_estorno',
        'motivo_estorno',
        'created_by'
    ];
    
    protected $returnType = 'object';
    protected $useSoftDeletes = true;
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    protected $validationRules = [
        'id_parcela' => 'required|integer',
        'id_forma_pagamento' => 'required|integer',
        'data_pagamento' => 'required|valid_date',
        'valor_pago' => 'required|decimal'
    ];
    
    protected $validationMessages = [
        'id_parcela' => [
            'required' => 'A parcela é obrigatória',
            'integer' => 'ID da parcela inválido'
        ],
        'id_forma_pagamento' => [
            'required' => 'A forma de pagamento é obrigatória',
            'integer' => 'Forma de pagamento inválida'
        ],
        'data_pagamento' => [
            'required' => 'A data de pagamento é obrigatória',
            'valid_date' => 'Data de pagamento inválida'
        ],
        'valor_pago' => [
            'required' => 'O valor pago é obrigatório',
            'decimal' => 'Valor pago inválido'
        ]
    ];
    
    /**
     * Buscar todos os pagamentos de uma parcela específica
     * 
     * @param int $id_parcela
     * @return array
     */
    public function getPagamentosByParcela($id_parcela)
    {
        return $this->select('si_pagamentos.*, si_formas_pagamentos.nome as forma_pagamento_nome')
            ->join('si_formas_pagamentos', 'si_formas_pagamentos.id = si_pagamentos.id_forma_pagamento', 'left')
            ->where('si_pagamentos.id_parcela', $id_parcela)
            ->where('si_pagamentos.status !=', 'ESTORNADO')
            ->orderBy('si_pagamentos.data_pagamento', 'DESC')
            ->orderBy('si_pagamentos.created_at', 'DESC')
            ->findAll();
    }
    
    /**
     * Calcular o total pago de uma parcela (apenas pagamentos confirmados)
     * 
     * @param int $id_parcela
     * @return float
     */
    public function getTotalPagoParcela($id_parcela)
    {
        $result = $this->selectSum('valor_pago')
            ->where('id_parcela', $id_parcela)
            ->where('status', 'CONFIRMADO')
            ->first();
        
        return $result ? (float)$result->valor_pago : 0;
    }
    
    /**
     * Calcular o total líquido pago (considerando descontos, juros e multas)
     * 
     * @param int $id_parcela
     * @return float
     */
    public function getTotalLiquidoParcela($id_parcela)
    {
        $result = $this->selectSum('valor_liquido')
            ->where('id_parcela', $id_parcela)
            ->where('status', 'CONFIRMADO')
            ->first();
        
        return $result ? (float)$result->valor_liquido : 0;
    }
    
    /**
     * Verificar se uma parcela está totalmente paga
     * 
     * @param int $id_parcela
     * @param float $valor_total_parcela
     * @return bool
     */
    public function isParcelaQuitada($id_parcela, $valor_total_parcela)
    {
        $totalPago = $this->getTotalPagoParcela($id_parcela);
        return $totalPago >= $valor_total_parcela;
    }
    
    /**
     * Calcular valor restante a pagar de uma parcela
     * 
     * @param int $id_parcela
     * @param float $valor_total_parcela
     * @return float
     */
    public function getValorRestante($id_parcela, $valor_total_parcela)
    {
        $totalPago = $this->getTotalPagoParcela($id_parcela);
        return max(0, $valor_total_parcela - $totalPago);
    }
    
    /**
     * Estornar um pagamento
     * 
     * @param int $id_pagamento
     * @param string $motivo
     * @param int $user_id
     * @return bool
     */
    public function estornarPagamento($id_pagamento, $motivo, $user_id = null)
    {
        $data = [
            'status' => 'ESTORNADO',
            'data_estorno' => date('Y-m-d'),
            'motivo_estorno' => $motivo
        ];
        
        if ($user_id) {
            $data['updated_by'] = $user_id;
        }
        
        return $this->update($id_pagamento, $data);
    }
    
    /**
     * Buscar pagamentos de um contrato
     * 
     * @param int $id_contrato
     * @return array
     */
    public function getPagamentosByContrato($id_contrato)
    {
        return $this->select('
                si_pagamentos.*,
                si_formas_pagamentos.nome as forma_pagamento_nome,
                si_parcelas_contrato.numero_parcela,
                si_parcelas_contrato.tipo_lancamento
            ')
            ->join('si_parcelas_contrato', 'si_parcelas_contrato.id = si_pagamentos.id_parcela', 'left')
            ->join('si_formas_pagamentos', 'si_formas_pagamentos.id = si_pagamentos.id_forma_pagamento', 'left')
            ->where('si_parcelas_contrato.id_contrato', $id_contrato)
            ->where('si_pagamentos.status !=', 'ESTORNADO')
            ->orderBy('si_pagamentos.data_pagamento', 'DESC')
            ->findAll();
    }
    
    /**
     * Relatório de recebimentos por forma de pagamento em um período
     * 
     * @param string $data_inicio
     * @param string $data_fim
     * @return array
     */
    public function getRecebimentosPorForma($data_inicio, $data_fim)
    {
        return $this->select('
                si_formas_pagamentos.nome as forma,
                COUNT(si_pagamentos.id) as quantidade,
                SUM(si_pagamentos.valor_pago) as total_bruto,
                SUM(si_pagamentos.desconto_aplicado) as total_desconto,
                SUM(si_pagamentos.juros_aplicado) as total_juros,
                SUM(si_pagamentos.multa_aplicada) as total_multa,
                SUM(si_pagamentos.valor_liquido) as total_liquido
            ')
            ->join('si_formas_pagamentos', 'si_formas_pagamentos.id = si_pagamentos.id_forma_pagamento', 'left')
            ->where('si_pagamentos.status', 'CONFIRMADO')
            ->where('si_pagamentos.data_pagamento >=', $data_inicio)
            ->where('si_pagamentos.data_pagamento <=', $data_fim)
            ->groupBy('si_pagamentos.id_forma_pagamento')
            ->orderBy('total_liquido', 'DESC')
            ->findAll();
    }
    
    /**
     * Total de recebimentos em um período
     * 
     * @param string $data_inicio
     * @param string $data_fim
     * @return float
     */
    public function getTotalRecebimentosPeriodo($data_inicio, $data_fim)
    {
        $result = $this->selectSum('valor_liquido')
            ->where('status', 'CONFIRMADO')
            ->where('data_pagamento >=', $data_inicio)
            ->where('data_pagamento <=', $data_fim)
            ->first();
        
        return $result ? (float)$result->valor_liquido : 0;
    }
    
    /**
     * Buscar pagamentos pendentes de confirmação
     * 
     * @return array
     */
    public function getPagamentosPendentes()
    {
        return $this->select('
                si_pagamentos.*,
                si_formas_pagamentos.nome as forma_pagamento_nome,
                si_parcelas_contrato.numero_parcela,
                si_contrato.id as id_contrato,
                si_aluno.nome as aluno_nome
            ')
            ->join('si_parcelas_contrato', 'si_parcelas_contrato.id = si_pagamentos.id_parcela', 'left')
            ->join('si_contrato', 'si_contrato.id = si_parcelas_contrato.id_contrato', 'left')
            ->join('si_aluno', 'si_aluno.id = si_contrato.id_aluno', 'left')
            ->join('si_formas_pagamentos', 'si_formas_pagamentos.id = si_pagamentos.id_forma_pagamento', 'left')
            ->where('si_pagamentos.status', 'PENDENTE')
            ->orderBy('si_pagamentos.data_pagamento', 'ASC')
            ->findAll();
    }
    
    /**
     * Confirmar um pagamento pendente
     * 
     * @param int $id_pagamento
     * @return bool
     */
    public function confirmarPagamento($id_pagamento)
    {
        return $this->update($id_pagamento, ['status' => 'CONFIRMADO']);
    }
}

