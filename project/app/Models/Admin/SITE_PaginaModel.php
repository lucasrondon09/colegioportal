<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class SITE_PaginaModel extends Model
{
	//protected $DBGroup = 'portal';
    protected $table = 'site_pagina';
	protected $primaryKey = 'id';
	protected $allowedFields = ['pagina', 'descricao'];
	
	protected $returnType     = 'object';
    protected $useSoftDeletes = false;
	
	protected $useTimestamps = false;

	
}
