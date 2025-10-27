<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class SI_TurmaModel extends Model
{
	//protected $DBGroup = 'portal';
    protected $table = 'si_turma';
	protected $primaryKey = 'id';
	protected $allowedFields = ['nome', 'id_nivel', 'id_grau', 'id_periodo', 'ano', 'status'];
	
	protected $returnType     = 'object';
    protected $useSoftDeletes = false;
	
	protected $useTimestamps = false;
    

	public function getTurma($ano){

		return $this->where('ano', $ano)->where('status', 1)->findAll();

	}

	
}
