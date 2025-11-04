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
							->where('si_pagamentos.status', 'CONFIRMADO')
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
	



	/**
	 * Buscar todos os lançamentos com informações completas
	 * Para visão geral do sistema financeiro
	 * 
	 * @param array $filtros
	 * @return array
	 */
	public function getAllLancamentosCompletos($filtros = [])
	{
		$builder = $this->select('
				si_parcelas_contrato.*,
				si_contrato.numero_contrato,
				si_contrato.id_aluno,
				si_contrato.id_responsavel,
				si_contrato.id_turma,
				si_aluno.nome as aluno_nome,
				si_aluno.matricula as aluno_matricula,
				si_turma.nome as turma_nome,
				si_turma.ano as turma_ano,
				si_pai.rm_resp_financeiro_nome as responsavel_nome,
				si_pai.rm_resp_financeiro_cpf as responsavel_cpf,
				si_tipo_lancamento.nome as tipo_lancamento_nome,
				si_tipo_lancamento.codigo as tipo_lancamento_codigo,
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
			->join('si_contrato', 'si_contrato.id = si_parcelas_contrato.id_contrato', 'left')
			->join('si_aluno', 'si_aluno.id = si_contrato.id_aluno', 'left')
			->join('si_turma', 'si_turma.id = si_contrato.id_turma', 'left')
			->join('si_pai', 'si_pai.id = si_contrato.id_responsavel', 'left')
			->join('si_tipo_lancamento', 'si_tipo_lancamento.id = si_parcelas_contrato.tipo_lancamento', 'left');

		// Aplicar filtros
		if (!empty($filtros['status'])) {
			$builder->where('si_parcelas_contrato.status', $filtros['status']);
		}

		if (!empty($filtros['tipo_lancamento'])) {
			$builder->where('si_parcelas_contrato.tipo_lancamento', $filtros['tipo_lancamento']);
		}

		if (!empty($filtros['id_aluno'])) {
			$builder->where('si_contrato.id_aluno', $filtros['id_aluno']);
		}

		if (!empty($filtros['id_turma'])) {
			$builder->where('si_contrato.id_turma', $filtros['id_turma']);
		}

		if (!empty($filtros['id_responsavel'])) {
			$builder->where('si_contrato.id_responsavel', $filtros['id_responsavel']);
		}

		if (!empty($filtros['data_vencimento_inicio'])) {
			$builder->where('si_parcelas_contrato.data_vencimento >=', $filtros['data_vencimento_inicio']);
		}

		if (!empty($filtros['data_vencimento_fim'])) {
			$builder->where('si_parcelas_contrato.data_vencimento <=', $filtros['data_vencimento_fim']);
		}

		if (!empty($filtros['search'])) {
			$builder->groupStart()
				->like('si_aluno.nome', $filtros['search'])
				->orLike('si_aluno.matricula', $filtros['search'])
				->orLike('si_pai.rm_resp_financeiro_nome', $filtros['search'])
				->orLike('si_contrato.numero_contrato', $filtros['search'])
				->orLike('si_parcelas_contrato.descricao', $filtros['search'])
				->groupEnd();
		}

		return $builder->orderBy('si_parcelas_contrato.data_vencimento', 'DESC');
	}

	/**
	 * Obter resumo financeiro geral do sistema
	 * Dashboard com estatísticas
	 * 
	 * @param array $filtros
	 * @return array
	 */
	public function getResumoFinanceiroGeral($filtros = [])
	{
		$db = \Config\Database::connect();
		$hoje = date('Y-m-d');
		$data_30_dias = date('Y-m-d', strtotime('+30 days'));

		// Query base para aplicar filtros
		$whereClause = $this->buildWhereClause($filtros);

		// Total a Receber (todas as parcelas abertas + parcialmente pagas)
		$query = $db->query("
			SELECT 
				COALESCE(SUM(
					si_parcelas_contrato.valor_parcela - 
					COALESCE((
						SELECT SUM(valor_pago) 
						FROM si_pagamentos 
						WHERE si_pagamentos.id_parcela = si_parcelas_contrato.id 
						  AND si_pagamentos.status = 'CONFIRMADO'
					), 0)
				), 0) as total_a_receber
			FROM si_parcelas_contrato
			LEFT JOIN si_contrato ON si_contrato.id = si_parcelas_contrato.id_contrato
			LEFT JOIN si_aluno ON si_aluno.id = si_contrato.id_aluno
			LEFT JOIN si_turma ON si_turma.id = si_contrato.id_turma
			LEFT JOIN si_pai ON si_pai.id = si_contrato.id_responsavel
			WHERE si_parcelas_contrato.deleted_at IS NULL
			  AND si_parcelas_contrato.status IN (1, 3, 4)
			  {$whereClause}
		");
		$total_a_receber = $query->getRow()->total_a_receber ?? 0;

		// Total Recebido (soma de todos os pagamentos confirmados)
		$query = $db->query("
			SELECT 
				COALESCE(SUM(si_pagamentos.valor_pago), 0) as total_recebido
			FROM si_pagamentos
			INNER JOIN si_parcelas_contrato ON si_parcelas_contrato.id = si_pagamentos.id_parcela
			LEFT JOIN si_contrato ON si_contrato.id = si_parcelas_contrato.id_contrato
			LEFT JOIN si_aluno ON si_aluno.id = si_contrato.id_aluno
			LEFT JOIN si_turma ON si_turma.id = si_contrato.id_turma
			LEFT JOIN si_pai ON si_pai.id = si_contrato.id_responsavel
			WHERE si_pagamentos.status = 'CONFIRMADO'
			  AND si_parcelas_contrato.deleted_at IS NULL
			  {$whereClause}
		");
		$total_recebido = $query->getRow()->total_recebido ?? 0;

		// Lançamentos Vencidos (não pagos ou parcialmente pagos)
		$query = $db->query("
			SELECT 
				COUNT(*) as qtd_vencidos,
				COALESCE(SUM(
					si_parcelas_contrato.valor_parcela - 
					COALESCE((
						SELECT SUM(valor_pago) 
						FROM si_pagamentos 
						WHERE si_pagamentos.id_parcela = si_parcelas_contrato.id 
						  AND si_pagamentos.status = 'CONFIRMADO'
					), 0)
				), 0) as valor_vencido
			FROM si_parcelas_contrato
			LEFT JOIN si_contrato ON si_contrato.id = si_parcelas_contrato.id_contrato
			LEFT JOIN si_aluno ON si_aluno.id = si_contrato.id_aluno
			LEFT JOIN si_turma ON si_turma.id = si_contrato.id_turma
			LEFT JOIN si_pai ON si_pai.id = si_contrato.id_responsavel
			WHERE si_parcelas_contrato.deleted_at IS NULL
			  AND si_parcelas_contrato.data_vencimento < '{$hoje}'
			  AND si_parcelas_contrato.status IN (1, 3, 4)
			  {$whereClause}
		");
		$vencidos = $query->getRow();

		// Lançamentos a Vencer (próximos 30 dias)
		$query = $db->query("
			SELECT 
				COUNT(*) as qtd_a_vencer,
				COALESCE(SUM(
					si_parcelas_contrato.valor_parcela - 
					COALESCE((
						SELECT SUM(valor_pago) 
						FROM si_pagamentos 
						WHERE si_pagamentos.id_parcela = si_parcelas_contrato.id 
						  AND si_pagamentos.status = 'CONFIRMADO'
					), 0)
				), 0) as valor_a_vencer
			FROM si_parcelas_contrato
			LEFT JOIN si_contrato ON si_contrato.id = si_parcelas_contrato.id_contrato
			LEFT JOIN si_aluno ON si_aluno.id = si_contrato.id_aluno
			LEFT JOIN si_turma ON si_turma.id = si_contrato.id_turma
			LEFT JOIN si_pai ON si_pai.id = si_contrato.id_responsavel
			WHERE si_parcelas_contrato.deleted_at IS NULL
			  AND si_parcelas_contrato.data_vencimento >= '{$hoje}'
			  AND si_parcelas_contrato.data_vencimento <= '{$data_30_dias}'
			  AND si_parcelas_contrato.status IN (1, 3)
			  {$whereClause}
		");
		$a_vencer = $query->getRow();

		// Contadores por status
		$query = $db->query("
			SELECT 
				COUNT(CASE WHEN si_parcelas_contrato.status = 1 THEN 1 END) as abertos,
				COUNT(CASE WHEN si_parcelas_contrato.status = 2 THEN 1 END) as pagos,
				COUNT(CASE WHEN si_parcelas_contrato.status = 3 THEN 1 END) as parcialmente_pagos,
				COUNT(CASE WHEN si_parcelas_contrato.status = 4 THEN 1 END) as atrasados
			FROM si_parcelas_contrato
			LEFT JOIN si_contrato ON si_contrato.id = si_parcelas_contrato.id_contrato
			LEFT JOIN si_aluno ON si_aluno.id = si_contrato.id_aluno
			LEFT JOIN si_turma ON si_turma.id = si_contrato.id_turma
			LEFT JOIN si_pai ON si_pai.id = si_contrato.id_responsavel
			WHERE si_parcelas_contrato.deleted_at IS NULL
			  {$whereClause}
		");
		$contadores = $query->getRow();

		return [
			'total_a_receber' => (float)$total_a_receber,
			'total_recebido' => (float)$total_recebido,
			'qtd_vencidos' => (int)($vencidos->qtd_vencidos ?? 0),
			'valor_vencido' => (float)($vencidos->valor_vencido ?? 0),
			'qtd_a_vencer' => (int)($a_vencer->qtd_a_vencer ?? 0),
			'valor_a_vencer' => (float)($a_vencer->valor_a_vencer ?? 0),
			'qtd_abertos' => (int)($contadores->abertos ?? 0),
			'qtd_pagos' => (int)($contadores->pagos ?? 0),
			'qtd_parcialmente_pagos' => (int)($contadores->parcialmente_pagos ?? 0),
			'qtd_atrasados' => (int)($contadores->atrasados ?? 0),
		];
	}

	/**
	 * Construir cláusula WHERE para filtros
	 * 
	 * @param array $filtros
	 * @return string
	 */
	private function buildWhereClause($filtros = [])
	{
		$where = [];

		if (!empty($filtros['status'])) {
			$where[] = "si_parcelas_contrato.status = " . (int)$filtros['status'];
		}

		if (!empty($filtros['tipo_lancamento'])) {
			$where[] = "si_parcelas_contrato.tipo_lancamento = " . (int)$filtros['tipo_lancamento'];
		}

		if (!empty($filtros['id_aluno'])) {
			$where[] = "si_contrato.id_aluno = " . (int)$filtros['id_aluno'];
		}

		if (!empty($filtros['id_turma'])) {
			$where[] = "si_contrato.id_turma = " . (int)$filtros['id_turma'];
		}

		if (!empty($filtros['id_responsavel'])) {
			$where[] = "si_contrato.id_responsavel = " . (int)$filtros['id_responsavel'];
		}

		if (!empty($filtros['data_vencimento_inicio'])) {
			$where[] = "si_parcelas_contrato.data_vencimento >= '" . $filtros['data_vencimento_inicio'] . "'";
		}

		if (!empty($filtros['data_vencimento_fim'])) {
			$where[] = "si_parcelas_contrato.data_vencimento <= '" . $filtros['data_vencimento_fim'] . "'";
		}

		return !empty($where) ? ' AND ' . implode(' AND ', $where) : '';
	}

	/**
	 * Obter estatísticas por tipo de lançamento
	 * 
	 * @param array $filtros
	 * @return array
	 */
	public function getEstatisticasPorTipo($filtros = [])
	{
		$db = \Config\Database::connect();
		$whereClause = $this->buildWhereClause($filtros);

		$query = $db->query("
			SELECT 
				si_tipo_lancamento.nome as tipo_nome,
				si_tipo_lancamento.codigo as tipo_codigo,
				COUNT(*) as quantidade,
				SUM(si_parcelas_contrato.valor_parcela) as valor_total,
				SUM(COALESCE((
					SELECT SUM(valor_pago) 
					FROM si_pagamentos 
					WHERE si_pagamentos.id_parcela = si_parcelas_contrato.id 
					  AND si_pagamentos.status = 'CONFIRMADO'
				), 0)) as valor_recebido
			FROM si_parcelas_contrato
			LEFT JOIN si_contrato ON si_contrato.id = si_parcelas_contrato.id_contrato
			LEFT JOIN si_aluno ON si_aluno.id = si_contrato.id_aluno
			LEFT JOIN si_turma ON si_turma.id = si_contrato.id_turma
			LEFT JOIN si_pai ON si_pai.id = si_contrato.id_responsavel
			LEFT JOIN si_tipo_lancamento ON si_tipo_lancamento.id = si_parcelas_contrato.tipo_lancamento
			WHERE si_parcelas_contrato.deleted_at IS NULL
			  {$whereClause}
			GROUP BY si_parcelas_contrato.tipo_lancamento, si_tipo_lancamento.nome, si_tipo_lancamento.codigo
			ORDER BY valor_total DESC
		");

		return $query->getResultArray();
	}
		
}


