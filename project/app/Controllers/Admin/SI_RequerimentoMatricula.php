<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\Controller;
use App\Models\Admin\SI_PaiModel;
use App\Controllers\Fpdf\fpdf;
use App\Controllers\tcpdf\tcpdf;
use App\Models\Admin\SI_AlunoModel;
use App\Models\Admin\SI_NivelDisciplinaProvaModel;
use App\Models\Admin\SI_NotaModel;
use App\Models\Admin\SI_ParametroModel;
use App\Models\Admin\SI_TurmaModel;
use App\Models\Admin\SI_AlunoTurmaModel;



class FPDF_Relatorio_matricula extends FPDF {

    public $empresa = 1;

    public function setEmpresa($numero) {
        $this->empresa = $numero;
    }
    function Header()
    {
        // To be implemented in your own inherited classbase_url().'/assets/dist/img/arquivo_foto_aluno/example.jpg'
        $caminho_imagem =  base_url('/assets/admin/dist/img/TOPO2.jpg');

        if($this->empresa == 2) {
            $caminho_imagem = base_url('/assets/admin/dist/img/TOPO2_EMPRESA2.jpg');
        }

        $quadrado = 25;
        $this->Image($caminho_imagem, 6, 4, 198, 15);
    }
    function Footer()
    {
        // To be implemented in your own inherited class
        $caminho_imagem = base_url('/assets/admin/dist/img/RODAPEP_LISTRAS.jpg');

        $quadrado = 25;
        $this->Image($caminho_imagem, 6, 286, 198, 3);
    }

}


class SI_RequerimentoMatricula extends Controller {


	private $largura_matricula = 19;
	private $largura_pagina = 190;
	private $largura_nome = 100;
	private $largura_faltas = 150;
	private $pdf;


    public $empresa = 1;

    public function setEmpresa($numero) {
        $this->empresa = $numero;
        $this->pdf->setEmpresa($this->empresa);
    }

	public function __construct() {
		$this->pdf = new FPDF_Relatorio_matricula();
	}

