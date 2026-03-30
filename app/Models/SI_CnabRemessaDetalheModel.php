<?php

namespace App\Models;

use CodeIgniter\Model;

class SI_CnabRemessaDetalheModel extends Model
{
    protected $table            = 'si_cnab_remessa_detalhe';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_remessa',
        'id_parcela',
        'nosso_numero',
        'valor',
        'vencimento',
        'sequencial_registro',
        'status_envio',
        'codigo_rejeicao',
        'mensagem_rejeicao'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [
        'id_remessa'          => 'required|integer',
        'id_parcela'          => 'required|integer',
        'nosso_numero'        => 'required|max_length[20]',
        'valor'               => 'required|decimal',
        'vencimento'          => 'required|valid_date',
        'sequencial_registro' => 'required|integer',
        'status_envio'        => 'required|in_list[pendente,enviado,registrado,rejeitado]'
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
     * Busca detalhes de uma remessa com informações da parcela
     */
    public function getDetalhesPorRemessa(int $idRemessa)
    {
        return $this->select('
                si_cnab_remessa_detalhe.*,
                si_parcelas_contrato.numero_parcela,
                si_parcelas_contrato.id_contrato,
                si_parcelas_contrato.status as status_parcela
            ')
            ->join('si_parcelas_contrato', 'si_parcelas_contrato.id = si_cnab_remessa_detalhe.id_parcela', 'left')
            ->where('si_cnab_remessa_detalhe.id_remessa', $idRemessa)
            ->orderBy('si_cnab_remessa_detalhe.sequencial_registro', 'ASC')
            ->findAll();
    }

    /**
     * Atualiza status de envio
     */
    public function atualizarStatusEnvio(int $id, string $status, ?string $codigoRejeicao = null, ?string $mensagemRejeicao = null): bool
    {
        $data = ['status_envio' => $status];
        
        if ($codigoRejeicao !== null) {
            $data['codigo_rejeicao'] = $codigoRejeicao;
        }
        
        if ($mensagemRejeicao !== null) {
            $data['mensagem_rejeicao'] = $mensagemRejeicao;
        }
        
        return $this->update($id, $data);
    }

    /**
     * Busca detalhe por nosso número
     */
    public function getByNossoNumero(string $nossoNumero)
    {
        return $this->where('nosso_numero', $nossoNumero)->first();
    }

    /**
     * Conta boletos por status em uma remessa
     */
    public function contarPorStatus(int $idRemessa)
    {
        $detalhes = $this->where('id_remessa', $idRemessa)->findAll();
        
        $contador = [
            'pendente'   => 0,
            'enviado'    => 0,
            'registrado' => 0,
            'rejeitado'  => 0
        ];
        
        foreach ($detalhes as $detalhe) {
            $status = $detalhe['status_envio'];
            if (isset($contador[$status])) {
                $contador[$status]++;
            }
        }
        
        return $contador;
    }

    /**
     * Insere múltiplos detalhes de uma vez
     */
    public function inserirLote(array $detalhes): bool
    {
        return $this->insertBatch($detalhes);
    }
}
