<?php

function permission()
{
	$session = session();
		
	if(!isset($session->userId)){
		$session->destroy();
		throw new \CodeIgniter\Exceptions\PageNotFoundException('Você precisa estar logado na aplicação!');
		

	}
}