	public function adicionar_vetor() {

		$aluno_id = $this->request->getVar('alunoId');
		$turma_id = $this->request->getVar('turmaId');

		
		
		//$cep = util::GET('cep');
		
		
		$altura_linha_texto = 4;
		$altura_linha_separar = @$altura_linha_separar;
		
		$tamanho_fonte = 9;
		$aluno = (new SI_AlunoModel())->find($aluno_id);
		//$aluno = EASYNC5__si_aluno::getByPK($aluno_id);
		
	//		$caminho_imagem = 'visao/pagina/conteudo/relatorio/boletim/logo_boletim.jpg';
		

		
		$this->pdf->AddPage();

		//$this->pdf->Image($caminho_imagem, 10, 10, 95, 1$altura_linha_separar);


		$this->pdf->SetFont ( 'Arial', '', 16 );
        $this->pdf->Cell($this->largura_pagina, 7, "", 0, 1, 'C');
        $this->pdf->Cell($this->largura_pagina, 7, "", 0, 1, 'C');
		$this->pdf->Cell($this->largura_pagina, 7, mb_convert_encoding("REQUERIMENTO DE MATRÍCULA", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
		
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($this->largura_pagina, $altura_linha_texto, mb_convert_encoding("Ilmo. Sr. Diretor", 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($this->largura_pagina, $altura_linha_texto, mb_convert_encoding("O (a) aluno (a) abaixo qualificado (a), por seu responsável, requer sua matrícula neste Colégio", 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
			
		
		$larg1 = 50;
		$larg2 = 50;
		$larg3 = 50;
		$larg4 = 50;
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("Curso:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding("Turma:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->Cell($larg3, $altura_linha_texto, mb_convert_encoding("Período:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->Cell($larg4, $altura_linha_texto, mb_convert_encoding("Ano letivo:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		//$turma_id = (int)util::GET('turma');
		//$turma = EASYNC5__si_turma::getByPK($turma_id);
		//$turma_id = (new SI_AlunoTurmaModel())->where('fk_aluno', $aluno_id)->first();
		$turma = (new SI_TurmaModel())->find($turma_id);

		

		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding(parametro::get_grau( $turma->id_grau), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding($turma->nome, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->Cell($larg3, $altura_linha_texto, mb_convert_encoding(parametro::get_periodo( $turma->id_periodo), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->Cell($larg4, $altura_linha_texto, mb_convert_encoding($turma->ano, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
	
		// quebra de linha:
		$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell(1, 6, "", 0, 0, 'L');
		
		
		$larg_nome = 100;
		$larg_mat = 30;
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_nome, $altura_linha_texto, mb_convert_encoding("Nome do(a) aluno(a):", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_mat, $altura_linha_texto, mb_convert_encoding("Nº de matrícula:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_nome, $altura_linha_texto, $this->cortar_texto(mb_convert_encoding($aluno->nome, 'ISO-8859-1', 'UTF-8'), $larg_nome), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_mat, $altura_linha_texto, mb_convert_encoding($aluno->matricula, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		// quebra de linha:
		$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell(1, 6, "", 0, 0, 'L');

		
		if(!empty($aluno->arquivo_foto)) {

			$foto_aluno = $aluno->arquivo_foto;

			$caminho_foto = base_url().'/assets/dist/img/arquivo_foto_aluno/' . $foto_aluno;
            //$caminho_foto = '../../arquivo_foto_aluno/' . $aluno->getArquivo_foto()->value();
            /*$localh = $_SERVER['SERVER_NAME'] == 'localhost';

            if($localh) {
                $caminho_foto = '../../arquivo_foto_aluno/example.jpg';
            }*/
			
			$im = imagecreatefromjpeg($caminho_foto);
			if(imagesx($im) != imagesy($im)) {
				$size = min(imagesx($im), imagesy($im));
				$im2 = self::mycrop($im, array('x' => 0, 'y' => 0, 'width' => $size, 'height' => $size));
				if ($im2 !== FALSE) {
					imagejpeg($im2, $caminho_foto, 96);
				}
			}
			
			$this->pdf->Image($caminho_foto, 150, 55, 35);
		}else{
			$caminho_foto = base_url().'/assets/dist/img/arquivo_foto_aluno/semfoto.jpeg';
		}
		
		
		
		
		
		$larg_nasc = 40;
		$larg_loc_nasc = 60;
		$larg_rg = 30;
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_nasc, $altura_linha_texto, mb_convert_encoding("Data de nascimento:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_loc_nasc, $altura_linha_texto, mb_convert_encoding("Local de Nascimento/Estado:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_rg, $altura_linha_texto, mb_convert_encoding("CPF do (a) Aluno (a):", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_nasc, $altura_linha_texto, mb_convert_encoding($aluno->nasc, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_loc_nasc, $altura_linha_texto, mb_convert_encoding($aluno->cid_nasc, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_rg, $altura_linha_texto, mb_convert_encoding($aluno->rg, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		
		// quebra de linha:
		$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell(1, 6, "", 0, 0, 'L');
		
		
		
		
		
		
		$larg_end = 140;
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_end, $altura_linha_texto, mb_convert_encoding("Endereço residencial", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_end, $altura_linha_texto, mb_convert_encoding($aluno->end, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		
		// quebra de linha:
		$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell(1, 6, "", 0, 0, 'L');
		
		
		
		
		
		$larg_bairro = 80;
		$larg_cidade_estado = 50;
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_bairro, $altura_linha_texto, mb_convert_encoding("Bairro:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_cidade_estado, $altura_linha_texto, mb_convert_encoding("Cidade/Estado:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_bairro, $altura_linha_texto, $this->cortar_texto(mb_convert_encoding($aluno->bairro, 'ISO-8859-1', 'UTF-8'), $larg_bairro), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_cidade_estado, $altura_linha_texto, mb_convert_encoding($aluno->cidade .'/'. $aluno->uf, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		// quebra de linha:
		$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell(1, 6, "", 0, 0, 'L');
		
		
		$larg_cep = 80;
		$larg_fone_residencial = 50;
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_cep, $altura_linha_texto, mb_convert_encoding("CEP:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_fone_residencial, $altura_linha_texto, mb_convert_encoding("Telefone Residencial:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		//$pai = $aluno->get_REFERENCE_Si_pai__USING_COLUMN__Fk_pai();
		$pai = (new SI_PaiModel())->find($aluno->fk_pai);
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		//$this->pdf->Cell($larg_cep, $altura_linha_texto, $this->cortar_texto(utf8_decode($cep), $larg_cep), 0, 0, 'L');
		$this->pdf->Cell($larg_cep, $altura_linha_texto, $this->cortar_texto(mb_convert_encoding($pai->rm_resp_financeiro_cep, 'ISO-8859-1', 'UTF-8'), $larg_cep), 0, 0, 'L');
		

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_fone_residencial, $altura_linha_texto, mb_convert_encoding($aluno->fone, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		// quebra de linha:
		$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell(1, 6, "", 0, 0, 'L');
		
		
		
		
		
		
		
		
		$larg_nome_pai = 140;
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_end, $altura_linha_texto, mb_convert_encoding("Nome do pai", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_end, $altura_linha_texto, mb_convert_encoding($pai->nome_pai, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		
		// quebra de linha:
		$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell(1, 6, "", 0, 0, 'L');
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		$larg_estado_civil = 80;
		$larg_nacionalid = 60;
		$larg_dt_nasc = 30;
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_estado_civil, $altura_linha_texto, mb_convert_encoding("Estado civil:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_loc_nasc, $altura_linha_texto, mb_convert_encoding("Nacionalidade:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_rg, $altura_linha_texto, mb_convert_encoding("Data de nascimento:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_estado_civil, $altura_linha_texto, mb_convert_encoding($pai->rm_pai_estado_civil, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_loc_nasc, $altura_linha_texto, mb_convert_encoding($pai->rm_pai_nacionalidade, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_rg, $altura_linha_texto, mb_convert_encoding($pai->nasc_pai, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		
		// quebra de linha:
		$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell(1, 6, "", 0, 0, 'L');
		
		
		
		
		
		
		
		
		$larg_profissao = 80;
		$larg_pai_rg = 60;
		$larg_pai_cpf = 30;
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_profissao, $altura_linha_texto, mb_convert_encoding("Profissão:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_pai_rg, $altura_linha_texto, mb_convert_encoding("RG:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_pai_cpf, $altura_linha_texto, mb_convert_encoding("CPF:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_profissao, $altura_linha_texto, mb_convert_encoding($pai->profissao_pai, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_pai_rg, $altura_linha_texto, mb_convert_encoding($pai->rg_pai, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_pai_cpf, $altura_linha_texto, mb_convert_encoding($pai->cpf_pai, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		
		
		// quebra de linha:
		$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell(1, 6, "", 0, 0, 'L');
		
		
		
		
		
		$larg_email = 80;
		$larg_fone_comercial = 60;
		$larg_fone_celular = 30;
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_email, $altura_linha_texto, mb_convert_encoding("Endereço Eletrônico (e-mail):", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_fone_comercial, $altura_linha_texto, mb_convert_encoding("Telefone comercial:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_fone_celular, $altura_linha_texto, mb_convert_encoding("Telefone celular:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_email, $altura_linha_texto, mb_convert_encoding($pai->email_pai, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_fone_comercial, $altura_linha_texto, mb_convert_encoding($pai->fone_pai, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_fone_celular, $altura_linha_texto, mb_convert_encoding($pai->cel_pai, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		
		// quebra de linha:
		$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell(1, 6, "", 0, 0, 'L');
		
		
		
		
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_end, $altura_linha_texto, mb_convert_encoding("Nome da mãe:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_end, $altura_linha_texto, mb_convert_encoding($pai->nome_mae, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		
		// quebra de linha:
		$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell(1, 6, "", 0, 0, 'L');
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_estado_civil, $altura_linha_texto, mb_convert_encoding("Estado civil:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_loc_nasc, $altura_linha_texto, mb_convert_encoding("Nacionalidade:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_rg, $altura_linha_texto, mb_convert_encoding("Data de nascimento:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_estado_civil, $altura_linha_texto, mb_convert_encoding($pai->rm_mae_estado_civil, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_loc_nasc, $altura_linha_texto, mb_convert_encoding($pai->rm_mae_nacionalidade, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_rg, $altura_linha_texto, mb_convert_encoding($pai->nasc_mae, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		
		// quebra de linha:
		$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell(1, 6, "", 0, 0, 'L');
		
		
		
		
		
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_profissao, $altura_linha_texto, mb_convert_encoding("Profissão:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_pai_rg, $altura_linha_texto, mb_convert_encoding("RG:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_pai_cpf, $altura_linha_texto, mb_convert_encoding("CPF:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_profissao, $altura_linha_texto, mb_convert_encoding($pai->profissao_mae, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_pai_rg, $altura_linha_texto, mb_convert_encoding($pai->rg_mae, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_pai_cpf, $altura_linha_texto, mb_convert_encoding($pai->cpf_mae, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		
		// quebra de linha:
		$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell(1, 6, "", 0, 0, 'L');
		
		
		
		
		
		// quebra de linha:
		$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell(1, 6, "", 0, 0, 'L');
		
		
		
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_email, $altura_linha_texto, mb_convert_encoding("Endereço Eletrônico (e-mail):", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_fone_comercial, $altura_linha_texto, mb_convert_encoding("Telefone comercial:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_fone_celular, $altura_linha_texto, mb_convert_encoding("Telefone celular:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_email, $altura_linha_texto, mb_convert_encoding($pai->email_mae, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_fone_comercial, $altura_linha_texto, mb_convert_encoding($pai->fone_mae, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_fone_celular, $altura_linha_texto, mb_convert_encoding($pai->cel_mae, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		
		
		
		
		// quebra de linha:
		$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell(1, 6, "", 0, 0, 'L');
		
		
		
		
		
		$larg1 = 80;
		$larg2 = 40;
		$larg3 = 40;
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("Responsável (na Ausência dos pais):", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding("Telefone:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg3, $altura_linha_texto, mb_convert_encoding("Grau de Parentesco:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding($pai->nome_resp, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding($pai->fone_resp, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg3, $altura_linha_texto, mb_convert_encoding($pai->rm_grau_parentesco_responsavel, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		
		
		
		
		
		
		
		// quebra de linha:
		$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell(1, 6, "", 0, 0, 'L');
		
		
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_end, $altura_linha_texto, mb_convert_encoding("Nome do responsável financeiro:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_end, $altura_linha_texto, mb_convert_encoding($pai->rm_resp_financeiro_nome, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		
		
		
		
		
		
		// quebra de linha:
		$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell(1, 6, "", 0, 0, 'L');
		
		
		$larg1 = 80;
		$larg2 = 40;
		$larg3 = 40;
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("RG:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding("CPF:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg3, $altura_linha_texto, mb_convert_encoding("Grau de Parentesco:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding($pai->rm_resp_financeiro_rg, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding($pai->rm_resp_financeiro_cpf, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg3, $altura_linha_texto, mb_convert_encoding($pai->rm_resp_financeiro_grau_parentesco, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		
		
		
		// quebra de linha:
		$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell(1, 6, "", 0, 0, 'L');
		
		
		$larg1 = 120;
		$larg2 = 80;
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("Endereço para correspondência Financeira  (Rua, Avenida, Quadra... ):", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding("Bairro:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding($pai->rm_resp_financeiro_endereco_correspondencia, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding($pai->rm_resp_financeiro_bairro, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		
		
		
		
		// quebra de linha:
		$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell(1, 6, "", 0, 0, 'L');
		
		
		$larg1 = 120;
		$larg2 = 80;
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("CEP:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding("Cidade/Estado:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding($pai->rm_resp_financeiro_cep, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding($pai->rm_resp_financeiro_cidade_estado, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		
		
		// quebra de linha:
		$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell(1, 6, "", 0, 0, 'L');
		
		
		
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("Observação:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		$this->pdf->Ln();
		
		$texto = mb_convert_encoding("Declaro estar de acordo com o regime escolar deste estabelecimento, bem como reconhecer o pagamento líquido mensal até o dia 05 do mês vigente.", 'ISO-8859-1', 'UTF-8');
		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->MultiCell(70, 4, $texto);
		
		
		$this->pdf->Ln();
		$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("Cuiabá, ______ de ________________ de _________", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->Ln();
		$this->pdf->Ln();
		$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("_______________________________________________", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("           ASSINATURA DO RESPONSÁVEL", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$px2 = $this->pdf->GetX() -5;
		$py2 = $this->pdf->GetY() - 14;
		$this->pdf->SetXY($px2, $py2);
		
		$larg1 = 60;
		$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("DEFERIMENTO", 'ISO-8859-1', 'UTF-8'), 0, 0, 'C');
		
		$this->pdf->SetXY($px2, $py2 + 10);
		$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("_______________________________________________", 'ISO-8859-1', 'UTF-8'), 0, 0, 'C');
		$this->pdf->SetXY($px2, $py2 + 14);
		$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("           ASSINATURA DO DIRETOR", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$px = 10;
		$larg_x = 180;

		$paddingY = 7;

		$py = 45;
		$py += $paddingY;
		$this->pdf->Line($px, $py, $px + $larg_x, $py);

		$py = 95;
        $py += $paddingY;
		$this->pdf->Line($px, $py, $px + $larg_x, $py);
		
		
		$py = 135;
        $py += $paddingY;
		$this->pdf->Line($px, $py, $px + $larg_x, $py);
		
		
		$py = 175;
        $py += $paddingY;
		$this->pdf->Line($px, $py, $px + $larg_x, $py);
		
		
		$py = 185;
        $py += $paddingY;
		$this->pdf->Line($px, $py, $px + $larg_x, $py);
		
		
		$py = 225;
        $py += $paddingY;
		$this->pdf->Line($px, $py, $px + $larg_x, $py);

		$this->pdf->Output('D', 'Requerimento_de_matricula.pdf');
	}

	public function adicionar_vetor_turma() {

		$turma_atual = $this->request->getVar('turmaAtual');
		$turma_id = $this->request->getVar('turmaId');

		
		$alunos = (new SI_AlunoTurmaModel())->where('fk_turma', $turma_atual)->findAll();

		foreach($alunos as $alunosItem){
			$aluno_id = $alunosItem->fk_aluno;
			
			$altura_linha_texto = 4;
			$altura_linha_separar = @$altura_linha_separar;
			
			$tamanho_fonte = 9;
			$aluno = (new SI_AlunoModel())->find($aluno_id);
			//$aluno = EASYNC5__si_aluno::getByPK($aluno_id);
			
		//		$caminho_imagem = 'visao/pagina/conteudo/relatorio/boletim/logo_boletim.jpg';
			

			
			$this->pdf->AddPage();

			//$this->pdf->Image($caminho_imagem, 10, 10, 95, 1$altura_linha_separar);


			$this->pdf->SetFont ( 'Arial', '', 16 );
			$this->pdf->Cell($this->largura_pagina, 7, "", 0, 1, 'C');
			$this->pdf->Cell($this->largura_pagina, 7, "", 0, 1, 'C');
			$this->pdf->Cell($this->largura_pagina, 7, mb_convert_encoding("REQUERIMENTO DE MATRÍCULA", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
			
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($this->largura_pagina, $altura_linha_texto, mb_convert_encoding("Ilmo. Sr. Diretor", 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($this->largura_pagina, $altura_linha_texto, mb_convert_encoding("O (a) aluno (a) abaixo qualificado (a), por seu responsável, requer sua matrícula neste Colégio", 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
				
			
			$larg1 = 50;
			$larg2 = 50;
			$larg3 = 50;
			$larg4 = 50;
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("Curso:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding("Turma:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->Cell($larg3, $altura_linha_texto, mb_convert_encoding("Período:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->Cell($larg4, $altura_linha_texto, mb_convert_encoding("Ano letivo:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			//$turma_id = (int)util::GET('turma');
			//$turma = EASYNC5__si_turma::getByPK($turma_id);
			//$turma_id = (new SI_AlunoTurmaModel())->where('fk_aluno', $aluno_id)->first();
			$turma = (new SI_TurmaModel())->find($turma_id);

			

			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding(parametro::get_grau( $turma->id_grau), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding($turma->nome, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->Cell($larg3, $altura_linha_texto, mb_convert_encoding(parametro::get_periodo( $turma->id_periodo), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->Cell($larg4, $altura_linha_texto, mb_convert_encoding($turma->ano, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
		
			// quebra de linha:
			$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
			$this->pdf->Ln();
			$this->pdf->Cell(1, 6, "", 0, 0, 'L');
			
			
			$larg_nome = 100;
			$larg_mat = 30;
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_nome, $altura_linha_texto, mb_convert_encoding("Nome do(a) aluno(a):", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_mat, $altura_linha_texto, mb_convert_encoding("Nº de matrícula:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_nome, $altura_linha_texto, $this->cortar_texto(mb_convert_encoding($aluno->nome, 'ISO-8859-1', 'UTF-8'), $larg_nome), 0, 0, 'L');

			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_mat, $altura_linha_texto, mb_convert_encoding($aluno->matricula, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			// quebra de linha:
			$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
			$this->pdf->Ln();
			$this->pdf->Cell(1, 6, "", 0, 0, 'L');

			
			if (!empty($aluno->arquivo_foto)) {

				$foto_aluno = $aluno->arquivo_foto;
				$caminho_foto = base_url() . '/assets/dist/img/arquivo_foto_aluno/' . $foto_aluno;
			
				// Verifica se a imagem existe usando getimagesize()
				if (@getimagesize($caminho_foto)) {
					// A imagem foi encontrada, tenta carregar
					$im = imagecreatefromjpeg($caminho_foto);
				} else {
					// A imagem não foi encontrada, define a foto padrão
					$caminho_foto = base_url() . '/assets/dist/img/arquivo_foto_aluno/semfoto.jpg';
					$im = imagecreatefromjpeg($caminho_foto);
				}
			
				// Verifica se a imagem é quadrada e faz o crop se necessário
				if (imagesx($im) != imagesy($im)) {
					$size = min(imagesx($im), imagesy($im));
					$im2 = self::mycrop($im, array('x' => 0, 'y' => 0, 'width' => $size, 'height' => $size));
					if ($im2 !== FALSE) {
						imagejpeg($im2, $caminho_foto, 96);
					}
				}
			
				// Adiciona a imagem ao PDF
				$this->pdf->Image($caminho_foto, 150, 55, 35);
			
			} else {
				// Se não houver arquivo de foto definido, usa a foto padrão
				$caminho_foto = base_url() . '/assets/dist/img/arquivo_foto_aluno/semfoto.jpg';
				$this->pdf->Image($caminho_foto, 150, 55, 35);
			}
			
			
			
			
			
			
			$larg_nasc = 40;
			$larg_loc_nasc = 60;
			$larg_rg = 30;
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_nasc, $altura_linha_texto, mb_convert_encoding("Data de nascimento:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_loc_nasc, $altura_linha_texto, mb_convert_encoding("Local de Nascimento/Estado:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_rg, $altura_linha_texto, mb_convert_encoding("CPF do (a) Aluno (a):", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_nasc, $altura_linha_texto, mb_convert_encoding($aluno->nasc, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_loc_nasc, $altura_linha_texto, mb_convert_encoding($aluno->cid_nasc, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_rg, $altura_linha_texto, mb_convert_encoding($aluno->rg, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			
			// quebra de linha:
			$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
			$this->pdf->Ln();
			$this->pdf->Cell(1, 6, "", 0, 0, 'L');
			
			
			
			
			
			
			$larg_end = 140;
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_end, $altura_linha_texto, mb_convert_encoding("Endereço residencial", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_end, $altura_linha_texto, mb_convert_encoding($aluno->end, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

			
			
			// quebra de linha:
			$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
			$this->pdf->Ln();
			$this->pdf->Cell(1, 6, "", 0, 0, 'L');
			
			
			
			
			
			$larg_bairro = 80;
			$larg_cidade_estado = 50;
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_bairro, $altura_linha_texto, mb_convert_encoding("Bairro:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_cidade_estado, $altura_linha_texto, mb_convert_encoding("Cidade/Estado:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_bairro, $altura_linha_texto, $this->cortar_texto(mb_convert_encoding($aluno->bairro, 'ISO-8859-1', 'UTF-8'), $larg_bairro), 0, 0, 'L');

			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_cidade_estado, $altura_linha_texto, mb_convert_encoding($aluno->cidade .'/'. $aluno->uf, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			// quebra de linha:
			$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
			$this->pdf->Ln();
			$this->pdf->Cell(1, 6, "", 0, 0, 'L');
			
			
			$larg_cep = 80;
			$larg_fone_residencial = 50;
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_cep, $altura_linha_texto, mb_convert_encoding("CEP:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_fone_residencial, $altura_linha_texto, mb_convert_encoding("Telefone Residencial:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			//$pai = $aluno->get_REFERENCE_Si_pai__USING_COLUMN__Fk_pai();
			$pai = (new SI_PaiModel())->find($aluno->fk_pai);
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			//$this->pdf->Cell($larg_cep, $altura_linha_texto, $this->cortar_texto(utf8_decode($cep), $larg_cep), 0, 0, 'L');
			$this->pdf->Cell($larg_cep, $altura_linha_texto, $this->cortar_texto(mb_convert_encoding($pai->rm_resp_financeiro_cep, 'ISO-8859-1', 'UTF-8'), $larg_cep), 0, 0, 'L');
			

			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_fone_residencial, $altura_linha_texto, mb_convert_encoding($aluno->fone, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			// quebra de linha:
			$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
			$this->pdf->Ln();
			$this->pdf->Cell(1, 6, "", 0, 0, 'L');
			
			
			
			
			
			
			
			
			$larg_nome_pai = 140;
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_end, $altura_linha_texto, mb_convert_encoding("Nome do pai", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_end, $altura_linha_texto, mb_convert_encoding($pai->nome_pai, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

			
			
			// quebra de linha:
			$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
			$this->pdf->Ln();
			$this->pdf->Cell(1, 6, "", 0, 0, 'L');
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			$larg_estado_civil = 80;
			$larg_nacionalid = 60;
			$larg_dt_nasc = 30;
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_estado_civil, $altura_linha_texto, mb_convert_encoding("Estado civil:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_loc_nasc, $altura_linha_texto, mb_convert_encoding("Nacionalidade:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_rg, $altura_linha_texto, mb_convert_encoding("Data de nascimento:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_estado_civil, $altura_linha_texto, mb_convert_encoding($pai->rm_pai_estado_civil, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_loc_nasc, $altura_linha_texto, mb_convert_encoding($pai->rm_pai_nacionalidade, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_rg, $altura_linha_texto, mb_convert_encoding($pai->nasc_pai, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			
			// quebra de linha:
			$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
			$this->pdf->Ln();
			$this->pdf->Cell(1, 6, "", 0, 0, 'L');
			
			
			
			
			
			
			
			
			$larg_profissao = 80;
			$larg_pai_rg = 60;
			$larg_pai_cpf = 30;
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_profissao, $altura_linha_texto, mb_convert_encoding("Profissão:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_pai_rg, $altura_linha_texto, mb_convert_encoding("RG:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_pai_cpf, $altura_linha_texto, mb_convert_encoding("CPF:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_profissao, $altura_linha_texto, mb_convert_encoding($pai->profissao_pai, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_pai_rg, $altura_linha_texto, mb_convert_encoding($pai->rg_pai, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_pai_cpf, $altura_linha_texto, mb_convert_encoding($pai->cpf_pai, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			
			
			// quebra de linha:
			$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
			$this->pdf->Ln();
			$this->pdf->Cell(1, 6, "", 0, 0, 'L');
			
			
			
			
			
			$larg_email = 80;
			$larg_fone_comercial = 60;
			$larg_fone_celular = 30;
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_email, $altura_linha_texto, mb_convert_encoding("Endereço Eletrônico (e-mail):", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_fone_comercial, $altura_linha_texto, mb_convert_encoding("Telefone comercial:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_fone_celular, $altura_linha_texto, mb_convert_encoding("Telefone celular:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_email, $altura_linha_texto, mb_convert_encoding($pai->email_pai, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_fone_comercial, $altura_linha_texto, mb_convert_encoding($pai->fone_pai, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_fone_celular, $altura_linha_texto, mb_convert_encoding($pai->cel_pai, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			
			// quebra de linha:
			$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
			$this->pdf->Ln();
			$this->pdf->Cell(1, 6, "", 0, 0, 'L');
			
			
			
			
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_end, $altura_linha_texto, mb_convert_encoding("Nome da mãe:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_end, $altura_linha_texto, mb_convert_encoding($pai->nome_mae, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

			
			
			// quebra de linha:
			$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
			$this->pdf->Ln();
			$this->pdf->Cell(1, 6, "", 0, 0, 'L');
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_estado_civil, $altura_linha_texto, mb_convert_encoding("Estado civil:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_loc_nasc, $altura_linha_texto, mb_convert_encoding("Nacionalidade:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_rg, $altura_linha_texto, mb_convert_encoding("Data de nascimento:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_estado_civil, $altura_linha_texto, mb_convert_encoding($pai->rm_mae_estado_civil, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_loc_nasc, $altura_linha_texto, mb_convert_encoding($pai->rm_mae_nacionalidade, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_rg, $altura_linha_texto, mb_convert_encoding($pai->nasc_mae, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			
			// quebra de linha:
			$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
			$this->pdf->Ln();
			$this->pdf->Cell(1, 6, "", 0, 0, 'L');
			
			
			
			
			
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_profissao, $altura_linha_texto, mb_convert_encoding("Profissão:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_pai_rg, $altura_linha_texto, mb_convert_encoding("RG:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_pai_cpf, $altura_linha_texto, mb_convert_encoding("CPF:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_profissao, $altura_linha_texto, mb_convert_encoding($pai->profissao_mae, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_pai_rg, $altura_linha_texto, mb_convert_encoding($pai->rg_mae, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_pai_cpf, $altura_linha_texto, mb_convert_encoding($pai->cpf_mae, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			
			// quebra de linha:
			$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
			$this->pdf->Ln();
			$this->pdf->Cell(1, 6, "", 0, 0, 'L');
			
			
			
			
			
			// quebra de linha:
			$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
			$this->pdf->Ln();
			$this->pdf->Cell(1, 6, "", 0, 0, 'L');
			
			
			
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_email, $altura_linha_texto, mb_convert_encoding("Endereço Eletrônico (e-mail):", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_fone_comercial, $altura_linha_texto, mb_convert_encoding("Telefone comercial:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_fone_celular, $altura_linha_texto, mb_convert_encoding("Telefone celular:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_email, $altura_linha_texto, mb_convert_encoding($pai->email_mae, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_fone_comercial, $altura_linha_texto, mb_convert_encoding($pai->fone_mae, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_fone_celular, $altura_linha_texto, mb_convert_encoding($pai->cel_mae, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			
			
			
			
			// quebra de linha:
			$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
			$this->pdf->Ln();
			$this->pdf->Cell(1, 6, "", 0, 0, 'L');
			
			
			
			
			
			$larg1 = 80;
			$larg2 = 40;
			$larg3 = 40;
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("Responsável (na Ausência dos pais):", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding("Telefone:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg3, $altura_linha_texto, mb_convert_encoding("Grau de Parentesco:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding($pai->nome_resp, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding($pai->fone_resp, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg3, $altura_linha_texto, mb_convert_encoding($pai->rm_grau_parentesco_responsavel, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			
			
			
			
			
			
			
			// quebra de linha:
			$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
			$this->pdf->Ln();
			$this->pdf->Cell(1, 6, "", 0, 0, 'L');
			
			
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg_end, $altura_linha_texto, mb_convert_encoding("Nome do responsável financeiro:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg_end, $altura_linha_texto, mb_convert_encoding($pai->rm_resp_financeiro_nome, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

			
			
			
			
			
			
			
			// quebra de linha:
			$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
			$this->pdf->Ln();
			$this->pdf->Cell(1, 6, "", 0, 0, 'L');
			
			
			$larg1 = 80;
			$larg2 = 40;
			$larg3 = 40;
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("RG:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding("CPF:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg3, $altura_linha_texto, mb_convert_encoding("Grau de Parentesco:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding($pai->rm_resp_financeiro_rg, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding($pai->rm_resp_financeiro_cpf, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg3, $altura_linha_texto, mb_convert_encoding($pai->rm_resp_financeiro_grau_parentesco, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			
			
			
			// quebra de linha:
			$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
			$this->pdf->Ln();
			$this->pdf->Cell(1, 6, "", 0, 0, 'L');
			
			
			$larg1 = 120;
			$larg2 = 80;
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("Endereço para correspondência Financeira  (Rua, Avenida, Quadra... ):", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding("Bairro:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding($pai->rm_resp_financeiro_endereco_correspondencia, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding($pai->rm_resp_financeiro_bairro, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			
			
			
			
			// quebra de linha:
			$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
			$this->pdf->Ln();
			$this->pdf->Cell(1, 6, "", 0, 0, 'L');
			
			
			$larg1 = 120;
			$larg2 = 80;
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("CEP:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding("Cidade/Estado:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding($pai->rm_resp_financeiro_cep, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding($pai->rm_resp_financeiro_cidade_estado, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			
			
			// quebra de linha:
			$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
			$this->pdf->Ln();
			$this->pdf->Cell(1, 6, "", 0, 0, 'L');
			
			
			
			
			
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
			$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("Observação:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			$this->pdf->Ln();
			
			$texto = mb_convert_encoding("Declaro estar de acordo com o regime escolar deste estabelecimento, bem como reconhecer o pagamento líquido mensal até o dia 05 do mês vigente.", 'ISO-8859-1', 'UTF-8');
			
			$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
			$this->pdf->MultiCell(70, 4, $texto);
			
			
			$this->pdf->Ln();
			$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("Cuiabá, ______ de ________________ de _________", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			$this->pdf->Ln();
			$this->pdf->Ln();
			$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("_______________________________________________", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			$this->pdf->Ln();
			$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("           ASSINATURA DO RESPONSÁVEL", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$px2 = $this->pdf->GetX() -5;
			$py2 = $this->pdf->GetY() - 14;
			$this->pdf->SetXY($px2, $py2);
			
			$larg1 = 60;
			$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("DEFERIMENTO", 'ISO-8859-1', 'UTF-8'), 0, 0, 'C');
			
			$this->pdf->SetXY($px2, $py2 + 10);
			$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("_______________________________________________", 'ISO-8859-1', 'UTF-8'), 0, 0, 'C');
			$this->pdf->SetXY($px2, $py2 + 14);
			$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding("           ASSINATURA DO DIRETOR", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$px = 10;
			$larg_x = 180;

			$paddingY = 7;

			$py = 45;
			$py += $paddingY;
			$this->pdf->Line($px, $py, $px + $larg_x, $py);

			$py = 95;
			$py += $paddingY;
			$this->pdf->Line($px, $py, $px + $larg_x, $py);
			
			
			$py = 135;
			$py += $paddingY;
			$this->pdf->Line($px, $py, $px + $larg_x, $py);
			
			
			$py = 175;
			$py += $paddingY;
			$this->pdf->Line($px, $py, $px + $larg_x, $py);
			
			
			$py = 185;
			$py += $paddingY;
			$this->pdf->Line($px, $py, $px + $larg_x, $py);
			
			
			$py = 225;
			$py += $paddingY;
			$this->pdf->Line($px, $py, $px + $larg_x, $py);
		}
		
		$this->pdf->Output('D', 'Requerimento_de_matricula_turma.pdf');
	}
	
	public function render() 
	{
		$this->pdf->Output();
	}


	private function cortar_texto($texto, $largura_maxima = 100) {
	
		if($this->pdf->GetStringWidth($texto) <= $largura_maxima) {
			return $texto;
		}
	
		$ret = " (...)";
		$largura_maxima -= $this->pdf->GetStringWidth($ret);
		while(true) {
			if($this->pdf->GetStringWidth($texto) > $largura_maxima) {
				$texto = substr($texto, 0, strlen($texto) - 1);
			}else{
				return $texto . $ret;
			}
		}
	}

	public static function mycrop($src, array $rect)
    {
    	$dest = imagecreatetruecolor($rect['width'], $rect['height']);
    	imagecopy(
    			$dest,
    			$src,
    			0,
    			0,
    			$rect['x'],
    			$rect['y'],
    			$rect['width'],
    			$rect['height']
    			);
    
    	return $dest;
    }
	
}

?>
