<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class SI_ConfiguracoesBoletoModel extends Model
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
        'nao_receber_apos_dias',
        'protestar_apos_dias',
        'aceite',
        'tipo_titulo',
        'mensagem_banco',
        'desconto_percentual',
        'ativo',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'juros_percentual'      => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        'multa_percentual'      => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        'multa_apos_dias'       => 'required|integer|greater_than_equal_to[0]',
        'nao_receber_apos_dias' => 'required|integer|greater_than_equal_to[0]',
        'protestar_apos_dias'   => 'required|integer|greater_than_equal_to[0]',
        'aceite'                => 'required|in_list[S,N]',
        'tipo_titulo'           => 'required|max_length[2]',
        'desconto_percentual'   => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Obter configuração ativa
     */
    public function getConfigAtiva()
    {
        return $this->where('ativo', 1)->first();
    }
}
