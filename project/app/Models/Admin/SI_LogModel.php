<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class SI_LogModel extends Model
{
	//protected $DBGroup = 'portal';
    protected $table = 'si_log';
	protected $primaryKey = 'id';
	protected $allowedFields = ['data', 'id_usuario', 'tipo', 'texto'];
	
	protected $returnType     = 'object';
    protected $useSoftDeletes = false;
	
	protected $useTimestamps = false;

	
}
