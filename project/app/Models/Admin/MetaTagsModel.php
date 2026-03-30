<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class MetaTagsModel extends Model
{
    protected $table = 'meta_tags';
	protected $primaryKey = 'id';
	protected $allowedFields = ['descricao', 'palavras_chave'];
	
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
