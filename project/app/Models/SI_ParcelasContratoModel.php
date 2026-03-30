<?php

namespace App\Models;

use CodeIgniter\Model;

class SI_ParcelasContratoModel extends Model
{
    protected $table            = 'si_parcelas_contrato';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_contrato',
        'numero_parcela',
        'data_vencimento',
        'valor_parcela',
        'status',
        'data_pagamento',
        'valor_pago',
        'forma_pagamento',
        'observacoes',
        'boleto_gerado',
        'boleto_gerado_em',
        'nosso_numero',
        'linha_digitavel',
        'codigo_barras',
        'enviado_remessa',
        'data_envio_remessa',
        'id_remessa',
        'juros_percentual',
        'multa_percentual',
        'multa_apos_dias',
        'desconto_percentual',
        'nao_receber_apos_dias',
        'protestar_apos_dias',
        'aceite',
        'tipo_titulo',
        'mensagem_banco'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'id_contrato'      => 'required|integer',
        'numero_parcela'   => 'required|integer',
        'data_vencimento'  => 'required|valid_date',
        'valor_parcela'    => 'required|decimal',
        'status'           => 'required|in_list[pendente,pago,cancelado,vencido]'
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
     * Busca parcelas com boletos gerados e não enviados
     */
    public function getParcelasParaRemessa()
    {
        return $this->where('boleto_gerado', 1)
            ->where('enviado_remessa', 0)
            ->where('status !=', 'pago')
            ->orderBy('data_vencimento', 'ASC')
            ->findAll();
    }

    /**
     * Busca parcela por nosso número
     */
    public function getByNossoNumero(string $nossoNumero)
    {
        return $this->where('nosso_numero', $nossoNumero)->first();
    }

    /**
     * Marca parcela como enviada em remessa
     */
    public function marcarEnviadaRemessa(int $id, int $idRemessa): bool
    {
        return $this->update($id, [
            'enviado_remessa'    => 1,
            'data_envio_remessa' => date('Y-m-d H:i:s'),
            'id_remessa'         => $idRemessa
        ]);
    }

    /**
     * Desmarca parcela como enviada em remessa
     */
    public function desmarcarEnviadaRemessa(int $id): bool
    {
        return $this->update($id, [
            'enviado_remessa'    => 0,
            'data_envio_remessa' => null,
            'id_remessa'         => null
        ]);
    }

    /**
     * Marca parcela como paga
     */
    public function marcarComoPaga(int $id, float $valorPago, string $dataPagamento): bool
    {
        return $this->update($id, [
            'status'         => 'pago',
            'data_pagamento' => $dataPagamento,
            'valor_pago'     => $valorPago
        ]);
    }

    /**
     * Marca parcela como cancelada
     */
    public function marcarComoCancelada(int $id): bool
    {
        return $this->update($id, ['status' => 'cancelado']);
    }

    /**
     * Busca parcelas vencidas
     */
    public function getParcelasVencidas()
    {
        return $this->where('status', 'pendente')
            ->where('data_vencimento <', date('Y-m-d'))
            ->findAll();
    }

    /**
     * Busca parcelas por contrato
     */
    public function getParcelasPorContrato(int $idContrato)
    {
        return $this->where('id_contrato', $idContrato)
            ->orderBy('numero_parcela', 'ASC')
            ->findAll();
    }
}
