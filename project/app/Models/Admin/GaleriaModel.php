<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class GaleriaModel extends Model
{
    protected $table = 'galeria';
	protected $primaryKey = 'id';
	protected $allowedFields = ['idUsuario', 'capa', 'idCategoria','titulo', 'status', 'ano'];
	
	protected $returnType     = 'object';
    protected $useSoftDeletes = true;
	
	protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    
		
	public function get($id = null)
	{
		if($id <> null)
		{
			return $this->find($id);
				
		}
		
		return $this->find();
	
	}


	
}
