<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class SI_RecuperacaoModel extends Model
{
	//protected $DBGroup = 'portal';
    protected $table = 'si_recuperacao';
	protected $primaryKey = 'id';
	protected $allowedFields = ['fk_aluno', 'fk_turma', 'id_disciplina', 'nota'];
	
	protected $returnType     = 'object';
    protected $useSoftDeletes = false;
	
	protected $useTimestamps = false;
    

	
}
