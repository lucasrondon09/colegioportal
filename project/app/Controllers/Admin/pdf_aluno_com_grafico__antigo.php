<?php


//include_once '../../../../../modelo/fpdf/fpdf.php';


namespace App\Controllers\Admin;

use CodeIgniter\Controller;

class pdf_aluno_com_grafico {

	protected $largura_disciplinas = 50;
	protected $pdf;



	public function __construct() {
		$this->pdf = new FPDF();
	}


	public function gerar_aluno(
		$info_aluno, 
		$output_pdf = true, 
		$gerar_graficos = true, 
		$quebrar_pagina = true) 
		
	{

		$nome = $info_aluno["nome"];
		$matricula = $info_aluno["matricula"];
		
		$nome_turma = $info_aluno["nome_turma"];
		$grau = $info_aluno["grau"];
		$ano = $info_aluno["ano"];
		
		$vetor_notas_boletim = $info_aluno["notas"];
		
		//$vetor_materias_grafico = $info_aluno[2];


		$caminho_imagem = 'visao/pagina/conteudo/relatorio/boletim/logo_boletim.jpg';
		

		
		if($quebrar_pagina) {
			$this->pdf->AddPage ();
			
			$this->pdf->Image($caminho_imagem, 10, 10, 95, 12);
		}else{
			$this->pdf->Ln();
			$this->pdf->Ln();
			$this->pdf->Ln();
			$this->pdf->Ln();
			$this->pdf->Ln();
			$this->pdf->Ln();
			$this->pdf->Ln();
			$this->pdf->Ln();
			
			
			$this->pdf->Image($caminho_imagem, 10, 136, 95, 12);
		}
		$this->pdf->SetFont ( 'Arial', 'B', 11 );




		$this->pdf->SetFillColor(cor::$escala_cinza_1, cor::$escala_cinza_1, cor::$escala_cinza_1);
		$this->pdf->SetDrawColor(cor::$escala_cinza_3, cor::$escala_cinza_3, cor::$escala_cinza_3);

		$this->pdf->Cell(95, 12, utf8_decode(''), 1, 0, 'C', 0);
		$this->pdf->Cell(90, 12, 'Boletim Escolar', 1, 0, 'C', 0);
		$this->pdf->Ln();
		$this->pdf->Cell(185, 7, utf8_decode($nome_turma .' - '. $grau .' - '. $ano), 1, 0, 'L', 0);
		$this->pdf->Ln();
		$this->pdf->Cell(185, 7, utf8_decode('Matrícula: ' . $matricula. ' - ' . $nome), 1, 0, 'L', 0);
		$this->pdf->Ln();


		$this->pdf->SetFont ( 'Arial', '', 11 );

		$this->pdf->Cell($this->largura_disciplinas, 13, 'Componentes Curriculares', 1, 0, 'C', 1);
		$this->pdf->Cell ( 90, 8,	'Notas Trimestrais', 	1,		0, 'C', 1 );
		$this->pdf->Cell ( 45, 8,	'Notas Anuais', 	1,		0, 'C', 1);
		$this->pdf->Ln();
		$this->pdf->Cell($this->largura_disciplinas, 5, '', 'LB', 0, 'C', 1);

		$this->pdf->Cell ( 15, 5, utf8_decode('1º Trim.'),	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, 'Faltas',	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, utf8_decode('2º Trim.'),	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, 'Faltas',	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, utf8_decode('3º Trim.'),	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, 'Faltas',	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, 'MPA',	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, 'REC',	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, 'MA',	1, 		0, 'C', 1);


		$this->notas_boletim($vetor_notas_boletim);
		
		
		$this->pdf->SetFont ( 'Arial', '', 8 );
		$this->pdf->Ln();
		$this->pdf->Ln();
		$this->pdf->Cell ( 50, 4, utf8_decode('Legenda X2: MPA = Média Parcial Anual; REC = Nota de Recuperação; MA = Média Anual'),	0, 0, 'L', 0);
		
		/*
		$this->pdf->Cell ( 50, 4, utf8_decode('MPA = Média Parcial Anual'),	0, 0, 'L', 0);
		$this->pdf->Ln();
		$this->pdf->Cell ( 50, 4, utf8_decode('REC = Nota de Recuperação'),	0, 0, 'L', 0);
		$this->pdf->Ln();
		$this->pdf->Cell ( 50, 4, utf8_decode('MA = Média Anual'),	0, 0, 'L', 0);
		*/
		
		if($gerar_graficos) {
			$this->pdf->SetFont ( 'Arial', '', 11 );
			
			
			$this->pdf->SetFillColor(cor::$branco, cor::$branco, cor::$branco);
			
			
			
			//print_r($vetor_notas_boletim);
			
			$vetor_materias_grafico = array();
			
			
			
			/*
			print_r($info_aluno);
			exit();
			*/
			
			
			
			foreach ($vetor_notas_boletim as $notas) {
				
				$vetor_materia = array(
					$notas['disciplina'],
					str_replace(",", ".", $notas['1t']),
					str_replace(",", ".", $notas['1t_media_turma']),
					str_replace(",", ".", $notas['2t']),
					str_replace(",", ".", $notas['2t_media_turma']),
					str_replace(",", ".", $notas['3t']),
					str_replace(",", ".", $notas['3t_media_turma'])
				);
				array_push($vetor_materias_grafico, $vetor_materia);
			}
			
			//echo '<pre>';		print_r($vetor_materias_grafico);
			
			
			
			
			$this->pdf->Ln();
			$this->pdf->Ln();
	
			$this->pdf->Ln();
	
	
			$this->pdf->Cell ( 40, 5, '', 0, 0, 'L', 1 );
			$this->pdf->Cell ( 68, 7, utf8_decode('Notas 1º e 2º Trimestre'), 1, 0, 'C', 1 );
			$this->pdf->Cell ( 9, 5, '', 0, 0, 'L', 0	 );
			$this->pdf->Cell ( 68, 7, utf8_decode('Notas 3º Trimestre'), 1, 0, 'C', 1 );
			$this->pdf->Ln();
			$this->pdf->Cell ( 40, 5, 'Disciplina', 1, 0, 'L', 1 );
	
	
	
			$y = $this->pdf->GetY() + 5;
			$altura_y = 110;
	
	
			$this->pdf->SetFont ( 'Arial', '', 9 );
			for($i=0; $i<=10; $i++) {
				//$this->pdf->SetDrawColor(cor::$escala_cinza_3, cor::$escala_cinza_3, cor::$escala_cinza_3);
				
				$offset = 3;
				
				$this->pdf->SetX($this->pdf->GetX() + $offset);
				$this->pdf->Cell ( 6, 5, $i, 0, 0, 'C', 0 );
				$this->pdf->SetX($this->pdf->GetX() - $offset);
				//$this->pdf->SetDrawColor(cor::$escala_cinza_2, cor::$escala_cinza_2, cor::$escala_cinza_2);
				$x = $this->pdf->GetX() - 0;
				$this->pdf->Line($x, $y, $x, $y + $altura_y);
			}
			
			

			// NUCLEOA
			$trim1_nucleo_comum = $info_aluno["media_nucleo_comum_1trim"];
			//$this->pdf->Cell ( 6, 5, $trim1_nucleo_comum, 0, 0, 'C', 0 );
				
				
				
			
			
			//$this->pdf->SetDrawColor(cor::$escala_cinza_3, cor::$escala_cinza_3, cor::$escala_cinza_3);
	
	
	
	
			$this->pdf->Cell ( 11, 5, '', 0, 0, 'L', 0 );
			for($i=0; $i<=10; $i++) {
				//$this->pdf->SetDrawColor(cor::$escala_cinza_3, cor::$escala_cinza_3, cor::$escala_cinza_3);
				
				$offset = 3;
				
				$this->pdf->SetX($this->pdf->GetX() + $offset);
				$this->pdf->Cell ( 6, 5, $i, 0, 0, 'C', 0 );
				$this->pdf->SetX($this->pdf->GetX() - $offset);
				//$this->pdf->SetDrawColor(cor::$escala_cinza_2, cor::$escala_cinza_2, cor::$escala_cinza_2);
				$x = $this->pdf->GetX() - 0;
				$this->pdf->Line($x, $y, $x, $y + $altura_y);
			}
			//$this->pdf->Cell ( 4, 5, '1',	1, 0, 'L', 1 );
			$this->pdf->Ln();
	
	
			$this->set_materias_grafico($vetor_materias_grafico, $info_aluno);
	
	
	
			$this->pdf->SetFont ( 'Arial', '', 11 );
	
	
			$this->legendas();
			
			
			
			
			
			//$this->media_materias_nucleo();
		}
		
		if($output_pdf) {
			$this->pdf->Output();
		}	
		
		
		
	}
	
