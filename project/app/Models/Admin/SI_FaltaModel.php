<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class SI_FaltaModel extends Model
{
	//protected $DBGroup = 'portal';
    protected $table = 'si_falta';
	protected $primaryKey = 'id';
	protected $allowedFields = ['fk_aluno', 'fk_turma', 'trimestre', 'id_disciplina', 'faltas'];
	
	protected $returnType     = 'object';
    protected $useSoftDeletes = false;
	
	protected $useTimestamps = false;
    

	
}
