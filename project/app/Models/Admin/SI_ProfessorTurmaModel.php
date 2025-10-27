<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class SI_ProfessorTurmaModel extends Model
{
	//protected $DBGroup = 'portal';
    protected $table = 'si_professor_turma_disciplina';
	protected $primaryKey = 'id';
	protected $allowedFields = ['fk_professor', 'fk_turma', 'id_disciplina'];
	
	protected $returnType     = 'object';
    protected $useSoftDeletes = false;
	
	protected $useTimestamps = false;
    
	public function getTurmasDisciplinas($idProfessor, $ano){


		return $this->select('si_turma.nome as turma, si_disciplina.nome as disciplina, si_disciplina.disciplina_id as idDisciplina, si_turma.id as idTurma')
						->where('fk_professor', $idProfessor)
						->where('si_turma.ano', $ano)
						->join('si_turma', 'si_turma.id = si_professor_turma_disciplina.fk_turma')	
						->join('si_disciplina', 'si_disciplina.disciplina_id = si_professor_turma_disciplina.id_disciplina')	
						->findAll();

	}


	
}
