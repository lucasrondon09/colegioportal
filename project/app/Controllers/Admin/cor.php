<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
//include_once '../../../../../modelo/fpdf/fpdf.php';



class cor extends Controller{
	public static $branco = 255;
	public static $escala_cinza_1 = 220;
	public static $escala_cinza_2 = 240;
	public static $escala_cinza_3 = 160;
	public static $escala_cinza_4 = 100;
	public static $preto = 0;
	
	/*
	 * 
	public static $comparativo1_cor1_R = 63;
	public static $comparativo1_cor1_G = 66;
	public static $comparativo1_cor1_B = 192;

	public static $comparativo1_cor2_R = 191;
	public static $comparativo1_cor2_G = 64;
	public static $comparativo1_cor2_B = 67;

	public static $comparativo1_cor3_R = 185;
	public static $comparativo1_cor3_G = 191;
	public static $comparativo1_cor3_B = 64;
	
	public static $comparativo1_cor4_R = 64;
	public static $comparativo1_cor4_G = 191;
	public static $comparativo1_cor4_B = 83;

	 */
	
	
	public static $comparativo1_cor1_R = 10;
	public static $comparativo1_cor1_G = 10;
	public static $comparativo1_cor1_B = 10;

	public static $comparativo1_cor2_R = 150;
	public static $comparativo1_cor2_G = 150;
	public static $comparativo1_cor2_B = 150;

	public static $comparativo1_cor3_R = 80;
	public static $comparativo1_cor3_G = 80;
	public static $comparativo1_cor3_B = 80;
	
	public static $comparativo1_cor4_R = 180;
	public static $comparativo1_cor4_G = 180;
	public static $comparativo1_cor4_B = 180;
	
}

/*
class pdf_turma_sem_grafico extends pdf_turma_com_grafico {
	
	public function gerar_turma($vetor_info) {
		$i=0;
		foreach ($vetor_info as $info_aluno) {
			$quebrar_pagina = false;
			if($i % 2 == 0) {
				$quebrar_pagina = true;
			}
			//print_r($info_aluno);
			$this->gerar_aluno($info_aluno, false, false, $quebrar_pagina);
			
			//echo "VARIAVEL I = '$i'; ";
			$i++;
		}
		
		
		//$this->gerar_aluno($vetor_info[0]);
		$this->pdf->Output();
	}
}
*/