	protected function media_materias_nucleo() {
		$this->pdf->Ln();
		$this->pdf->Cell( 6, 6, utf8_decode('Matérias núcleo'), 0, 0, 'L', 1 );
	}


	protected function get_comprimento_barra($nota) {

		if($nota ==  parametro::nota_sem_valor()) {
			return 0.01;
		}
		
		$nota_zero = 6.0;
		$largura_maxima_nota = 65.8;
		$comprimento_nota = $largura_maxima_nota - $nota_zero; // 59.8
		/*
		 10 > 59
		 $n > x
		 */
		$comp = $nota * $comprimento_nota / 10 + $nota_zero;
		return $comp;
	}

	protected function notas_boletim($vetor) {
		//global $this->pdf, $this->largura_disciplinas;
		
		$indice_atual_nota_boletim = 0;
		foreach ($vetor as $notas) {
			//print_r($disciplina);
			$this->pdf->Ln();
			$this->pdf->SetFillColor(cor::$branco, cor::$branco, cor::$branco);

			if($indice_atual_nota_boletim % 2 != 0) {
				$this->pdf->SetFillColor(cor::$escala_cinza_2, cor::$escala_cinza_2, cor::$escala_cinza_2);
			}
			
			
			
			$this->pdf->Cell ( $this->largura_disciplinas, 5, utf8_decode($notas['disciplina']), 1, 0, 'L', 1);
			$this->pdf->Cell ( 15, 5, $notas['1t'],	1, 		0, 'R', 1 );
			$this->pdf->Cell ( 15, 5, $notas['1tf'],	1, 		0, 'R', 1 );
			$this->pdf->Cell ( 15, 5, $notas['2t'],	1, 		0, 'R', 1 );
			$this->pdf->Cell ( 15, 5, $notas['2tf'],	1, 		0, 'R', 1 );
			$this->pdf->Cell ( 15, 5, $notas['3t'],	1, 		0, 'R', 1 );
			$this->pdf->Cell ( 15, 5, $notas['3tf'],	1, 		0, 'R', 1 );
			
			
			$this->set_cor_texto('preto');
			
			$mpa = $notas['mpa'];
			if($mpa !=  parametro::nota_sem_valor()) {
				$this->set_cor_texto('azul');
				$mpa = str_replace(',', '.', $mpa);
				if($mpa < 6) {
					// vermelho
					$this->set_cor_texto('vermelho');
				}
			}
			
			$this->pdf->Cell ( 15, 5, $notas['mpa'],	1, 		0, 'R', 1 );
			
			$this->pdf->SetTextColor(cor::$preto);
			$this->pdf->Cell ( 15, 5, $notas['rec'],	1, 		0, 'R', 1 );
			
			
		
			
			$this->set_cor_texto('preto');
			
			$ma = $notas['ma'];
			if($ma !=  parametro::nota_sem_valor()) {
				$this->set_cor_texto('azul');
				$ma = str_replace(',', '.', $ma);
				if($ma < 6) {
					// vermelho
					$this->set_cor_texto('vermelho');
				}
			}
			$this->pdf->Cell ( 15, 5, $notas['ma'],	1, 		0, 'R', 1 );
			$this->pdf->SetTextColor(cor::$preto);



			$indice_atual_nota_boletim ++;
		}
	}
	
