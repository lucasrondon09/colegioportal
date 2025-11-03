<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class SI_ContratoModel extends Model
{

    protected $table = 'si_contrato';
	protected $primaryKey = 'id';
	protected $allowedFields = ['numero_contrato', 'id_aluno', 'id_responsavel', 'id_turma', 'tipo_contrato','data_inicio', 'data_fim', 'valor_total', 'parcelas', 'dia_vencimento', 'status'];
	
	protected $returnType     = 'object';
    protected $useSoftDeletes = false;
	
	protected $useTimestamps = true;
	protected $createdField  = 'created_at';
	protected $updatedField  = 'updated_at';
	protected $deletedField  = 'deleted_at';


	public function getContratoByAluno($id_aluno){
		return $this->where('id_aluno', $id_aluno)->first();
	}	

	public function getContratoById($id){
		return $this->select('si_contrato.*, si_aluno.nome as nome_aluno, si_pai.rm_resp_financeiro_nome as nome_responsavel')
				->join('si_aluno', 'si_aluno.id = si_contrato.id_aluno')
				->join('si_pai', 'si_pai.id = si_contrato.id_responsavel')
				->where('si_contrato.id', $id)
				->first();
	}

	public function getContratosByTurma($id_turma){
		return $this->where('id_turma', $id_turma)->findAll();
	}

	public function getContratosByResponsavel($id_responsavel){
		return $this->where('id_responsavel', $id_responsavel)->findAll();
	}

	public function getAllContratos(){
		return $this->findAll();
	}

	
}


