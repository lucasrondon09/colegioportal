<?php

use App\Models\Admin\LeadsModel;


//--------------------------------------------------------------------
function leadsInsert($fields)
{

	$modelLeads	= new LeadsModel();

	$checkEmail = $modelLeads->like('email', $fields['email'])->find();

	if(!$checkEmail){

		$modelLeads->insert($fields);

		return true;
		
	}

	return false;

}
