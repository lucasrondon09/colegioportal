<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class SI_PaiModel extends Model
{
	//protected $DBGroup = 'portal';
    protected $table = 'si_pai';
	protected $primaryKey = 'id';
	protected $allowedFields = ['mat_pai', 'nome_pai', 'senha_pai', 'rg_pai', 'cpf_pai', 'nasc_pai', 'trabalho_pai', 'profissao_pai', 'nat_pai', 'cel_pai', 'fone_pai', 'bairro_pai', 'cid_pai', 'end_pai', 'uf_pai', 'nome_mae', 'rg_mae', 'cpf_mae', 'nasc_mae', 'trabalho_mae', 'profissao_mae', 'nat_mae', 'cel_mae', 'fone_mae', 'end_mae', 'bairro_mae', 'cid_mae', 'uf_mae', 'nome_resp', 'rg_resp', 'cpf_resp', 'nasc_resp', 'fone_resp', 'cel_resp', 'tel_resp', 'end_resp', 'bairro_resp', 'cid_resp', 'uf_resp', 'sacado', 'email_pai', 'email_mae', 'email_resp', 'status', 'rm_pai_estado_civil', 'rm_pai_nacionalidade', 'rm_mae_estado_civil', 'rm_mae_nacionalidade', 'rm_grau_parentesco_responsavel', 'rm_resp_financeiro_nome', 'rm_resp_financeiro_rg', 'rm_resp_financeiro_cpf', 'rm_resp_financeiro_grau_parentesco', 'rm_resp_financeiro_endereco_correspondencia', 'rm_resp_financeiro_bairro', 'rm_resp_financeiro_cep', 'rm_resp_financeiro_cidade_estado', 'devedor', ];
	
	protected $returnType     = 'object';
    protected $useSoftDeletes = false;
	
	protected $useTimestamps = false;
    

	public function getPai($id = null){

		if(!empty($id)){

			return $this->find($id);

		}

		return $this->findAll();
	
	
	}
	
}


