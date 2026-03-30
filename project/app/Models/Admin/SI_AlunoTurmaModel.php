<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class SI_AlunoTurmaModel extends Model
{
	//protected $DBGroup = 'portal';
    protected $table = 'si_aluno_turma';
	protected $primaryKey = 'id';
	protected $allowedFields = ['fk_aluno', 'fk_turma'];
	
	protected $returnType     = 'object';
    protected $useSoftDeletes = false;
	
	protected $useTimestamps = false;

	
}