	protected function set_cor_texto($cor) {
		// cores: vermelho, azul ou preto.
	
		if($cor == 'vermelho') {
			$this->pdf->SetTextColor(cor::$comparativo1_cor2_R, cor::$comparativo1_cor2_G, cor::$comparativo1_cor2_B);
		}
		if($cor == 'azul') {
			$this->pdf->SetTextColor(cor::$comparativo1_cor1_R, cor::$comparativo1_cor1_G, cor::$comparativo1_cor1_B);
		}
		if($cor == 'preto') {
			$this->pdf->SetTextColor(cor::$preto);
		}
	}

	protected function set_materias_grafico($vetor, $info_aluno) {

		//global $this->pdf;
		$padding_left_barras = 50;
		$padding_left_barras_3_trimestre = 127;
		$altura_barras = 0.5;


		
		foreach ($vetor as $materia) {

			$individual_1 = $materia[1];
			$turma_1 = $materia[2];
			$individual_2 = $materia[3];
			$turma_2 = $materia[4];
			$individual_3 = $materia[5];
			$turma_3 = $materia[6];


			// inicio matemática

			$this->pdf->Cell ( 40, 2, '', 0, 0, 'L', 0 );
			$this->pdf->Ln();

			$this->pdf->Cell ( 40, 6, utf8_decode($materia[0]), 0, 1, 'L', 1 );


			// barras 1 e 2 trimestre

			$this->pdf->SetY($this->pdf->GetY() - 5);
			$this->pdf->SetX($padding_left_barras);

			$this->pdf->SetFillColor(cor::$comparativo1_cor1_R, cor::$comparativo1_cor1_G, cor::$comparativo1_cor1_B);
			$this->pdf->Cell ( $this->get_comprimento_barra($individual_1), $altura_barras, '',	0, 0, 'R', 1 );


			$this->pdf->Ln();
			$this->pdf->Ln();
			$this->pdf->SetX($padding_left_barras);

			$this->pdf->SetFillColor(cor::$comparativo1_cor2_R, cor::$comparativo1_cor2_G, cor::$comparativo1_cor2_B);
			$this->pdf->Cell ( $this->get_comprimento_barra($turma_1), $altura_barras, '',	0, 0, 'R', 1 );

			$this->pdf->Ln();
			$this->pdf->Ln();
			$this->pdf->SetX($padding_left_barras);

			$this->pdf->SetFillColor(cor::$comparativo1_cor3_R, cor::$comparativo1_cor3_G, cor::$comparativo1_cor3_B);
			$this->pdf->Cell (  $this->get_comprimento_barra($individual_2), $altura_barras, '',	0, 0, 'R', 1 );

			$this->pdf->Ln();
			$this->pdf->Ln();
			$this->pdf->SetX($padding_left_barras);

			$this->pdf->SetFillColor(cor::$comparativo1_cor4_R, cor::$comparativo1_cor4_G, cor::$comparativo1_cor4_B);
			$this->pdf->Cell (  $this->get_comprimento_barra($turma_2), $altura_barras, '',	0, 0, 'R', 1 );





			// barras 3 trimestre
			$this->pdf->SetY($this->pdf->GetY() - 3);

			$this->pdf->SetX($padding_left_barras_3_trimestre);

			$this->pdf->SetFillColor(cor::$branco, cor::$branco, cor::$branco);
			$this->pdf->Cell ( 1, $altura_barras, '',	0, 0, 'R', 1 );


			$this->pdf->Ln();
			$this->pdf->Ln();
			$this->pdf->SetX($padding_left_barras_3_trimestre);

			$this->pdf->SetFillColor(cor::$comparativo1_cor1_R, cor::$comparativo1_cor1_G, cor::$comparativo1_cor1_B);
			$this->pdf->Cell ( $this->get_comprimento_barra($individual_3), $altura_barras, '',	0, 0, 'R', 1 );

			$this->pdf->Ln();
			$this->pdf->Ln();
			$this->pdf->SetX($padding_left_barras_3_trimestre);

			$this->pdf->SetFillColor(cor::$comparativo1_cor2_R, cor::$comparativo1_cor2_G, cor::$comparativo1_cor2_B);
			$this->pdf->Cell ($this->get_comprimento_barra($turma_3), $altura_barras, '',	0, 0, 'R', 1 );

			$this->pdf->Ln();
			$this->pdf->Ln();
			$this->pdf->SetX($padding_left_barras_3_trimestre);

			$this->pdf->SetFillColor(cor::$branco, cor::$branco, cor::$branco);
			$this->pdf->Cell ( 1, $altura_barras, '',	0, 0, 'R', 1 );

			$this->pdf->Ln();
			// fim 3 trimestre



			$this->pdf->SetX($padding_left_barras);


			$this->pdf->Ln();
			$this->pdf->SetFillColor(cor::$branco, cor::$branco, cor::$branco);
			$this->pdf->SetX(10);

			$this->pdf->Cell ( 40, 2, '', 0, 0, 'L', 1 );
			$this->pdf->Ln();
			//$this->pdf->Line($this->pdf->GetX(), $this->pdf->GetY(), $this->pdf->GetX() + 183, $this->pdf->GetY() );
			
			// fim matemática
			
		}
		

		$this->pdf->Cell ( 40, 2, '', 0, 0, 'L', 0 );
		$this->pdf->Ln();
		
		
		$px = $this->pdf->GetX();
		$py = $this->pdf->GetY();


		
		
		
		/*
		$trim1_nucleo_comum = $info_aluno["media_nucleo_comum_1trim"];
		$trim2_nucleo_comum = $info_aluno["media_nucleo_comum_2trim"];
		$trim3_nucleo_comum = $info_aluno["media_nucleo_comum_3trim"];
		
		$this->pdf->Cell ( 40, 6, utf8_decode("MNC 1º trimestre"), 0, 1, 'L', 1 );
		$this->pdf->Cell ( 40, 6, utf8_decode("MNC 2º trimestre"), 0, 1, 'L', 1 );
		$this->pdf->Cell ( 40, 6, utf8_decode("MNC 3º trimestre"), 0, 1, 'L', 1 );

		

		$linhaX = $this->pdf->GetX();
		$linhaY = $this->pdf->GetY() - 19;
		$linha_largura = 106;
		
		// separar matérias do núcleo comum
		$this->pdf->Line($linhaX, $linhaY, $linhaX + $linha_largura, $linhaY);


		$this->pdf->SetFillColor(cor::$comparativo1_cor2_R);
		
		
		$px_barras_nucleo = $this->pdf->GetX() + 40;
		$py_barras_nucleo = $this->pdf->GetY() - 17;
		
		$dist_entre = 6;
		$altura_barras = 3;	

		
		$this->pdf->SetXY($px_barras_nucleo, $py_barras_nucleo);
		$this->pdf->Cell ( $this->get_comprimento_barra($trim1_nucleo_comum), $altura_barras, '',	0, 0, 'R', 1 );

		$this->pdf->SetXY($px_barras_nucleo, $py_barras_nucleo + $dist_entre);
		$this->pdf->Cell ( $this->get_comprimento_barra($trim2_nucleo_comum), $altura_barras, '',	0, 0, 'R', 1 );

		$this->pdf->SetXY($px_barras_nucleo, $py_barras_nucleo + $dist_entre * 2);
		$this->pdf->Cell ( $this->get_comprimento_barra($trim3_nucleo_comum), $altura_barras, '',	0, 0, 'R', 1 );
		*/
		
		
		$trim1_nucleo_comum = $info_aluno["media_geral_1trim"];
		$trim2_nucleo_comum = $info_aluno["media_geral_2trim"];
		$trim3_nucleo_comum = $info_aluno["media_geral_3trim"];
		
		$this->pdf->Cell ( 40, 6, utf8_decode("Média do 1º trimestre"), 0, 1, 'L', 1 );
		$this->pdf->Cell ( 40, 6, utf8_decode("Média do 2º trimestre"), 0, 1, 'L', 1 );
		$this->pdf->Cell ( 40, 6, utf8_decode("Média do 3º trimestre"), 0, 1, 'L', 1 );

		

		$linhaX = $this->pdf->GetX();
		$linhaY = $this->pdf->GetY() - 19;
		$linha_largura = 106;
		
		// separar matérias do núcleo comum
		$this->pdf->Line($linhaX, $linhaY, $linhaX + $linha_largura, $linhaY);


		$this->pdf->SetFillColor(cor::$comparativo1_cor2_R);
		
		
		$px_barras_nucleo = $this->pdf->GetX() + 40;
		$py_barras_nucleo = $this->pdf->GetY() - 17;
		
		$dist_entre = 6;
		$altura_barras = 3;	

		
		$this->pdf->SetXY($px_barras_nucleo, $py_barras_nucleo);
		$this->pdf->Cell ( $this->get_comprimento_barra($trim1_nucleo_comum), $altura_barras, '',	0, 0, 'R', 1 );

		$this->pdf->SetXY($px_barras_nucleo, $py_barras_nucleo + $dist_entre);
		$this->pdf->Cell ( $this->get_comprimento_barra($trim2_nucleo_comum), $altura_barras, '',	0, 0, 'R', 1 );

		$this->pdf->SetXY($px_barras_nucleo, $py_barras_nucleo + $dist_entre * 2);
		$this->pdf->Cell ( $this->get_comprimento_barra($trim3_nucleo_comum), $altura_barras, '',	0, 0, 'R', 1 );
		
		
		
		
		
		
		
		

		$this->pdf->Cell ( 40, 2, '', 0, 0, 'L', 0 );
		
		//$this->pdf->Cell ( 10, 6, "$trim1_nucleo_comum - $trim2_nucleo_comum - $trim3_nucleo_comum",	0, 1, 'R', 1 );
		
		
		
		$this->pdf->SetXY($px, $py);
		
		
		
		// barras verticais separadoras dos gráficos:
		$this->pdf->SetDrawColor(cor::$escala_cinza_3, cor::$escala_cinza_3, cor::$escala_cinza_3);
		$y_inicio = $this->pdf->GetY() - 95;
		$y = 238;
		$x = 50;
		$this->pdf->Line($x, $y_inicio, $x, $y);
		$x = 127;
		$this->pdf->Line($x, $y_inicio, $x, $y);
		
		
		
		
	}

