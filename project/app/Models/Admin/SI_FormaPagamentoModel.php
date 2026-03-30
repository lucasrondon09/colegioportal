<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class SI_FormaPagamentoModel extends Model
{
    protected $table = 'si_formas_pagamentos';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nome',
        'descricao',
        'taxa_percentual',
        'prazo_compensacao',
        'ativo',
        'ordem_exibicao'
    ];
    
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'nome' => 'required|min_length[3]|max_length[50]'
    ];
    
    protected $validationMessages = [
        'nome' => [
            'required' => 'O nome da forma de pagamento é obrigatório',
            'min_length' => 'O nome deve ter no mínimo 3 caracteres',
            'max_length' => 'O nome deve ter no máximo 50 caracteres'
        ]
    ];
    
    /**
     * Buscar apenas formas de pagamento ativas
     * 
     * @return array
     */
    public function getFormasAtivas()
    {
        return $this->where('ativo', true)
            ->orderBy('ordem_exibicao', 'ASC')
            ->orderBy('nome', 'ASC')
            ->findAll();
    }
    
    /**
     * Buscar todas as formas de pagamento ordenadas
     * 
     * @return array
     */
    public function getAllOrdenadas()
    {
        return $this->orderBy('ordem_exibicao', 'ASC')
            ->orderBy('nome', 'ASC')
            ->findAll();
    }
    
    /**
     * Ativar uma forma de pagamento
     * 
     * @param int $id
     * @return bool
     */
    public function ativar($id)
    {
        return $this->update($id, ['ativo' => true]);
    }
    
    /**
     * Desativar uma forma de pagamento
     * 
     * @param int $id
     * @return bool
     */
    public function desativar($id)
    {
        return $this->update($id, ['ativo' => false]);
    }
    
    /**
     * Verificar se uma forma de pagamento está em uso
     * 
     * @param int $id
     * @return bool
     */
    public function isEmUso($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('si_pagamentos');
        
        $count = $builder->where('id_forma_pagamento', $id)->countAllResults();
        
        return $count > 0;
    }
    
    /**
     * Buscar forma de pagamento por nome
     * 
     * @param string $nome
     * @return object|null
     */
    public function getByNome($nome)
    {
        return $this->where('nome', $nome)->first();
    }
}

