<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class SI_Parcelas_ContratoModel extends Model
{

    protected $table = 'si_parcelas_contrato';
	protected $primaryKey = 'id';
	protected $allowedFields = ['id_contrato', 'id_forma_pagamento', 'tipo_lancamento', 'numero_parcela', 'data_vencimento', 'valor_parcela', 'status', 'data_pagamento', 'valor_pago'];
	
	protected $returnType     = 'object';
    protected $useSoftDeletes = false;
	
	protected $useTimestamps = true;
	protected $createdField  = 'created_at';
	protected $updatedField  = 'updated_at';
	protected $deletedField  = 'deleted_at';

	public function getParcelasByContrato($id_contrato){
		return $this->where('id_contrato', $id_contrato)
					->orderBy('data_vencimento
					', 'ASC')
					->findAll();
	}

	public function valorTotalParcelas($id_contrato){
		$result = $this->selectSum('valor_parcela')
					   ->where('id_contrato', $id_contrato)
					   ->first();
		return $result ? $result->valor_parcela : 0;
	}

	public function countParcelasByContrato($id_contrato){
		return $this->where('id_contrato', $id_contrato)
					->countAllResults();
	}
	

	
}


