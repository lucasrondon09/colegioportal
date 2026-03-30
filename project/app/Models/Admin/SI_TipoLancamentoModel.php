<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class SI_TipoLancamentoModel extends Model
{
    protected $table = 'si_tipo_lancamento';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'nome',
        'codigo',
        'descricao',
        'permite_parcelamento',
        'permite_desconto',
        'gera_boleto_automatico',
        'conta_contabil',
        'centro_custo',
        'ordem_exibicao',
        'ativo'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    protected $validationRules = [
        'nome' => 'required|min_length[3]|max_length[100]',
        'codigo' => 'required|min_length[2]|max_length[20]|is_unique[si_tipo_lancamento.codigo,id,{id}]',
    ];
    
    protected $validationMessages = [
        'nome' => [
            'required' => 'O nome do tipo de lançamento é obrigatório',
            'min_length' => 'O nome deve ter no mínimo 3 caracteres',
            'max_length' => 'O nome deve ter no máximo 100 caracteres'
        ],
        'codigo' => [
            'required' => 'O código é obrigatório',
            'is_unique' => 'Este código já está em uso'
        ]
    ];
    
    /**
     * Busca tipos ativos
     */
    public function getTiposAtivos()
    {
        return $this->where('ativo', 1)
                    ->orderBy('ordem_exibicao', 'ASC')
                    ->findAll();
    }
    
    /**
     * Busca tipos para select/dropdown
     */
    public function getTiposParaSelect()
    {
        $tipos = $this->getTiposAtivos();
        $options = [];
        
        foreach ($tipos as $tipo) {
            $options[$tipo['id']] = $tipo['nome'];
        }
        
        return $options;
    }
    
    /**
     * Busca tipo por código
     */
    public function getTipoPorCodigo($codigo)
    {
        return $this->where('codigo', $codigo)->first();
    }
    
    /**
     * Ativa um tipo
     */
    public function ativar($id)
    {
        return $this->update($id, ['ativo' => 1]);
    }
    
    /**
     * Desativa um tipo
     */
    public function desativar($id)
    {
        return $this->update($id, ['ativo' => 0]);
    }
    
    /**
     * Verifica se o tipo está em uso
     */
    public function isEmUso($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('si_parcelas_contrato');
        
        $count = $builder->where('tipo_lancamento', $id)
                        ->where('deleted_at', null)
                        ->countAllResults();
        
        return $count > 0;
    }
    
    /**
     * Busca com paginação e filtros
     */
    public function buscarComPaginacao($search = '', $perPage = 10)
    {
        $builder = $this->builder();
        
        if (!empty($search)) {
            $builder->groupStart()
                   ->like('nome', $search)
                   ->orLike('codigo', $search)
                   ->orLike('descricao', $search)
                   ->groupEnd();
        }
        
        return $builder->orderBy('ordem_exibicao', 'ASC')
                      ->paginate($perPage);
    }
    
    /**
     * Reordena tipos
     */
    public function reordenar($id, $novaOrdem)
    {
        return $this->update($id, ['ordem_exibicao' => $novaOrdem]);
    }
    
    /**
     * Estatísticas de uso do tipo
     */
    public function getEstatisticas($id)
    {
        $db = \Config\Database::connect();
        
        $stats = [
            'total_lancamentos' => 0,
            'valor_total' => 0,
            'valor_pago' => 0,
            'valor_saldo' => 0
        ];
        
        $query = $db->query("
            SELECT 
                COUNT(*) as total_lancamentos,
                SUM(valor_total) as valor_total,
                SUM(valor_pago) as valor_pago,
                SUM(valor_saldo) as valor_saldo
            FROM si_parcelas_contrato
            WHERE tipo_lancamento = ?
            AND deleted_at IS NULL
        ", [$id]);
        
        $result = $query->getRowArray();
        
        if ($result) {
            $stats = [
                'total_lancamentos' => (int) $result['total_lancamentos'],
                'valor_total' => (float) $result['valor_total'],
                'valor_pago' => (float) $result['valor_pago'],
                'valor_saldo' => (float) $result['valor_saldo']
            ];
        }
        
        return $stats;
    }
    
    /**
     * Duplica um tipo
     */
    public function duplicar($id)
    {
        $tipo = $this->find($id);
        
        if (!$tipo) {
            return false;
        }
        
        unset($tipo['id']);
        $tipo['nome'] = $tipo['nome'] . ' (Cópia)';
        $tipo['codigo'] = $tipo['codigo'] . '_COPIA';
        $tipo['ativo'] = 0;
        
        return $this->insert($tipo);
    }
}

