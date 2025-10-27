<?php

namespace App\Controllers\Admin;
use CodeIgniter\Controller;

date_default_timezone_set('America/Cuiaba');

class parametro extends Controller{
	
	public static function nota_sem_valor() {
		return '	';
	}
	
	public static $ano_inicio_sistema = 2005;
	
	public static function provas() {
		$vetor = array(
			's1' => 'S1',
			's2' => 'S2',
			's3' => 'S3',
			's4' => 'S4',
			'f' => 'FOR',
			's' => 'SIM',
			'r' => 'REC'
		);
		
		return $vetor;
	}
	
	public static function provas_sem_rec() {
		$vetor = array(
			's1' => 'S1',
			's2' => 'S2',
			's3' => 'S3',
			's4' => 'S4',
			'f' => 'FOR',
			's' => 'SIM'
		);
		
		return $vetor;
	}
	
	
	public static  function niveis() {
		$vetor = array(
			'm' => 'MATERNAL',
			'i1' => 'INFANTIL I',
			'i2' => 'INFANTIL II',
			'i3' => 'INFANTIL III',
			'1a' => '1º ANO',
			'2a' => '2º ANO',
			'3a' => '3º ANO',
			'4a' => '4º ANO',
			'5a' => '5º ANO',
			'6a' => '6º ANO',
			'7a' => '7º ANO',
			'8a' => '8º ANO',
			'9a' => '9º ANO',
			'1s' => '1º SÉRIE',
			'2s' => '2º SÉRIE',
			'3s' => '3º SÉRIE',
			'4s' => '4º SÉRIE',
			'5s' => '5º SÉRIE',
			'6s' => '6º SÉRIE',
			'7s' => '7º SÉRIE',
			'8s' => '8º SÉRIE'
		);
		
		return $vetor;
	}
	
	public static  function niveis_lancar_boletim() {
		$vetor = array(
				'1a' => '1º ANO',
				'2a' => '2º ANO',
				'3a' => '3º ANO',
				'4a' => '4º ANO',
				'5a' => '5º ANO',
				'6a' => '6º ANO',
				'7a' => '7º ANO',
				'8a' => '8º ANO',
				'9a' => '9º ANO'
		);
	
		return $vetor;
	}

	public static function graus() {
		$vetor = array(
			'ei' => 'Educação infantil',
			'ef' => 'Ensino fundamental',
			'm' => 'Maternal'
		);
		
		return $vetor;
	}
	

	public static function periodos() {
		$vetor = array(
			'm' => 'MATUTINO',
			'v' => 'VESPERTINO',
			'd' => 'DIURNO'
		);
		
		return $vetor;
	}
	
	
	
	public static function disciplinas($somente_nucleo_comum = false) {
		/*
		$vetor = array(
			'p' => 'Português',
			'ma' => 'Matemática',
			'c' => 'Ciências',
			'l' => 'Língua Estrangeira',
			'h' => 'História',
			'g' => 'Geografia',
			'f' => 'Filosofia',
			'e' => 'Educação física',
			'mu' => 'Música',
			'a' => 'Artes'
		);
		*/
		$vetor = array(
			'p' => 'Português',
			'ma' => 'Matemática',
			'h' => 'História',
			'g' => 'Geografia',
			'c' => 'Ciências',
			'l' => 'Língua Estrangeira',
			'f' => 'Filosofia',
			'e' => 'Educação física',
			'mu' => 'Música',
			'a' => 'Artes'
		);
		
		if($somente_nucleo_comum) {
			$vetor = array(
				'p' => 'Português',
				'ma' => 'Matemática',
				'h' => 'História',
				'g' => 'Geografia',
				'c' => 'Ciências'
			);
			
		}
		
		return $vetor;
	}
	
	
	
	// retorna de 2006 até 1 ano após o ano atual.
	public static function anos_letivos() {
		$vetor = array();
		
		$ano_atual = (int)date('Y');
		$proximo = $ano_atual + 1;
		
		$inicio = 2006;
		$ultimo = $proximo;
		
		for($i=$inicio; $i<=$ultimo; $i++) {
			array_push($vetor, $i); 
		}
		
		return $vetor;
	}
	
	
	
	
	
	



	public static  function get_grau($id) 
	{
		$vetor = parametro::graus();
		
		foreach ($vetor as $k => $v) {
			if($k == $id) {
				return $v;
			}
		}
		return 'valor incorreto.';
	}
	public static  function get_periodo($id) 
	{
		$vetor = parametro::periodos();
		
		foreach ($vetor as $k => $v) {
			if($k == $id) {
				return $v;
			}
		}
		return 'valor incorreto.';
	}
	public static  function get_disciplina($id) 
	{
		$vetor = parametro::disciplinas();
		
		foreach ($vetor as $k => $v) {
			if($k == $id) {
				return $v;
			}
		}
		return 'valor incorreto.';
	}
	public static  function get_nivel($id) 
	{
		$vetor = parametro::niveis();
		
		foreach ($vetor as $k => $v) {
			if($k == $id) {
				return $v;
			}
		}
		return 'valor incorreto.';
	}
	public static  function get_prova($id) 
	{
		$vetor = parametro::provas();
		
		foreach ($vetor as $k => $v) {
			if($k == $id) {
				return $v;
			}
		}
		return 'valor incorreto.';
	}
	
}

//print_r($a); 






?>