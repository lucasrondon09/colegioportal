<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class LeadsModel extends Model
{
    protected $table = 'leads';
	protected $primaryKey = 'id';
	protected $allowedFields = ['nome', 'email', 'telefone', 'cpf'];
	
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
