<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class SI_Parcelas_ContratoModel extends Model
{

    protected $table = 'si_parcelas_contrato';
	protected $primaryKey = 'id';
	protected $allowedFields = ['id_contrato', 'tipo_lancamento', 'numero_parcela', 'descricao', 'data_emissao', 'data_vencimento', 'valor_parcela', 'valor_desconto', 'valor_juros', 'valor_multa', 'status'];
	
	protected $returnType     = 'object';
    protected $useSoftDeletes = true;
	
	protected $useTimestamps = true;
	protected $createdField  = 'created_at';
	protected $updatedField  = 'updated_at';
	protected $deletedField  = 'deleted_at';

	/**
	 * Buscar parcelas de um contrato com informações de pagamento
	 * 
	 * @param int $id_contrato
	 * @return array
	 */
	public function getParcelasByContratoComPagamentos($id_contrato){
		return $this->select('
				si_parcelas_contrato.*,
				(SELECT SUM(valor_pago) 
				 FROM si_pagamentos 
				 WHERE si_pagamentos.id_parcela = si_parcelas_contrato.id 
				   AND si_pagamentos.status = "CONFIRMADO"
				) as total_pago,
				(SELECT COUNT(*) 
				 FROM si_pagamentos 
				 WHERE si_pagamentos.id_parcela = si_parcelas_contrato.id 
				   AND si_pagamentos.status = "CONFIRMADO"
				) as qtd_pagamentos
			')
			->where('id_contrato', $id_contrato)
			->orderBy('data_vencimento', 'ASC')
			->findAll();
	}

	/**
	 * Buscar parcelas de um contrato (método original mantido para compatibilidade)
	 * 
	 * @param int $id_contrato
	 * @return array
	 */
	public function getParcelasByContrato($id_contrato){
		return $this->where('id_contrato', $id_contrato)
					->orderBy('data_vencimento', 'ASC')
					->findAll();
	}

	/**
	 * Calcular valor total das parcelas
	 * 
	 * @param int $id_contrato
	 * @return float
	 */
	public function valorTotalParcelas($id_contrato){
		$result = $this->selectSum('valor_parcela')
					   ->where('id_contrato', $id_contrato)
					   ->first();
		return $result ? (float)$result->valor_parcela : 0;
	}

	/**
	 * Contar parcelas de um contrato
	 * 
	 * @param int $id_contrato
	 * @return int
	 */
	public function countParcelasByContrato($id_contrato){
		return $this->where('id_contrato', $id_contrato)
					->countAllResults();
	}

	/**
	 * Calcular valor total recebido (soma dos pagamentos confirmados)
	 * 
	 * @param int $id_contrato
	 * @return float
	 */
	public function valorTotalRecebido($id_contrato){
		$db = \Config\Database::connect();
		$builder = $db->table('si_pagamentos');
		
		$result = $builder->select('SUM(valor_pago) as total_recebido')
						  ->join('si_parcelas_contrato', 'si_parcelas_contrato.id = si_pagamentos.id_parcela')
						  ->where('si_parcelas_contrato.id_contrato', $id_contrato)
						  ->where('si_pagamentos.status', 'CONFIRMADO')
						  ->get()
						  ->getRow();
		
		return $result ? (float)$result->total_recebido : 0;
	}

	/**
	 * Calcular valor total vencido (parcelas com vencimento passado e não pagas)
	 * 
	 * @param int $id_contrato
	 * @return float
	 */
	public function valorTotalVencido($id_contrato){
		$hoje = date('Y-m-d');
		
		// Buscar parcelas vencidas
		$parcelas_vencidas = $this->select('id, valor_parcela')
									->where('id_contrato', $id_contrato)
									->where('data_vencimento <', $hoje)
									->findAll();
		
		if(empty($parcelas_vencidas)){
			return 0;
		}
		
		$db = \Config\Database::connect();
		$total_vencido = 0;
		
		foreach($parcelas_vencidas as $parcela){
			// Verificar quanto foi pago desta parcela
			$builder = $db->table('si_pagamentos');
			$pago = $builder->selectSum('valor_pago')
							->where('id_parcela', $parcela->id)
							->where('status', 'CONFIRMADO')
							->get()
							->getRow();
			
			$valor_pago = $pago ? (float)$pago->valor_pago : 0;
			$valor_restante = $parcela->valor_parcela - $valor_pago;
			
			if($valor_restante > 0){
				$total_vencido += $valor_restante;
			}
		}
		
		return $total_vencido;
	}

	/**
	 * Calcular valor total a receber (parcelas futuras + saldo de parcelas vencidas)
	 * 
	 * @param int $id_contrato
	 * @return float
	 */
	public function valorTotalAReceber($id_contrato){
		$total_parcelas = $this->valorTotalParcelas($id_contrato);
		$total_recebido = $this->valorTotalRecebido($id_contrato);
		
		return $total_parcelas - $total_recebido;
	}

	/**
	 * Contar parcelas por status
	 * 
	 * @param int $id_contrato
	 * @param int $status
	 * @return int
	 */
	public function countParcelasByStatus($id_contrato, $status){
		return $this->where('id_contrato', $id_contrato)
					->where('status', $status)
					->countAllResults();
	}

	/**
	 * Verificar se uma parcela está totalmente paga
	 * 
	 * @param int $id_parcela
	 * @return bool
	 */
	public function isParcelaPaga($id_parcela){
		$parcela = $this->find($id_parcela);
		
		if(!$parcela){
			return false;
		}
		
		$db = \Config\Database::connect();
		$builder = $db->table('si_pagamentos');
		
		$result = $builder->selectSum('valor_pago')
						  ->where('id_parcela', $id_parcela)
						  ->where('status', 'CONFIRMADO')
						  ->get()
						  ->getRow();
		
		$total_pago = $result ? (float)$result->valor_pago : 0;
		
		return $total_pago >= $parcela->valor_parcela;
	}

	/**
	 * Atualizar status da parcela baseado nos pagamentos
	 * 
	 * @param int $id_parcela
	 * @return bool
	 */
	public function atualizarStatusParcela($id_parcela){
		$parcela = $this->find($id_parcela);
		
		if(!$parcela){
			return false;
		}
		
		$db = \Config\Database::connect();
		$builder = $db->table('si_pagamentos');
		
		$result = $builder->selectSum('valor_pago')
						  ->where('id_parcela', $id_parcela)
						  ->where('status', 'CONFIRMADO')
						  ->get()
						  ->getRow();
		
		$total_pago = $result ? (float)$result->valor_pago : 0;
		$hoje = date('Y-m-d');
		
		// Determinar novo status
		// 1-Aberto; 2-Pago; 3-Pago Parcialmente; 4-Atrasado
		if($total_pago >= $parcela->valor_parcela){
			$novo_status = 2; // Pago
		} elseif($total_pago > 0){
			$novo_status = 3; // Pago Parcialmente
		} elseif($parcela->data_vencimento < $hoje){
			$novo_status = 4; // Atrasado
		} else {
			$novo_status = 1; // Aberto
		}
		
		return $this->update($id_parcela, ['status' => $novo_status]);
	}

	/**
	 * Buscar parcelas vencidas de um contrato
	 * 
	 * @param int $id_contrato
	 * @return array
	 */
	public function getParcelasVencidas($id_contrato){
		$hoje = date('Y-m-d');
		
		return $this->where('id_contrato', $id_contrato)
					->where('data_vencimento <', $hoje)
					->whereIn('status', [1, 3, 4]) // Aberto, Pago Parcialmente, Atrasado
					->orderBy('data_vencimento', 'ASC')
					->findAll();
	}

	/**
	 * Buscar próximas parcelas a vencer
	 * 
	 * @param int $id_contrato
	 * @param int $dias
	 * @return array
	 */
	public function getProximasParcelasVencer($id_contrato, $dias = 30){
		$hoje = date('Y-m-d');
		$data_limite = date('Y-m-d', strtotime("+{$dias} days"));
		
		return $this->where('id_contrato', $id_contrato)
					->where('data_vencimento >=', $hoje)
					->where('data_vencimento <=', $data_limite)
					->whereIn('status', [1, 3]) // Aberto, Pago Parcialmente
					->orderBy('data_vencimento', 'ASC')
					->findAll();
	}
	
}


