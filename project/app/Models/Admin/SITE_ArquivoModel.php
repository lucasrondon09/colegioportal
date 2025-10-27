<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class SITE_ArquivoModel extends Model
{
	//protected $DBGroup = 'portal';
    protected $table = 'site_arquivo';
	protected $primaryKey = 'id';
	protected $allowedFields = ['descricao', 'nome_arquivo', 'data', 'post', 'ordem'];
	
	protected $returnType     = 'object';
    protected $useSoftDeletes = false;
	
	protected $useTimestamps = false;

	
}
