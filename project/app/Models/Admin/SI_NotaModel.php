<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class SI_NotaModel extends Model
{
	//protected $DBGroup = 'portal';
    protected $table = 'si_nota';
	protected $primaryKey = 'id';
	protected $allowedFields = ['fk_aluno', 'fk_turma', 'trimestre', 'id_disciplina', 'id_prova', 'nota'];
	
	protected $returnType     = 'object';
    protected $useSoftDeletes = false;
	
	protected $useTimestamps = false;
    


	
}
