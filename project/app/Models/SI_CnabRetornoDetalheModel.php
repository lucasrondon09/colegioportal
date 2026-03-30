<?php

namespace App\Models;

use CodeIgniter\Model;

class SI_CnabRetornoDetalheModel extends Model
{
    protected $table            = 'si_cnab_retorno_detalhe';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_retorno',
        'id_parcela',
        'nosso_numero',
        'seu_numero',
        'codigo_ocorrencia',
        'descricao_ocorrencia',
        'data_ocorrencia',
        'data_credito',
        'valor_titulo',
        'valor_pago',
        'valor_tarifa',
        'valor_juros',
        'valor_multa',
        'valor_desconto',
        'processado',
        'erro_processamento'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [
        'id_retorno'        => 'required|integer',
        'nosso_numero'      => 'required|max_length[20]',
        'codigo_ocorrencia' => 'required|max_length[10]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Busca detalhes de um retorno com informações da parcela
     */
    public function getDetalhesPorRetorno(int $idRetorno)
    {
        return $this->select('
                si_cnab_retorno_detalhe.*,
                si_parcelas_contrato.numero_parcela,
                si_parcelas_contrato.id_contrato,
                si_parcelas_contrato.status as status_parcela,
                si_parcelas_contrato.valor_parcela
            ')
            ->join('si_parcelas_contrato', 'si_parcelas_contrato.id = si_cnab_retorno_detalhe.id_parcela', 'left')
            ->where('si_cnab_retorno_detalhe.id_retorno', $idRetorno)
            ->orderBy('si_cnab_retorno_detalhe.id', 'ASC')
            ->findAll();
    }

    /**
     * Busca detalhes não processados de um retorno
     */
    public function getDetalhesNaoProcessados(int $idRetorno)
    {
        return $this->where('id_retorno', $idRetorno)
            ->where('processado', 0)
            ->findAll();
    }

    /**
     * Marca detalhe como processado
     */
    public function marcarComoProcessado(int $id): bool
    {
        return $this->update($id, ['processado' => 1]);
    }

    /**
     * Registra erro de processamento
     */
    public function registrarErro(int $id, string $erro): bool
    {
        return $this->update($id, [
            'processado'         => 0,
            'erro_processamento' => $erro
        ]);
    }

    /**
     * Busca detalhe por nosso número
     */
    public function getByNossoNumero(string $nossoNumero, int $idRetorno)
    {
        return $this->where('nosso_numero', $nossoNumero)
            ->where('id_retorno', $idRetorno)
            ->first();
    }

    /**
     * Conta ocorrências por código
     */
    public function contarPorOcorrencia(int $idRetorno)
    {
        return $this->select('codigo_ocorrencia, descricao_ocorrencia, COUNT(*) as total')
            ->where('id_retorno', $idRetorno)
            ->groupBy('codigo_ocorrencia, descricao_ocorrencia')
            ->findAll();
    }

    /**
     * Insere múltiplos detalhes de uma vez
     */
    public function inserirLote(array $detalhes): bool
    {
        return $this->insertBatch($detalhes);
    }

    /**
     * Busca liquidações (código 06)
     */
    public function getLiquidacoes(int $idRetorno)
    {
        return $this->where('id_retorno', $idRetorno)
            ->where('codigo_ocorrencia', '06')
            ->findAll();
    }

    /**
     * Busca baixas (código 09)
     */
    public function getBaixas(int $idRetorno)
    {
        return $this->where('id_retorno', $idRetorno)
            ->where('codigo_ocorrencia', '09')
            ->findAll();
    }

    /**
     * Busca rejeições (códigos 02, 03, 26, 30)
     */
    public function getRejeicoes(int $idRetorno)
    {
        return $this->where('id_retorno', $idRetorno)
            ->whereIn('codigo_ocorrencia', ['02', '03', '26', '30'])
            ->findAll();
    }
}
