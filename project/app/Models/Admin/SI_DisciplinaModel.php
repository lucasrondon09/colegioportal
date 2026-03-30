<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class SI_DisciplinaModel extends Model
{
	//protected $DBGroup = 'portal';
    protected $table = 'si_disciplina';
	protected $primaryKey = 'id';
	protected $allowedFields = ['disciplina_id', 'nome'];
	
	protected $returnType     = 'object';
    protected $useSoftDeletes = false;
	
	protected $useTimestamps = false;
    

	public function getDisciplina($disciplina_id = null){

		if(!empty($disciplina_id)){
			return $this->where('disciplina_id', $disciplina_id)->findAll();
		}
		
		return $this->findAll();

	}

	
}
