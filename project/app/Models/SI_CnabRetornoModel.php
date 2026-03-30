<?php

namespace App\Models;

use CodeIgniter\Model;

class SI_CnabRetornoModel extends Model
{
    protected $table            = 'si_cnab_retorno';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'numero_retorno',
        'data_processamento',
        'data_arquivo',
        'arquivo_nome',
        'arquivo_path',
        'total_registros',
        'total_liquidados',
        'total_baixados',
        'total_rejeitados',
        'valor_total_liquidado',
        'status',
        'usuario_id',
        'observacoes',
        'hash_arquivo'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'data_processamento' => 'required|valid_date',
        'arquivo_nome'       => 'required|max_length[255]',
        'status'             => 'required|in_list[processando,processado,erro,reprocessado]',
        'hash_arquivo'       => 'permit_empty|is_unique[si_cnab_retorno.hash_arquivo,id,{id}]'
    ];
    protected $validationMessages   = [
        'hash_arquivo' => [
            'is_unique' => 'Este arquivo já foi processado anteriormente'
        ]
    ];
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
     * Verifica se arquivo já foi processado
     */
    public function arquivoJaProcessado(string $hash): bool
    {
        return $this->where('hash_arquivo', $hash)->countAllResults() > 0;
    }

    /**
     * Busca retornos com estatísticas
     */
    public function getRetornosComEstatisticas($limit = 20, $offset = 0)
    {
        return $this->orderBy('id', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }

    /**
     * Atualiza status do retorno
     */
    public function atualizarStatus(int $id, string $status, ?string $observacoes = null): bool
    {
        $data = ['status' => $status];
        if ($observacoes !== null) {
            $data['observacoes'] = $observacoes;
        }
        return $this->update($id, $data);
    }

    /**
     * Atualiza totalizadores do retorno
     */
    public function atualizarTotalizadores(int $id, array $totalizadores): bool
    {
        return $this->update($id, $totalizadores);
    }

    /**
     * Busca estatísticas gerais de retornos
     */
    public function getEstatisticas()
    {
        $total = $this->countAll();
        $processados = $this->where('status', 'processado')->countAllResults();
        $erros = $this->where('status', 'erro')->countAllResults();
        
        $valorTotal = $this->selectSum('valor_total_liquidado')->first();
        $totalLiquidados = $this->selectSum('total_liquidados')->first();

        return [
            'total'            => $total,
            'processados'      => $processados,
            'erros'            => $erros,
            'valor_liquidado'  => $valorTotal['valor_total_liquidado'] ?? 0,
            'total_liquidados' => $totalLiquidados['total_liquidados'] ?? 0
        ];
    }
}
