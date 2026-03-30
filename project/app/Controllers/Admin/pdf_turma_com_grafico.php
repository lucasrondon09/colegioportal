<?php


//include_once '../../../../../modelo/fpdf/fpdf.php';


namespace App\Controllers\Admin;

use CodeIgniter\Controller;

class pdf_turma_com_grafico extends pdf_aluno_com_grafico {

	public function gerar_turma($vetor_info) {
		
		
		foreach ($vetor_info as $info_aluno) {
			$this->gerar_aluno($info_aluno, false);
		}
		
		
		//$this->gerar_aluno($vetor_info[0]);
		$this->pdf->Output();
	}





}


