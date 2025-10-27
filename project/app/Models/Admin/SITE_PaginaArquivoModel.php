<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class SITE_PaginaArquivoModel extends Model
{
	//protected $DBGroup = 'portal';
    protected $table = 'site_pagina_arquivo';
	protected $primaryKey = 'id';
	protected $allowedFields = ['fk_pagina', 'fk_arquivo'];
	
	protected $returnType     = 'object';
    protected $useSoftDeletes = false;
	
	protected $useTimestamps = false;

	
}
