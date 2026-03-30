<?php

namespace App\Models;

use CodeIgniter\Model;

class SI_CnabLogModel extends Model
{
    protected $table            = 'si_cnab_log';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tipo',
        'operacao',
        'descricao',
        'id_remessa',
        'id_retorno',
        'usuario_id',
        'ip_address'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [
        'tipo'     => 'required|in_list[remessa,retorno,erro,info]',
        'operacao' => 'required|max_length[100]'
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
     * Registra log de remessa
     */
    public function logRemessa(string $operacao, string $descricao, ?int $idRemessa = null, ?int $usuarioId = null): bool
    {
        return $this->insert([
            'tipo'        => 'remessa',
            'operacao'    => $operacao,
            'descricao'   => $descricao,
            'id_remessa'  => $idRemessa,
            'usuario_id'  => $usuarioId,
            'ip_address'  => $this->getIpAddress()
        ]);
    }

    /**
     * Registra log de retorno
     */
    public function logRetorno(string $operacao, string $descricao, ?int $idRetorno = null, ?int $usuarioId = null): bool
    {
        return $this->insert([
            'tipo'        => 'retorno',
            'operacao'    => $operacao,
            'descricao'   => $descricao,
            'id_retorno'  => $idRetorno,
            'usuario_id'  => $usuarioId,
            'ip_address'  => $this->getIpAddress()
        ]);
    }

    /**
     * Registra log de erro
     */
    public function logErro(string $operacao, string $descricao, ?int $usuarioId = null): bool
    {
        return $this->insert([
            'tipo'        => 'erro',
            'operacao'    => $operacao,
            'descricao'   => $descricao,
            'usuario_id'  => $usuarioId,
            'ip_address'  => $this->getIpAddress()
        ]);
    }

    /**
     * Registra log de informação
     */
    public function logInfo(string $operacao, string $descricao, ?int $usuarioId = null): bool
    {
        return $this->insert([
            'tipo'        => 'info',
            'operacao'    => $operacao,
            'descricao'   => $descricao,
            'usuario_id'  => $usuarioId,
            'ip_address'  => $this->getIpAddress()
        ]);
    }

    /**
     * Busca logs recentes
     */
    public function getLogsRecentes($limit = 50, $tipo = null)
    {
        $builder = $this->orderBy('id', 'DESC');
        
        if ($tipo !== null) {
            $builder->where('tipo', $tipo);
        }
        
        return $builder->limit($limit)->findAll();
    }

    /**
     * Busca logs por remessa
     */
    public function getLogsPorRemessa(int $idRemessa)
    {
        return $this->where('id_remessa', $idRemessa)
            ->orderBy('id', 'DESC')
            ->findAll();
    }

    /**
     * Busca logs por retorno
     */
    public function getLogsPorRetorno(int $idRetorno)
    {
        return $this->where('id_retorno', $idRetorno)
            ->orderBy('id', 'DESC')
            ->findAll();
    }

    /**
     * Obtém endereço IP do usuário
     */
    private function getIpAddress(): string
    {
        $request = \Config\Services::request();
        return $request->getIPAddress();
    }

    /**
     * Limpa logs antigos (mais de 90 dias)
     */
    public function limparLogsAntigos(int $dias = 90): int
    {
        $dataLimite = date('Y-m-d H:i:s', strtotime("-{$dias} days"));
        return $this->where('created_at <', $dataLimite)->delete();
    }
}
