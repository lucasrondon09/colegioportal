<?php

namespace App\Models;

use CodeIgniter\Model;

class SI_CnabRemessaModel extends Model
{
    protected $table            = 'si_cnab_remessa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'numero_remessa',
        'data_geracao',
        'data_envio',
        'arquivo_nome',
        'arquivo_path',
        'total_registros',
        'valor_total',
        'status',
        'usuario_id',
        'observacoes'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'numero_remessa'  => 'required|integer|is_unique[si_cnab_remessa.numero_remessa,id,{id}]',
        'data_geracao'    => 'required|valid_date',
        'arquivo_nome'    => 'required|max_length[255]',
        'total_registros' => 'required|integer',
        'valor_total'     => 'required|decimal',
        'status'          => 'required|in_list[gerado,enviado,processado,erro]'
    ];
    protected $validationMessages   = [
        'numero_remessa' => [
            'required'   => 'O número da remessa é obrigatório',
            'integer'    => 'O número da remessa deve ser um número inteiro',
            'is_unique'  => 'Este número de remessa já existe'
        ],
        'data_geracao' => [
            'required'   => 'A data de geração é obrigatória',
            'valid_date' => 'Data de geração inválida'
        ],
        'arquivo_nome' => [
            'required'   => 'O nome do arquivo é obrigatório',
            'max_length' => 'O nome do arquivo não pode ter mais de 255 caracteres'
        ],
        'status' => [
            'required' => 'O status é obrigatório',
            'in_list'  => 'Status inválido. Use: gerado, enviado, processado ou erro'
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
     * Obtém o próximo número de remessa
     */
    public function getProximoNumeroRemessa(): int
    {
        $ultima = $this->selectMax('numero_remessa')->first();
        return ($ultima['numero_remessa'] ?? 0) + 1;
    }

    /**
     * Busca remessas com detalhes
     */
    public function getRemessasComDetalhes($limit = 20, $offset = 0)
    {
        return $this->select('si_cnab_remessa.*, COUNT(si_cnab_remessa_detalhe.id) as qtd_boletos')
            ->join('si_cnab_remessa_detalhe', 'si_cnab_remessa_detalhe.id_remessa = si_cnab_remessa.id', 'left')
            ->groupBy('si_cnab_remessa.id')
            ->orderBy('si_cnab_remessa.id', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }

    /**
     * Busca remessa por número
     */
    public function getByNumero(int $numero)
    {
        return $this->where('numero_remessa', $numero)->first();
    }

    /**
     * Atualiza status da remessa
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
     * Marca remessa como enviada
     */
    public function marcarComoEnviada(int $id): bool
    {
        return $this->update($id, [
            'status'     => 'enviado',
            'data_envio' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Busca estatísticas de remessas
     */
    public function getEstatisticas()
    {
        $total = $this->countAll();
        $geradas = $this->where('status', 'gerado')->countAllResults();
        $enviadas = $this->where('status', 'enviado')->countAllResults();
        $processadas = $this->where('status', 'processado')->countAllResults();
        $erros = $this->where('status', 'erro')->countAllResults();

        return [
            'total'       => $total,
            'geradas'     => $geradas,
            'enviadas'    => $enviadas,
            'processadas' => $processadas,
            'erros'       => $erros
        ];
    }
}
