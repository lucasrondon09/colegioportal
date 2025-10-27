<?php

function authSystem()
{
	if(session()->sistema == 'sispai'){

		throw new \CodeIgniter\Exceptions\PageNotFoundException('Sem permissão ao módulo!');

	}
}