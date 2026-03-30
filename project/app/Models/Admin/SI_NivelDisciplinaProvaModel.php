<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class SI_NivelDisciplinaProvaModel extends Model
{
	//protected $DBGroup = 'portal';
    protected $table = 'si_nivel_disciplina_prova';
	protected $primaryKey = 'id';
	protected $allowedFields = ['id_nivel', 'id_disciplina', 'id_prova', 'ano_vigencia'];
	
	protected $returnType     = 'object';
    protected $useSoftDeletes = false;
	
	protected $useTimestamps = false;
    


	
}
