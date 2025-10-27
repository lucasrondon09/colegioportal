<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class SI_AlunoModel extends Model
{
	//protected $DBGroup = 'portal';
    protected $table = 'si_aluno';
	protected $primaryKey = 'id';
	protected $allowedFields = ['fk_pai', 'matricula', 'data_matricula', 'nome', 'rg', 'senha', 'arquivo_foto', 'nasc', 'cid_nasc', 'uf_nasc', 'end', 'uf', 'bairro', 'cidade', 'fone', 'email', 'prov_aluno', 'fk_usuario_lancou_ultima_atitude', 'permitir_pai_visualizar_atitude', 'status'];
	
	protected $returnType     = 'object';
    protected $useSoftDeletes = false;
	
	protected $useTimestamps = false;
    


	
}
