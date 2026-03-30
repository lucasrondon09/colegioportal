<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class SI_UsuarioModel extends Model
{
	//protected $DBGroup = 'portal';
    protected $table = 'si_usuario';
	protected $primaryKey = 'id';
	protected $allowedFields = ['nome', 'usuario', 'senha', 'permissao', 'status'];
	
	protected $returnType     = 'object';
    protected $useSoftDeletes = false;
	
	protected $useTimestamps = false;
    


	
}
