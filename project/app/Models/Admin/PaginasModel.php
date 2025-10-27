<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class PaginasModel extends Model
{
    protected $table = 'paginas';
	protected $primaryKey = 'id';
	protected $allowedFields = ['idCategoria', 'titulo', 'subtitulo', 'texto', 'capa', 'status'];
	
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
