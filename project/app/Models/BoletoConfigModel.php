<?php

namespace App\Models;

use CodeIgniter\Model;

class BoletoConfigModel extends Model
{
    protected $table            = 'si_boleto_config';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'juros_percentual',
        'multa_percentual',
        'multa_apos_dias',
        'desconto_percentual',
        'nao_receber_apos_dias',
        'aceite',
        'protestar_apos_dias',
        'tipo_titulo',
        'mensagem_banco',
        'instrucao_1',
        'instrucao_2',
        'instrucao_3',
        'ativo',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'juros_percentual'       => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        'multa_percentual'       => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        'multa_apos_dias'        => 'required|integer|greater_than_equal_to[0]',
        'desconto_percentual'    => 'permit_empty|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        'nao_receber_apos_dias'  => 'required|integer|greater_than_equal_to[0]',
        'aceite'                 => 'required|in_list[S,N]',
        'protestar_apos_dias'    => 'required|integer|greater_than_equal_to[0]',
        'tipo_titulo'            => 'required|max_length[2]',
        'mensagem_banco'         => 'permit_empty|max_length[500]',
        'instrucao_1'            => 'permit_empty|max_length[255]',
        'instrucao_2'            => 'permit_empty|max_length[255]',
        'instrucao_3'            => 'permit_empty|max_length[255]',
        'ativo'                  => 'required|in_list[0,1]',
    ];
    
    protected $validationMessages   = [
        'juros_percentual' => [
            'required' => 'O campo juros é obrigatório',
            'decimal' => 'O campo juros deve ser um número decimal',
            'greater_than_equal_to' => 'O campo juros deve ser maior ou igual a 0',
            'less_than_equal_to' => 'O campo juros deve ser menor ou igual a 100',
        ],
        'multa_percentual' => [
            'required' => 'O campo multa é obrigatório',
            'decimal' => 'O campo multa deve ser um número decimal',
            'greater_than_equal_to' => 'O campo multa deve ser maior ou igual a 0',
            'less_than_equal_to' => 'O campo multa deve ser menor ou igual a 100',
        ],
        'aceite' => [
            'required' => 'O campo aceite é obrigatório',
            'in_list' => 'O campo aceite deve ser S ou N',
        ],
    ];
    
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Retorna a configuração ativa
     * 
     * @return array|null
     */
    public function getConfigAtiva()
    {
        return $this->where('ativo', 1)->first();
    }

    /**
     * Atualiza a configuração ativa
     * 
     * @param array $data Dados para atualizar
     * @return bool
     */
    public function atualizarConfigAtiva(array $data)
    {
        $config = $this->getConfigAtiva();
        
        if ($config) {
            return $this->update($config['id'], $data);
        }
        
        // Se não existe configuração ativa, criar uma nova
        $data['ativo'] = 1;
        return $this->insert($data);
    }
}