	protected function legendas() {

		//global $this->pdf;

		
		
		
		
		// legenda ESQUERDA início

		$this->pdf->SetFont ( 'Arial', '', 7 );
		$legenda_x = 51;
		$legenda_x_texto = $legenda_x + 3;
		$posicao_y_esquerda = $this->pdf->GetY() + 13;
		$ALTURA_CELULA_LEGENDA_CORES = 4;

		
		$this->pdf->SetY($posicao_y_esquerda);
		
		//$this->pdf->SetXY($legenda_x, $this->pdf->GetY() + 10);
		$this->pdf->Ln();
		$this->pdf->Ln();
		$this->pdf->Ln();
		$this->pdf->Ln();
		
		$this->pdf->SetX($legenda_x);
		$this->pdf->SetFillColor(cor::$comparativo1_cor1_R, cor::$comparativo1_cor1_G, cor::$comparativo1_cor1_B);
		$this->pdf->Cell ( 2, 2, '',	0, 0, 'R', 1 );

		$this->pdf->SetFillColor(cor::$branco, cor::$branco, cor::$branco);
		$this->pdf->SetXY($legenda_x_texto, $this->pdf->GetY() - 1);
		$this->pdf->Cell( 6, $ALTURA_CELULA_LEGENDA_CORES, utf8_decode('Nota individual no Trimestre'), 0, 0, 'L', 1 );
		$this->pdf->Ln();



		


		$this->pdf->SetX($legenda_x);
		$this->pdf->SetFillColor(cor::$comparativo1_cor2_R, cor::$comparativo1_cor2_G, cor::$comparativo1_cor2_B);
		$this->pdf->Cell ( 2, 2, '',	0, 0, 'R', 1 );

		$this->pdf->SetFillColor(cor::$branco, cor::$branco, cor::$branco);
		$this->pdf->SetXY($legenda_x_texto, $this->pdf->GetY() - 1);
		$this->pdf->Cell ( 6, $ALTURA_CELULA_LEGENDA_CORES,  utf8_decode('Média da turma no Trimestre'), 0, 0, 'L', 1 );
		$this->pdf->Ln();


		
/**

		$this->pdf->SetX($legenda_x);
		$this->pdf->SetFillColor(cor::$comparativo1_cor3_R, cor::$comparativo1_cor3_G, cor::$comparativo1_cor3_B);
		$this->pdf->Cell ( 2, 2, '',	0, 0, 'R', 1 );


		$this->pdf->SetFillColor(cor::$branco, cor::$branco, cor::$branco);
		$this->pdf->SetXY($legenda_x_texto, $this->pdf->GetY() - 1);
		$this->pdf->Cell ( 6, $ALTURA_CELULA_LEGENDA_CORES,  utf8_decode('Nota individual no 2º Trimestre'), 0, 0, 'L', 1 );
		$this->pdf->Ln();






		$this->pdf->SetX($legenda_x);
		$this->pdf->SetFillColor(cor::$comparativo1_cor4_R, cor::$comparativo1_cor4_G, cor::$comparativo1_cor4_B);
		$this->pdf->Cell ( 2, 2, '',	0, 0, 'R', 1 );


		$this->pdf->SetFillColor(cor::$branco, cor::$branco, cor::$branco);
		$this->pdf->SetXY($legenda_x_texto, $this->pdf->GetY() - 1);
		$this->pdf->Cell ( 6, $ALTURA_CELULA_LEGENDA_CORES,  utf8_decode('Média da turma no 2º Trimestre'), 0, 0, 'L', 1 );
		$this->pdf->Ln();

		
*/




		/*
		$this->pdf->SetX($legenda_x);
		
		$this->pdf->SetFillColor(cor::$branco, cor::$branco, cor::$branco);
		$this->pdf->SetXY($legenda_x - 1, $this->pdf->GetY() - 1);
		$this->pdf->Cell ( 6, $ALTURA_CELULA_LEGENDA_CORES,  utf8_decode('MNC = Média do Núcleo Comum'), 0, 0, 'L', 1 );
		$this->pdf->Ln();
		*/
		
		
		// Legenda fim


		
		
		
		
		$this->pdf->SetDrawColor(cor::$escala_cinza_2, cor::$escala_cinza_2, cor::$escala_cinza_2);



		// legenda DIREITA início

		$legenda_x = 128;
		$legenda_x_texto = $legenda_x + 3;
		$this->pdf->SetY($posicao_y_esquerda);

		$this->pdf->Ln();
		$this->pdf->Ln();
		$this->pdf->SetX($legenda_x);
		$this->pdf->SetFillColor(cor::$comparativo1_cor1_R, cor::$comparativo1_cor1_G, cor::$comparativo1_cor1_B);
		$this->pdf->Cell ( 2, 2, '',	0, 0, 'R', 1 );


		$this->pdf->SetFillColor(cor::$branco, cor::$branco, cor::$branco);
		$this->pdf->SetXY($legenda_x_texto, $this->pdf->GetY() - 1);
		$this->pdf->Cell ( 6, $ALTURA_CELULA_LEGENDA_CORES,  utf8_decode('Nota individual no 3º Trimestre'), 0, 0, 'L', 1 );
		$this->pdf->Ln();






		$this->pdf->SetX($legenda_x);
		$this->pdf->SetFillColor(cor::$comparativo1_cor2_R, cor::$comparativo1_cor2_G, cor::$comparativo1_cor2_B);
		$this->pdf->Cell ( 2, 2, '',	0, 0, 'R', 1 );
		
		$this->pdf->SetFillColor(cor::$branco, cor::$branco, cor::$branco);
		$this->pdf->SetXY($legenda_x_texto, $this->pdf->GetY() - 1);
		$this->pdf->Cell ( 6, $ALTURA_CELULA_LEGENDA_CORES,  utf8_decode('Média da turma no 3º Trimestre'), 0, 0, 'L', 1 );
		$this->pdf->Ln();




		// Legenda fim



	}







}


