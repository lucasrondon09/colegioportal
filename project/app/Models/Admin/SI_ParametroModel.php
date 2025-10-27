<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class SI_ParametroModel extends Model
{
	//protected $DBGroup = 'portal';
    protected $table = 'si_parametro';
	protected $primaryKey = 'id';
	protected $allowedFields = ['chave', 'valor', 'observacao'];
	
	protected $returnType     = 'object';
    protected $useSoftDeletes = false;
	
	protected $useTimestamps = false;
    

	public function getAnoLetivo(){

		return $this->where('chave', 'ANO_LETIVO_CORRENTE')->first()->valor;

	}

	
}
