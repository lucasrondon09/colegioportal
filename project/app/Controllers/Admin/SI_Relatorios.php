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
use TCPDF as GlobalTCPDF;
use Mpdf\Mpdf;


//require_once APPPATH . 'Libraries/tcpdf/tcpdf.php';



class SI_Relatorios extends Controller
{
	public function teste_pdf(){
		echo "teste"; die;
	}

	public function gerar_contrato_aluno_pdf_($turma_id, $aluno_id){

		$mpdf = new Mpdf();
		$mpdf->WriteHTML('<h1>Olá, mundo!</h1>');
		$mpdf->Output('teste.pdf', 'I');

	}

	public function gerar_contrato_aluno_pdf($turma_id, $aluno_id)
	{
		$alunoModel = new \App\Models\Admin\SI_AlunoModel();
		$turmaModel = new \App\Models\Admin\SI_TurmaModel();

		$aluno = $alunoModel->find($aluno_id);
		$turma = $turmaModel->find($turma_id);

		$data = [
			'aluno' => $aluno,
			'turma' => $turma
		];

		$html = view('admin/SI_Relatorios/contrato_aluno.php', $data);

		$mpdf = new \Mpdf\Mpdf([
			'margin_top' => 35 // margem superior para o cabeçalho
		]);

		// Adiciona imagem de cabeçalho
		$headerImg = base_url().'/assets/admin/dist/img/TOPO2.jpg';
		$mpdf->SetHTMLHeader('<div style="text-align:center;"><img src="' . $headerImg . '" style="width:100%;"></div>');

		$mpdf->WriteHTML($html);
		$mpdf->Output('contrato_aluno.pdf', 'I');
		exit;
	}

	public function pdf(){

		$pdf = new TCPDF();
		//var_dump($pdf);
		/*
		$pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Lucas Rondon');
        $pdf->SetTitle('Relatório Exemplo');
        $pdf->SetSubject('Exemplo com TCPDF');
        $pdf->SetKeywords('CodeIgniter 4, TCPDF, Relatório');

        // Remova cabeçalhos automáticos
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Defina margens
        $pdf->SetMargins(15, 15, 15);

        // Adicione uma página
        $pdf->AddPage();

        // Defina fonte
        $pdf->SetFont('helvetica', '', 12);

        // Adicione conteúdo ao PDF
        $html = '<h1>Relatório Exemplo</h1>
                 <p>Este é um exemplo básico de geração de PDF usando TCPDF no CodeIgniter 4. Teste</p>';
        $pdf->writeHTML($html, true, false, true, false, '');
		//$filePath = WRITEPATH.'relatorio_exemplo.pdf';*/
		$filePath = 'relatorio_exemplo.pdf';
        // Exiba o PDF no navegador
        $pdf->Output($filePath, 'I');

		//return $this->response->download($filePath, null)->setFileName('relatorio_exemplo.pdf');


	}

	public $data;

	//Relatório - listas
	public function listas(){

		helper('form');


		$ano = (new SI_ParametroModel())->getAnoLetivo();
		$turmas = (new SI_TurmaModel())->where('ano', $ano)->where('status', 1)->orderBy('nome', 'asc')->findAll();

		$this->data = 	[
						'ano' => $ano,
						'turmas' => $turmas
						];

		echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Relatorios/listas.php', $this->data);
        echo view('admin/template/footer.php');


	}

	public function listas_send(){

		$ano 		= $this->request->getPost('ano');
		$turma_id 		= $this->request->getPost('turma');
		$relatorio_id 	= $this->request->getPost('relatorios');

		switch ($relatorio_id) {
			case 'telefone':
				$this->listas_telefone($turma_id, $ano);
				break;
			case 'chamada':
				$this->listas_chamada($turma_id, $ano);
				break;
			case 'tarefa':
				$this->listas_tarefas($turma_id, $ano);
				break;
			case 'classe':
				$disciplina_id 	= $this->request->getPost('disciplina');
				$this->listas_classe($turma_id, $disciplina_id);
				break;
			case 'carometro':
				$etapa 	= $this->request->getPost('etapa');
				$this->listas_carometro($turma_id, $etapa, $ano);
				break;
			
			default:		
				
				break;
		}

		die;

	}

	public function buscaTurmas(){

		$ano = $this->request->getPost('ano');

		$turmas = (new SI_TurmaModel())->where('ano', $ano)->where('status', 1)->orderBy('nome', 'asc')->findAll();
		$printTurma = '<option value="-1" selected="">Todas</option>';
		foreach ($turmas as $turmasItem):
			$printTurma .= '<option value="'.$turmasItem->id.'"> '.$turmasItem->nome.'</option>';
		endforeach;

		echo  $printTurma;


	}

	public function listas_carometro($turma_id, $etapa_id, $ano){

		$niveis_etapa = "'i1', 'i2', 'i3'";
        if($etapa_id == '2') {
            $niveis_etapa = "'1a', '2a', '3a', '4a', '5a'";
        }
        if($etapa_id == '3') {
            $niveis_etapa = "'6a', '7a', '8a', '9a'";
        }

		$q = "
			select concat(t.nome, ' - ', t.ano) turma, a.nome, a.arquivo_foto, t.id turma_id
			from si_aluno a
			join si_aluno_turma at on at.fk_aluno = a.id
			join si_turma t on t.id = at.fk_turma
			join si_nivel n on n.id_nivel = t.id_nivel
			where 
				$turma_id != -1 AND at.fk_turma = $turma_id
				OR $etapa_id != -1 AND t.ano = $ano AND t.status = 1 AND n.id_nivel IN ($niveis_etapa)
			order by n.ordem, t.ano, t.nome, a.nome";

			$r = (new SI_AlunoModel())->query($q)->getResultArray();
			
				
			$lista = new relatorio_lista_carometro($turma_id);
			$lista->render($r);
	}

	public function listas_classe($turma_id, $disciplina_id){

		$montar = new relatorio_diario_montar();
		$montar->render($turma_id, $disciplina_id);
		//$montar->render(195, 'p');
	}

	public function listas_telefone($turma_id, $ano){


		$turma = 'and at.fk_turma = '.$turma_id;

		if((int)$turma_id === -1){
			$turmas = (new SI_TurmaModel())->where('ano', $ano)->where('status', 1)->orderBy('nome', 'asc')->findAll();
			$idTurmas = '';
			foreach($turmas as $turmaItem){

				$idTurmas .= !empty($idTurmas) ? ', '.$turmaItem->id : $turmaItem->id;
				
			}

			$turma = 'and at.fk_turma in ('.$idTurmas.')';
		}

		$q = "
			select concat(t.nome, ' - ', t.ano) turma, t.id turma_id, t.ano, a.nome, a.matricula, a.nasc, a.fone
			from si_aluno a
			join si_aluno_turma at on at.fk_aluno = a.id
			join si_turma t on t.id = at.fk_turma
			join si_nivel n on n.id_nivel = t.id_nivel
			where 
				$turma_id != -1 $turma
				OR $turma_id = -1 AND t.ano = $ano AND t.status = 1
			order by n.ordem, t.ano, t.nome, a.nome";

			
			$r = (new SI_AlunoModel())->query($q)->getResultArray();
			
			//$r = $this->conn->qcv($q, "turma,nome,matricula,nasc,fone,turma_id");


			$lista = new relatorio_lista_telefone();
			$lista->render($r, $turma_id);


	}

	public function listas_tarefas($turma_id, $ano){

		$turma_rel = $turma_id;

		$montar = new relatorio_tarefas_montar($turma_id);
			
			if($turma_id == '-1') {
				$turma_id = -'1';
			}
			$montar->render($turma_rel, true, $turma_id, $ano);


	}

	public function listas_chamada($turma_id, $ano){

		$turma = 'and at.fk_turma = '.$turma_id;

		if((int)$turma_id === -1){
			$turmas = (new SI_TurmaModel())->where('ano', $ano)->where('status', 1)->orderBy('nome', 'asc')->findAll();
			$idTurmas = '';
			foreach($turmas as $turmaItem){

				$idTurmas .= !empty($idTurmas) ? ', '.$turmaItem->id : $turmaItem->id;
				
			}

			$turma = 'and at.fk_turma in ('.$idTurmas.')';
		}

		$q = "
			select concat(t.nome, ' - ', t.ano) turma, a.nome, a.matricula, t.id turma_id
			from si_aluno a
			join si_aluno_turma at on at.fk_aluno = a.id
			join si_turma t on t.id = at.fk_turma
			join si_nivel n on n.id_nivel = t.id_nivel
			where 
				$turma_id != -1 $turma
				OR $turma_id = -1 AND t.ano = $ano AND t.status = 1
			order by n.ordem, t.ano, t.nome, a.nome";

			$r = (new SI_AlunoModel())->query($q)->getResultArray();
				
			//echo $q;
			//exit();
			$montar = new relatorio_lista_chamada();
			$montar->render($r, $turma_id);
	}

	//Relatório de Pais
    public function pais() {

		$pdf = new relatorio_pai_pdf();

        $paiModel = new SI_PaiModel();
	
		$result = $paiModel->query("
                                SELECT mat_pai, nome_pai, nome_mae, nome_resp 
                                FROM si_pai 
                                WHERE status = 1 
                                ORDER BY mat_pai
                                ")->getResult();
		$pais = array();
		
		foreach ($result as $resultItem) {
			array_push($pais, array(
				"nome_pai" => $resultItem->nome_pai,
				"nome_mae" => $resultItem->nome_mae,
				"nome_resp" => $resultItem->nome_resp, 
				"matricula" => $resultItem->mat_pai
			));
		}
		
		
		
		
		$pdf->gerar_lista($pais);
		
	}


	//Relatorio de Alunos
	public function relatorio_aluno_montar() {
	
		$pdf = new relatorio_aluno_pdf();
		

		//$c = EASYNC5__model_conn::get_conn();

		$alunoModel = new SI_AlunoModel();
		
		$result = $alunoModel->query("
									SELECT matricula, nome 
									FROM si_aluno 
									WHERE status = 1 
									ORDER BY matricula
									")->getResult();
		
		/*
		$q = "
				SELECT matricula, nome 
				FROM si_aluno 
				WHERE status = 1 
				ORDER BY matricula 
				
				";*/

		//$r = $c->qcv($q, "matricula,nome");
		
		$alunos = array();
		
		foreach ($result as $resultItem) {
			array_push($alunos, array("nome" => $resultItem->nome, "matricula" => $resultItem->matricula));
		}
		
		$pdf->gerar_lista($alunos);
	}

	public function relatorio_aluno_matricula_montar() {
	
		$pdf = new relatorio_aluno_matricula_pdf();
		
		$ano = (new SI_ParametroModel())->getAnoLetivo();

		$alunoModel = new SI_AlunoModel();
		
		$result = $alunoModel->query("
										SELECT 		
										a.nome aluno_nome,
										a.matricula aluno_matricula,
										a.nasc aluno_nasc,
										a.fone aluno_fone,
										a.end aluno_end,
										t.nome aluno_turma,
										p.nome_pai,
										p.nome_mae,
										p.nome_resp,
										p.cel_pai,
										p.fone_pai,
										p.cel_mae,
										p.fone_mae,
										p.cel_resp,
										p.fone_resp
										
										FROM si_aluno a
										
										JOIN si_aluno_turma at
										ON a.id = at.fk_aluno
										
										JOIN si_turma t
										ON at.fk_turma = t.id
										
										JOIN si_pai p
										ON a.fk_pai = p.id
										
										JOIN si_nivel n
										ON n.id_nivel = t.id_nivel

										WHERE 
										
										t.status = 1
										AND a.status = 1
										AND t.ano = $ano
										
										ORDER BY n.ordem, t.nome, a.nome
									")->getResult();

		// aluno_nome,aluno_matricula,aluno_nasc,aluno_fone,aluno_end,aluno_turma,nome_pai,nome_mae,nome_resp
		
		$alunos = $result;

		$pdf->gerar_lista($ano, $alunos);
	}

	//Relatório Média da Turma por Matéria
	public function media_turma(){

		helper('form');


		$ano = (new SI_ParametroModel())->getAnoLetivo();
		$turmas = (new SI_TurmaModel())->where('ano', $ano)->where('status', 1)->orderBy('nome', 'asc')->findAll();

		$this->data = 	[
						'ano' => $ano,
						'turmas' => $turmas
						];

		echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Relatorios/media_turma.php', $this->data);
        echo view('admin/template/footer.php');


	}

	public function relatorio_media_materia(){

		$turma = $this->request->getPost('turma');

		
		$relatorio_media_materia = (new relatorio_media_nucleo_materia_montar)->render($turma);

	}

	//Relatório Média dos Alunos por Matéria
	public function media_alunos(){

		helper('form');


		$ano = (new SI_ParametroModel())->getAnoLetivo();
		$turmas = (new SI_TurmaModel())->where('ano', $ano)->where('status', 1)->orderBy('nome', 'asc')->findAll();

		$this->data = 	[
						'ano' => $ano,
						'turmas' => $turmas
						];

		echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Relatorios/media_alunos.php', $this->data);
        echo view('admin/template/footer.php');


	}

	public function relatorio_media_alunos(){

		$turma = $this->request->getPost('turma');

		$relatorio_media_aluno_montar_turma = (new relatorio_media_aluno_montar_turma)->render($turma);

	}


	//Relatório Média Individual - Núcleo Comum
	public function media_individual_nucleo_comum(){

		helper('form');


		$ano = (new SI_ParametroModel())->getAnoLetivo();
		$turmas = (new SI_TurmaModel())->where('ano', $ano)->where('status', 1)->orderBy('nome', 'asc')->findAll();

		$this->data = 	[
						'ano' => $ano,
						'turmas' => $turmas
						];

		echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Relatorios/media_individual_nucleo_comum.php', $this->data);
        echo view('admin/template/footer.php');


	}

	//Relatório Média Individual - Núcleo Comum
	public function media_individual_nucleo_comum_send(){

		$turma = $this->request->getPost('turma');

		$visao_pagina_conteudo_relatorio__media_individual__nucleo_comum__montar = (new visao_pagina_conteudo_relatorio__media_individual__nucleo_comum__montar)->render($turma);

	}

	//Relatório Média Individual - Todas as Disciplinas
	public function media_individual_todas_disciplinas(){

		helper('form');


		$ano = (new SI_ParametroModel())->getAnoLetivo();
		$turmas = (new SI_TurmaModel())->where('ano', $ano)->where('status', 1)->orderBy('nome', 'asc')->findAll();

		$this->data = 	[
						'ano' => $ano,
						'turmas' => $turmas
						];

		echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Relatorios/media_individual_todas_disciplinas.php', $this->data);
        echo view('admin/template/footer.php');


	}

	//Relatório Média Individual - Todas as Disciplinas
	public function media_individual_todas_disciplinas_send(){

		$turma = $this->request->getPost('turma');

		$visao_pagina_conteudo_relatorio__media_individual__todas_disciplinas__montar = (new visao_pagina_conteudo_relatorio__media_individual__todas_disciplinas__montar)->render($turma);

	}

	//Relatório Boletim
	public function boletim(){

		helper('form');


		$ano = (new SI_ParametroModel())->getAnoLetivo();
		$turmas = (new SI_TurmaModel())->where('ano', $ano)->where('status', 1)->orderBy('nome', 'asc')->findAll();

		$this->data = 	[
						'ano' => $ano,
						'turmas' => $turmas
						];

		echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Relatorios/boletim.php', $this->data);
        echo view('admin/template/footer.php');


	}

	//Relatório Boletim
	public function boletim_turma(){

		helper('form');


		$ano = $this->request->getGet('ano');
        $turmaId = $this->request->getGet('turma');
        $periodo = $this->request->getGet('periodo');
        $search = $this->request->getGet('search');

		//dd($turmaId);
		switch($periodo){
			case 'a':
				$periodoNome = 'Anual';
				break;
			case 1:
				$periodoNome = '1º Trimestre';
				break;	
			case 2:
				$periodoNome = '2º Trimestre';
				break;	
			case 3:
				$periodoNome = '3º Trimestre';
				break;	
		}


		

		if(!empty($search)){

			$alunos = (new SI_AlunoTurmaModel())->select('si_aluno.id, si_aluno.nome, si_aluno.matricula')
											->where('fk_turma', $turmaId)
											->like('si_aluno.nome', $search)
											->join('si_aluno', 'si_aluno.id = si_aluno_turma.fk_aluno')
											->orderBy('si_aluno.nome', 'asc')
											->findAll();

		}else{
			
			$alunos = (new SI_AlunoTurmaModel())->select('si_aluno.id, si_aluno.nome, si_aluno.matricula')
												->where('fk_turma', $turmaId)
												->join('si_aluno', 'si_aluno.id = si_aluno_turma.fk_aluno')
												->orderBy('si_aluno.nome', 'asc')
												->findAll();	
		}


		$turma = (new SI_TurmaModel())->find($turmaId);

		$this->data = 	[
						'turma' 		=> $turma,
						'alunos'		=> $alunos,
						'periodo'		=> $periodo,
						'periodoNome'	=> $periodoNome,
						'ano'			=> $ano		
						];

		echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Relatorios/boletim_turma.php', $this->data);
        echo view('admin/template/footer.php');


	}

	public function gerar_boletim_turma($turma_id, $aluno_id = null){

		$monta_pdf_boletim_turma = (new monta_pdf_boletim_turma)->render($turma_id, $aluno_id);
	}

	public function gerar_boletim_turma_ficha($turma_id, $aluno_id){


		$monta_pdf_ficha_individual = (new monta_pdf_ficha_individual)->render($turma_id, $aluno_id);
	}


	public function notas_trimestrais($turma_id, $aluno_id, $periodo){

		helper('form');


		if(session()->sistema == 'sispai'){
			$userId = session()->userId;

			$aluno = (new SI_AlunoModel())->where('fk_pai', $userId)
										  ->where('id', $aluno_id)
										  ->first();

			if(!$aluno){
				return redirect()->back();
			}
		}

		switch($periodo){
			case 'a':
				$periodoNome = 'Anual';
				break;
			case 1:
				$periodoNome = '1º Trimestre';
				break;	
			case 2:
				$periodoNome = '2º Trimestre';
				break;	
			case 3:
				$periodoNome = '3º Trimestre';
				break;	
		}

		$this->data = 	[
						'turma' 		=> (new SI_TurmaModel())->find($turma_id),
						'aluno'			=> (new SI_AlunoModel())->find($aluno_id),
						'periodo'		=> $periodo,
						'periodoNome'	=> $periodoNome,
						'tabela' 		=> (new notas_trimestrais)->boletim_trimestral($aluno_id, $turma_id, $periodo)
						];

						//$tabela = (new notas_trimestrais)->boletim_trimestral($aluno_id, $turma_id, $periodo);



		echo view('admin/template/header.php');
		echo view('admin/template/sidebar.php');
		echo view('admin/SI_Relatorios/notas_trimestrais.php', $this->data);
		echo view('admin/template/footer.php');

								

	}

}

class CarometroPDF extends  tcpdf {

	//Page header
    function Header()
    {
        // To be implemented in your own inherited class
        $caminho_imagem = base_url().'/assets/admin/dist/img/TOPO2.jpg';

        $quadrado = 25;
        $this->Image($caminho_imagem, 6, 4, 198, 15);
    }
    function Footer()
    {
        $this->SetY(-35);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');

        // To be implemented in your own inherited class
		$caminho_imagem = base_url().'/assets/admin/dist/img/RODAPE2.jpg';

        $quadrado = 25;
        //$this->Image($caminho_imagem, 6, 286, 198, 3);
    }

}



class relatorio_lista_carometro {


	var $gx = 35; // posição X inicial do gráfico, relativo a borda esquerda da folha;
	var $gy = 70; // posição Y inicial do gráfico, relativo a borda superior da folha;

	var $gw = 160; // largura do gráfico;
	var $gh = 80;  // altura do gráfico;
	var $pdf;

	var $estilo1t = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => '2,5', 'color' => array(0, 0, 164));
	var $estilo2t = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => '9,5', 'color' => array(0, 0, 164));
	var $estilo3t = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 164));


	public function render($alunos) {
		// "turma,nome,matricula,nasc,fone"
		$turma = $alunos[0]['turma'];

		// create new PDF document
		$this->pdf = new CarometroPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);



		// set document information
		$this->pdf->SetCreator(PDF_CREATOR);
		$this->pdf->SetAuthor('Lucas Rondon');
		$this->pdf->SetTitle('Relatório - Colégio Portal');
		$this->pdf->SetSubject('Relatório');
		$this->pdf->SetKeywords('Relatório');

		// set default header data
		$this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
		$this->pdf->setFooterData(array(0,64,0), array(0,64,128));

		// set header and footer fonts
		$this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$this->pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		// set default font subsetting mode
		$this->pdf->setFontSubsetting(true);

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$this->pdf->SetFont('dejavusans', '', 10, '', true);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$this->pdf->AddPage();

		
		/*

		// todas as turmas:
		$alunos = array(
			[0] => 
				array(
					[0] => turma,
					[0] => nome,
					[0] => arquivo_foto,
					[0] => turma_id
				)
		);
		
		
		
		//  separados:
		$alunos_separados = array(
			[0] => 
				array(
					[0] => turma,
					[0] => nome,
					[0] => arquivo_foto,
					[0] => turma_id
				)
		);
		
		

		*/
		
		$separados = array();
		$atemp = array();
		$id_primeira_turma = 0;
		$turma_id_atual = 0;
		
		foreach($alunos as $v) {
			if($turma_id_atual == 0) {
				$turma_id_atual = $v['turma_id'];
				array_push($atemp, $v);
				//echo $turma_id_atual;
			} else {
				if($v['turma_id'] != $turma_id_atual) {
					array_push($separados, $atemp);
					
					$atemp = array();
					$turma_id_atual = $v['turma_id'];
					if($id_primeira_turma == 0) {
						$id_primeira_turma = $turma_id_atual;
					}
				}
				array_push($atemp, $v);
			}
		}
		array_push($separados, $atemp);
		/*
		echo '<pre>';
		print_r($separados);
		*/
		
		// Set some content to print
		$html = '<table><tr><td align="center" style="font-size:12pt;font-weight:bold">Carômetro</td></tr></table>';
		$this->pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
			
		
		$primeira_turma = true;
		foreach ($separados as $alunosSUB) {
			if($primeira_turma) {
				$primeira_turma = false;
			}else{
				//if($alunosSUB[3] != $id_primeira_turma) {
					$this->pdf->AddPage();
				//}
			}
			//echo $alunosSUB[$k][0];
			//exit(0);
			$html = '<table><tr><td align="center" style="font-size:10pt;">'.$alunosSUB[0]['turma'].'</td></tr></table>';
	
	
	
			// Print text using writeHTMLCell()
			$this->pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
	
	
			$this->pdf->MultiCell(1,1, '', 0, 'L', 0, 1,
					30,
					45,
					true);
	
			$this->pdf->SetFont('dejavusans', '', 11, '', true);
			
			
			
			$html = '<table border="0" cellspacing="0" cellpadding="02" style="border:1px solid #ccc">
						';
			
	
			$jaAdicionaouPagina = false;		
			$total = sizeof($alunosSUB);
			
			// turma,nome,arquivo_foto,turma_id
			$i=0;
			$k=0;
			for($i=0; true; $i++) {
			
				$html .= '<TR>';
				
				$caminho = '';
				for($j = $i; $j < $i + 4; $j++) {
					if($k < $total) {
	
						/*
						$caminho = '';
						if($alunosSUB[$k]['arquivo_foto'] . '' != '') {
							$caminho = base_url().'/assets/dist/img/arquivo_foto_aluno/' . $alunosSUB[$k]['arquivo_foto'];

							if($_SERVER['HTTP_HOST'] == 'localhost') {
                                $caminho = base_url().'/assets/dist/img/arquivo_foto_aluno/example.jpg';
                            }
							list($width, $height) = getimagesize($caminho);
						}*/

						$caminho = '';
						if($alunosSUB[$k]['arquivo_foto'] . '' != '') {
							
							$imgExiste = FCPATH . '/assets/dist/img/arquivo_foto_aluno/' . $alunosSUB[$k]['arquivo_foto'];

							if (!file_exists($imgExiste)) {
								// Se a imagem não existir ou o nome estiver vazio, usa "semfoto.jpg"
								$caminho = base_url() . '/assets/dist/img/arquivo_foto_aluno/' . 'semfoto.jpg';
							}else{
								$caminho = base_url() . '/assets/dist/img/arquivo_foto_aluno/' . ($alunosSUB[$k]['arquivo_foto'] ?? '');
							}


							if ($_SERVER['HTTP_HOST'] == 'localhost') {
								$caminho = base_url() . '/assets/dist/img/arquivo_foto_aluno/semfoto.jpg';
							}
						}

						if (!empty($alunosSUB[$k]['arquivo_foto']) && @getimagesize($caminho)) {
							list($width, $height) = getimagesize($caminho);
						} else {
							// Caminho da imagem não encontrado ou imagem inválida
							$width = $height = null; // Define valores padrão ou ações alternativas
						}


						$vetor = $this->scale_image($caminho);
						$w = !empty($vetor) ? $vetor[0] : 0;
						$h = !empty($vetor) ? $vetor[1] : 0;
							
						
						$html .='
								<td width="150"  align="center" style="border:1px solid #ccc">
								';
						
						if($caminho != '') {
							$html .= '<img src="'.$caminho.'"  width="'.$w.'" height="'.$h.'">';
						}else{
							$caminho = base_url().'/assets/dist/img/arquivo_foto_aluno/semfoto.jpg';
							$html .= '<img src="'.$caminho.'"  width="'.$w.'" height="'.$h.'">';
						}
					$html .= '<br />' . $alunosSUB[$k]['nome'] . '</td>';
					}else{
						$html .= '
									<td>&nbsp;</td>';
					}
					$k++;
				}
			
				$html .= '
								</TR>';
				if($k >= $total) break;
				
				if(($i + 1) % 3 == 0) {
	
					// encerrar este bloco desta página, pois só pode ter 3 linhas de fotos por página.
					$html.= '</table>';
					
	
					// output the HTML content
					if($jaAdicionaouPagina) {
						$this->pdf->AddPage();
					echo 'Arddd';
					}else{
						$jaAdicionaouPagina = true;
					}
					$this->pdf->writeHTML($html, true, false, true, false, '');
					
					
					
					$html = '<table border="0" cellspacing="0" cellpadding="02" style="border:1px solid #ccc">
						';
						
				}
			}
			$html .= '
						</table>';
			
			if($jaAdicionaouPagina) {
				$this->pdf->AddPage();
			}
			
			$this->pdf->writeHTML($html, true, false, true, false, '');
	
			
		}
		
		
		
		
		// ---------------------------------------------------------

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		//header('Content-Type: application/pdf');
		
		if(sizeof($separados) == 1) {
			$this->pdf->Output('carometro.pdf', 'D');
		}else{
			$this->pdf->Output('carometro.pdf', 'D');
		}
		//$pdf->Output("filename.pdf",'FD');
	}

	private function scale_image($src_image) {
		$src_image = @imagecreatefromjpeg($src_image);

		// Verifique se a imagem foi carregada corretamente
		if ($src_image === false) {
			return null;
		}

		$src_width = imagesx($src_image);
		$src_height = imagesy($src_image);

		$dst_width =145;
		$dst_height = $dst_width;

		// Try to match destination image by width
		$new_width = $dst_width;
		$new_height = round($new_width*($src_height/$src_width));
		$new_x = 0;
		$new_y = round(($dst_height-$new_height)/2);

		// FILL and FIT mode are mutually exclusive

		$next = $new_height > $dst_height;

		// If match by width failed and destination image does not fit, try by height
		if ($next) {
			$new_height = $dst_height;
			$new_width = round($new_height*($src_width/$src_height));
			$new_x = round(($dst_width - $new_width)/2);
			$new_y = 0;
		}

		// Copy image on right place
		return array($new_width, $new_height);
		//imagecopyresampled($dst_image, $src_image , $new_x, $new_y, 0, 0, $new_width, $new_height, $src_width, $src_height);
	}

}

class relatorio_diario_montar{
	
	public function render($pturma, $pdisciplina) {

		$pdf = new relatorio_diario_pdf();
		
		
		$turma_id = $pturma;
		$disciplina_id = $pdisciplina;
		
		$q = "
				SELECT a.id aluno
				FROM si_aluno_turma at
				
				JOIN si_aluno a
				ON at.fk_aluno = a.id
				
				WHERE a.status = 1
				AND at.fk_turma = $turma_id
				
				ORDER BY a.nome
				
				";
		
		//echo $q;
		$r = (new SI_AlunoTurmaModel())->query($q)->getResultArray();
		
		
		if($r != null) {
			
			$nota = new nota();
			
			$disciplinas = parametro::disciplinas();
			$vetor_media_da_turma = array();
			for($trimestre=1; $trimestre<=3; $trimestre++) {
				foreach ($disciplinas as $disciplina => $nome_disc) {
					$media = $nota->get_media_turma($turma_id, $disciplina, $trimestre);
					
					$vetor_media_da_turma[$trimestre . "t"][$disciplina] = $media;
				}
			}
			
			
			$vetor_alunos = array();
			foreach ($r as $v) {
				$aluno_id = $v['aluno'];
				
				
				$vetor_aluno = array();
				
				$vetor_notas = $nota->boletim_anual_aluno($aluno_id, $turma_id, $vetor_media_da_turma);
				// extrair apenas a nota da disciplina selecionada no filtro:
				
				$vetor_aluno["nome"] = $vetor_notas["nome"];
				$vetor_aluno["matricula"] = $vetor_notas["matricula"];
				$vetor_aluno["notas_disciplina"] = $vetor_notas["notas"];
				
				// buscar as notas da disciplina:

				if($disciplina_id != -1){
					$achou_disciplina = false;
					foreach ($vetor_notas["notas"] as $notas_busca) {
						if($notas_busca["cod_disciplina"] == $disciplina_id) {
							// achou a disciplina.
							$vetor_aluno["notas_disciplina"] = $notas_busca;
							$achou_disciplina = true;
							break;
						}
					}
					if(!$achou_disciplina) {
						echo "ERRO: Disciplina NAO encontrada (" . $disciplina_id . ")";
						exit();
					}
				}
				
				array_push($vetor_alunos, $vetor_aluno);
			}
			
			$turma = (new SI_TurmaModel())->find($turma_id);
			
			$vetor_info = array();
			$vetor_info["turma"] = $turma->nome;
			$vetor_info["grau"] = parametro::get_grau($turma->id_grau);
			$vetor_info["periodo"] = parametro::get_periodo( $turma->id_periodo);
			$vetor_info["ano"] = $turma->ano;
			$vetor_info["disciplina"] = $disciplina_id != -1 ? parametro::get_disciplina($disciplina_id) : $disciplina_id;
			
			
			$pdf->set_vetores($vetor_info, $vetor_alunos);
			
			//print_r($vetor_alunos);
			
			
		}else{
			echo 'Não existem alunos para esta turma.';
		}
	}
}


class relatorio_diario_pdf {

	private $largura_matricula = 19;
	private $largura_pagina = 190;
	private $largura_nome = 100;
	private $largura_faltas = 150;
	private $pdf;



	public function __construct() {
		$this->pdf = new FPDF();
	}

	public function set_vetores($vetor_info, $vetor_alunos) {
	
		//print_r($vetor_alunos);
		
		$turma = $vetor_info["turma"];
		$grau = $vetor_info["grau"];
		$disciplina = $vetor_info["disciplina"];
		$ano = $vetor_info["ano"];
		$periodo = $vetor_info["periodo"];

		if((int)$disciplina === -1){

			// Vamos primeiro pegar todas as disciplinas
			$disciplinas = [];
			foreach ($vetor_alunos as $aluno) {
				foreach ($aluno['notas_disciplina'] as $disciplina) {
					if (!in_array($disciplina['disciplina'], $disciplinas)) {
						$disciplinas[] = $disciplina['disciplina'];
					}
				}
			}

			// Agora percorremos cada disciplina e filtramos os alunos por essa disciplina
			foreach ($disciplinas as $disciplinaDesc) {
				$alunos_filtrados = [];
				
				foreach ($vetor_alunos as $aluno) {
					foreach ($aluno['notas_disciplina'] as $disciplina) {
						if ($disciplina['disciplina'] === $disciplinaDesc) {
							$aluno_filtrado = [
								'nome' => $aluno['nome'],
								'matricula' => $aluno['matricula'],
								'notas_disciplina' => $disciplina
							];
							$alunos_filtrados[] = $aluno_filtrado;
							break; // Podemos parar de procurar outras disciplinas para este aluno
						}
					}
				}

				$this->diarioClassePage($turma, $grau, $disciplinaDesc, $ano, $periodo, $alunos_filtrados);
			}			

			//dd($vetor_alunos);


			

			/*
			foreach($vetor_alunos as $notasAluno){

				$disciplinaAluno = $notasAluno['notas_disciplina'];

			}
dd($vetor_alunos);
			dd($disciplinaAluno);

				foreach($disciplinaAluno as $disciplinaDesc){
					
					

					$this->diarioClassePage($turma, $grau, $disciplinaDesc, $ano, $periodo, $vetor_alunos);

				}*/


		}else{
			
			$this->diarioClassePage($turma, $grau, $disciplina, $ano, $periodo, $vetor_alunos);
		}
				
		
		
		
		$this->pdf->Output('Diario_de_classe.pdf', 'D');
	}

	private function diarioClassePage($turma, $grau, $disciplina, $ano, $periodo, $vetor_alunos){
		
		$caminho_imagem = base_url().'/assets/admin/dist/img/logo_boletim.jpg';
		
		$this->pdf->AddPage();

		$this->pdf->Image($caminho_imagem, 10, 10, 95, 12);

		$this->pdf->Cell($this->largura_pagina, 5, '', 0, 1, 'C');	
		$this->pdf->Ln();
		$this->pdf->Ln();	
		$this->pdf->Ln();	
		$this->pdf->SetFont ( 'Arial', '', 16 );

		$this->pdf->Cell($this->largura_pagina, 7, mb_convert_encoding("DIÁRIO DE CLASSE", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
		
		$this->pdf->SetFont ( 'Arial', 'B', 13 );
		$this->pdf->Cell($this->largura_pagina, 7, mb_convert_encoding("$turma,  $grau,  $periodo  $ano", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
		
		$this->pdf->SetFont ( 'Arial', 'BU', 13 );
		$this->pdf->Cell($this->largura_pagina, 7, mb_convert_encoding("$disciplina", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
		
		
		
		$this->pdf->SetFont ( 'Arial', '', 8 );
		$this->pdf->Ln();
		$this->gerar_alunos($vetor_alunos);
		
		

		$this->pdf->Ln();
		$this->pdf->Ln();

		$this->pdf->SetFont ( 'Arial', '', 10 );

		$this->pdf->Cell(200, 10, mb_convert_encoding("1º Trim. Aulas Previstas: ______ Aulas Dadas: ______    ______________________      ____________________", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		$this->pdf->Cell(200, 6, "", 0, 0);
		$this->pdf->Ln();
		$this->pdf->Cell(200, 10, mb_convert_encoding(str_repeat(" ", 105) . "Professor(a)                       Coordenador(a)", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');


		$this->pdf->Ln();
		$this->pdf->Cell(200, 10, mb_convert_encoding("2º Trim. Aulas Previstas: ______ Aulas Dadas: ______    ______________________      ____________________", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		$this->pdf->Cell(200, 6, "", 0, 0);
		$this->pdf->Ln();
		$this->pdf->Cell(200, 10, mb_convert_encoding(str_repeat(" ", 105) . "Professor(a)                       Coordenador(a)", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		


		$this->pdf->Ln();
		$this->pdf->Cell(200, 10, mb_convert_encoding("3º Trim. Aulas Previstas: ______ Aulas Dadas: ______    ______________________      ____________________", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		$this->pdf->Cell(200, 6, "", 0, 0);
		$this->pdf->Ln();
		$this->pdf->Cell(200, 10, mb_convert_encoding(str_repeat(" ", 105) . "Professor(a)                       Coordenador(a)", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		


		$this->pdf->Ln();
		$this->pdf->Cell(200, 10, mb_convert_encoding("Recup.  Aulas Previstas: ______ Aulas Dadas: ______    ______________________      ____________________", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		$this->pdf->Cell(200, 6, "", 0, 0);
		$this->pdf->Ln();
		$this->pdf->Cell(200, 10, mb_convert_encoding(str_repeat(" ", 105) . "Professor(a)                       Coordenador(a)", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		

	}
	
	
	private function gerar_alunos($vetor_alunos) {
		
		

		$largura_matricula = 14;
		$largura_nome = 100;
		$largura_notatrim = 12;
		$largura_falta = 7;
		
		$altura_linha = 5;
		
		$this->pdf->SetFont ( 'Arial', 'B', 8 );
		
		$this->pdf->Cell($largura_matricula, $altura_linha, mb_convert_encoding("Matrícula", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
		$this->pdf->Cell($largura_nome, $altura_linha, mb_convert_encoding("Nome", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
		$this->pdf->Cell($largura_notatrim, $altura_linha, mb_convert_encoding("1º Trim", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
		$this->pdf->Cell($largura_falta, $altura_linha, mb_convert_encoding("FT", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
		
		$this->pdf->Cell($largura_notatrim, $altura_linha, mb_convert_encoding("2º Trim", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
		$this->pdf->Cell($largura_falta, $altura_linha, mb_convert_encoding("FT", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
		
		$this->pdf->Cell($largura_notatrim, $altura_linha, mb_convert_encoding("3º Trim", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
		$this->pdf->Cell($largura_falta, $altura_linha, mb_convert_encoding("FT", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
		
		$this->pdf->Cell($largura_notatrim, $altura_linha, mb_convert_encoding("Rec", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
		
		
			$this->pdf->Ln();
		
			
		foreach ($vetor_alunos as $aluno) {
			
			$this->pdf->SetFont ( 'Arial', '', 8 );
			$this->pdf->Cell($largura_matricula, $altura_linha, mb_convert_encoding($aluno["matricula"], 'ISO-8859-1', 'UTF-8'), 1, 0, 'L');
			$this->pdf->Cell($largura_nome, $altura_linha, mb_convert_encoding($this->cortar_texto($aluno["nome"], $largura_nome), 'ISO-8859-1', 'UTF-8'), 1, 0, 'L');
			
		
			$this->pdf->SetFont ( 'Arial', '', 10 );	
			$this->pdf->Cell($largura_notatrim, $altura_linha, mb_convert_encoding($aluno["notas_disciplina"]["1t"], 'ISO-8859-1', 'UTF-8'), 1, 0, 'R');
			$this->pdf->Cell($largura_falta, $altura_linha, mb_convert_encoding($aluno["notas_disciplina"]["1tf"], 'ISO-8859-1', 'UTF-8'), 1, 0, 'R');
			
			$this->pdf->Cell($largura_notatrim, $altura_linha, mb_convert_encoding($aluno["notas_disciplina"]["2t"], 'ISO-8859-1', 'UTF-8'), 1, 0, 'R');
			$this->pdf->Cell($largura_falta, $altura_linha, mb_convert_encoding($aluno["notas_disciplina"]["2tf"], 'ISO-8859-1', 'UTF-8'), 1, 0, 'R');
			
			$this->pdf->Cell($largura_notatrim, $altura_linha, mb_convert_encoding($aluno["notas_disciplina"]["3t"], 'ISO-8859-1', 'UTF-8'), 1, 0, 'R');
			$this->pdf->Cell($largura_falta, $altura_linha, mb_convert_encoding($aluno["notas_disciplina"]["3tf"], 'ISO-8859-1', 'UTF-8'), 1, 0, 'R');
			
			$this->pdf->Cell($largura_notatrim, $altura_linha, mb_convert_encoding($aluno["notas_disciplina"]["rec"], 'ISO-8859-1', 'UTF-8'), 1, 0, 'R');
			
			
		
		
			$this->pdf->Ln();
		}
		
		
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
	
}

class FPDF_Rotate extends  fpdf{
	
	var $angle=0;
	
	function Rotate($angle,$x=-1,$y=-1)
	{
		if($x==-1)
			$x=$this->x;
		if($y==-1)
			$y=$this->y;
		if($this->angle!=0)
			$this->_out('Q');
		$this->angle=$angle;
		if($angle!=0)
		{
			$angle*=M_PI/180;
			$c=cos($angle);
			$s=sin($angle);
			$cx=$x*$this->k;
			$cy=($this->h-$y)*$this->k;
			$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
		}
	}

	function RotatedText($x,$y,$txt,$angle)
	{
		//Text rotated around its origin
		$this->Rotate($angle,$x,$y);
		$this->Text($x,$y,$txt);
		$this->Rotate(0);
	}
	
	
	function _endpage()
	{
		if($this->angle!=0)
		{
			$this->angle=0;
			$this->_out('Q');
		}
		parent::_endpage();
	}
}

class relatorio_tarefas_pdf {

	private $largura_matricula = 19;
	private $largura_pagina = 190;
	private $largura_nome = 100;
	private $largura_faltas = 150;
	private $pdf;



	public function __construct() {
		$this->pdf = new FPDF_Rotate();
	}

	public function set_vetor_aluno($vetor_alunos) {
	/*


			$vetor_aluno["nome"] = $v[1];
			$vetor_aluno["matricula"] = $v[2];
			$vetor_aluno["turma"] = $v[3];
			$vetor_aluno["turma_id"] = $v[4];
			
			*/
		$caminho_imagem = base_url().'/assets/admin/dist/img/logo_boletim.jpg';
	
		$turma_atual = 0;
		
		$sub_vetores = array();
		$temp = array();
		for($i=0; $i<sizeof($vetor_alunos); $i++) {
			$nome = $vetor_alunos[$i]["nome"];
			$turma = $vetor_alunos[$i]["turma"];
			$turma_id = $vetor_alunos[$i]["turma_id"];
			
			if($turma_id != $turma_atual) {
				if($turma_atual != 0) {
					array_push($sub_vetores, $temp);
				}
				$turma_atual = $turma_id; 
				$temp = array();
			}
			array_push($temp, $vetor_alunos[$i]);
		}
		array_push($sub_vetores, $temp);
		/*
		echo '<pre>';
		print_r($sub_vetores);
		exit();
		*/
		
		
		foreach($sub_vetores as $v) {
			$caminho_imagem = base_url().'/assets/admin/dist/img/logo_boletim.jpg';
			
			$this->pdf->AddPage();
			
			$this->pdf->Image($caminho_imagem, 10, 10, 95, 12);
			
			
			$this->pdf->SetFont ( 'Arial', '', 8 );
			$this->pdf->Cell($this->largura_pagina, 7, "", 0, 1, 'C');
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', '', 16 );
			$this->pdf->Cell($this->largura_pagina, 7, mb_convert_encoding("REGISTRO DE TAREFAS  " , 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
			
			$x_antigo = $this->pdf->GetX();
			$y_antigo = $this->pdf->GetY();
			
			$this->pdf->SetXY(20, 60);
			$this->pdf->Cell(30, 7, mb_convert_encoding($v[0]["turma"], 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
			
			$this->pdf->SetXY($x_antigo, $y_antigo);
			$this->pdf->SetFont('Arial', '', 8);
			
			$this->pdf->Ln();
			
			$this->pdf->Cell(15, 7, "", 0, 0, 'L');
			$this->pdf->Cell(40, 7, "", 0, 0, 'L');
			
			
			
			for($i=1; $i<=25; $i++) {
				$this->pdf->Cell(5, 60, "", 1, 0, 'L');
			}
			
			$this->pdf->Ln();
			
			
			$this->pdf->Cell(15, 5, mb_convert_encoding("Data", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
			$this->pdf->Cell(40, 5, mb_convert_encoding("Descrição da Tarefa", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
			
			
			for($i=1; $i<=25; $i++) {
				$this->pdf->Cell(5, 5, "", 1, 0, 'L');
			}
			
			
			$i=1;
			foreach ($v as $aluno) {
				$this->pdf->RotatedText(63.5 + $i * 5, $this->pdf->GetY() - 2, $this->cortar_texto(mb_convert_encoding($aluno["nome"], 'ISO-8859-1', 'UTF-8'), 58), 90);
				$i++;
			}
			
			$altura_tarefa = 8;
			
			for($j=0; $j<12; $j++) {
				$this->pdf->Ln();
				$this->pdf->Cell(15, $altura_tarefa, "", 1, 0, 'L');
				$this->pdf->Cell(40, $altura_tarefa, "", 1, 0, 'L');
			
			
				for($i=1; $i<=25; $i++) {
					$this->pdf->Cell(5, $altura_tarefa, "", 1, 0, 'L');
				}
			}
			$this->pdf->Cell(5, 5, "", 0, 0, 'L');
			$this->pdf->Ln();
			$this->pdf->Ln();
			
			$this->pdf->SetFont ( 'Arial', '', 10 );
			$this->pdf->Cell(80, 7, mb_convert_encoding("Legenda:    C - Completa      I - Incompleta       NF - Não fez", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			$this->pdf->Ln();
			
			$this->pdf->Ln();
			$this->pdf->Cell(80, 7, mb_convert_encoding("Disciplina: _______________            Trimestre: _______________", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			$this->pdf->Ln();
			
			$this->pdf->Cell(80, 7, mb_convert_encoding("Professor(a): ______________________________________________________________________", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			
			$this->pdf->Ln();
			$this->pdf->Ln();
			$this->pdf->Cell(15, 7, mb_convert_encoding("Observações: ", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			
			for($i=0; $i<4; $i++) {
				$this->pdf->Ln();
				$this->pdf->Cell(15, 7, mb_convert_encoding("_________________________________________________________________________________", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
			}
			
			
		}

		$this->pdf->Output('Tarefas.pdf', 'D');
	}

	public function set_vetores($turma, $ano, $disciplina, $vetor_alunos) {

		$this->disciplina = $disciplina;
		$caminho_imagem = base_url().'/assets/admin/dist/img/logo_boletim.jpg';
		$turmaRow = '';
		$iniciouTurma = false;
	
		foreach ($vetor_alunos as $aluno) {
			$turma = $aluno['turma'];
	
			if ($turma != $turmaRow) {
				if ($iniciouTurma) {
					// Parte Final
					$this->parteFinal();
				}
	
				$this->pdf->AddPage();
				$turmaRow = $turma;
				$iniciouTurma = true;
	
				$this->pdf->Image($caminho_imagem, 10, 10, 95, 12);
	
				$this->pdf->SetFont('Arial', '', 8);
				$this->pdf->Cell($this->largura_pagina, 7, "", 0, 1, 'C');
				$this->pdf->Ln();
				$this->pdf->SetFont('Arial', '', 16);
				$this->pdf->Cell($this->largura_pagina, 7, mb_convert_encoding("REGISTRO DE TAREFAS  " . $ano, 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
	
				$x_antigo = $this->pdf->GetX();
				$y_antigo = $this->pdf->GetY();
	
				$this->pdf->SetXY(20, 60);
				$this->pdf->Cell(30, 7, mb_convert_encoding($turma, 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
	
				$this->pdf->SetXY($x_antigo, $y_antigo);
				$this->pdf->SetFont('Arial', '', 8);
	
				$this->pdf->Ln();
	
				$this->pdf->Cell(15, 7, "", 0, 0, 'L');
				$this->pdf->Cell(40, 7, "", 0, 0, 'L');
	
				for ($i = 1; $i <= 25; $i++) {
					$this->pdf->Cell(5, 60, "", 1, 0, 'L');
				}
	
				$this->pdf->Ln();
	
				$this->pdf->Cell(15, 5, mb_convert_encoding("Data", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
				$this->pdf->Cell(40, 5, mb_convert_encoding("Descrição da Tarefa", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
	
				for ($i = 1; $i <= 25; $i++) {
					$this->pdf->Cell(5, 5, "", 1, 0, 'L');
				}
	
				$i = 1;
			}
	
			$this->pdf->RotatedText(63.5 + $i * 5, $this->pdf->GetY() - 2, $this->cortar_texto(mb_convert_encoding($aluno["nome"], 'ISO-8859-1', 'UTF-8'), 58), 90);
			$i++;
		}
	
		if ($iniciouTurma) {
			// Parte Final
			$this->parteFinal();
		}
	
		$this->pdf->Output('Tarefas.pdf', 'D');
	}
	
	private function parteFinal() {
		$altura_tarefa = 8;
	
		for ($j = 0; $j < 12; $j++) {
			$this->pdf->Ln();
			$this->pdf->Cell(15, $altura_tarefa, "", 1, 0, 'L');
			$this->pdf->Cell(40, $altura_tarefa, "", 1, 0, 'L');
	
			for ($i = 1; $i <= 25; $i++) {
				$this->pdf->Cell(5, $altura_tarefa, "", 1, 0, 'L');
			}
		}
		$this->pdf->Cell(5, 5, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Ln();
	
		$this->pdf->SetFont('Arial', '', 10);
		$this->pdf->Cell(80, 7, mb_convert_encoding("Legenda:    C - Completa      I - Incompleta       NF - Não fez", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		$this->pdf->Ln();
	
		$this->pdf->Ln();
		$this->pdf->Cell(80, 7, mb_convert_encoding("Disciplina: " . $this->disciplina . "            Trimestre: _______________", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		$this->pdf->Ln();
	
		$this->pdf->Cell(80, 7, mb_convert_encoding("Professor(a): ______________________________________________________________________", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
	
		$this->pdf->Ln();
		$this->pdf->Ln();
		$this->pdf->Cell(15, 7, mb_convert_encoding("Observações: ", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
	
		for ($i = 0; $i < 4; $i++) {
			$this->pdf->Ln();
			$this->pdf->Cell(15, 7, mb_convert_encoding("_________________________________________________________________________________", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		}
	}
	
	
	
	private function gerar_alunos($vetor_alunos) {
		
		$largura_matricula = 14;
		$largura_nome = 100;
		$largura_notatrim = 12;
		$largura_falta = 7;
		
		$altura_linha = 5;
		
		$this->pdf->SetFont ( 'Arial', 'B', 8 );
		
		$this->pdf->Cell($largura_matricula, $altura_linha, mb_convert_encoding("Matrícula", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
		$this->pdf->Cell($largura_nome, $altura_linha, mb_convert_encoding("Nome", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
		$this->pdf->Cell($largura_notatrim, $altura_linha, mb_convert_encoding("1º Trim", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
		$this->pdf->Cell($largura_falta, $altura_linha, mb_convert_encoding("FT", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
		
		$this->pdf->Cell($largura_notatrim, $altura_linha, mb_convert_encoding("2º Trim", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
		$this->pdf->Cell($largura_falta, $altura_linha, mb_convert_encoding("FT", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
		
		$this->pdf->Cell($largura_notatrim, $altura_linha, mb_convert_encoding("3º Trim", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
		$this->pdf->Cell($largura_falta, $altura_linha, mb_convert_encoding("FT", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
		
		$this->pdf->Cell($largura_notatrim, $altura_linha, mb_convert_encoding("Rec", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
		
		
			$this->pdf->Ln();
		
			
		foreach ($vetor_alunos as $aluno) {
			
			$this->pdf->SetFont ( 'Arial', '', 8 );
			$this->pdf->Cell($largura_matricula, $altura_linha, mb_convert_encoding($aluno["matricula"], 'ISO-8859-1', 'UTF-8'), 1, 0, 'L');
			$this->pdf->Cell($largura_nome, $altura_linha, mb_convert_encoding($this->cortar_texto($aluno["nome"], $largura_nome), 'ISO-8859-1', 'UTF-8'), 1, 0, 'L');
			
		
			$this->pdf->SetFont ( 'Arial', '', 10 );	
			$this->pdf->Cell($largura_notatrim, $altura_linha, mb_convert_encoding($aluno["notas_disciplina"]["1t"], 'ISO-8859-1', 'UTF-8'), 1, 0, 'R');
			$this->pdf->Cell($largura_falta, $altura_linha, mb_convert_encoding($aluno["notas_disciplina"]["1tf"], 'ISO-8859-1', 'UTF-8'), 1, 0, 'R');
			
			$this->pdf->Cell($largura_notatrim, $altura_linha, mb_convert_encoding($aluno["notas_disciplina"]["2t"], 'ISO-8859-1', 'UTF-8'), 1, 0, 'R');
			$this->pdf->Cell($largura_falta, $altura_linha, mb_convert_encoding($aluno["notas_disciplina"]["2tf"], 'ISO-8859-1', 'UTF-8'), 1, 0, 'R');
			
			$this->pdf->Cell($largura_notatrim, $altura_linha, mb_convert_encoding($aluno["notas_disciplina"]["3t"], 'ISO-8859-1', 'UTF-8'), 1, 0, 'R');
			$this->pdf->Cell($largura_falta, $altura_linha, mb_convert_encoding($aluno["notas_disciplina"]["3tf"], 'ISO-8859-1', 'UTF-8'), 1, 0, 'R');
			
			$this->pdf->Cell($largura_notatrim, $altura_linha, mb_convert_encoding($aluno["notas_disciplina"]["rec"], 'ISO-8859-1', 'UTF-8'), 1, 0, 'R');
			
			
		
		
			$this->pdf->Ln();
		}
		
		
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
	
}

class relatorio_tarefas_montar{
	
	
	public function render($turma_rel, $ignorar_disciplina, $turma_sobrescrever = -1, $ano = '1990') {



		$pdf = new relatorio_tarefas_pdf();

		$turma_id = $turma_sobrescrever != -1 ? $turma_sobrescrever : $turma_rel;
		$disciplina_id = '';

		$turma = 'and at.fk_turma = '.$turma_id;

		if((int)$turma_id === -1){
			$turmas = (new SI_TurmaModel())->where('ano', $ano)->where('status', 1)->orderBy('nome', 'asc')->findAll();
			$idTurmas = '';
			foreach($turmas as $turmaItem){

				$idTurmas .= !empty($idTurmas) ? ', '.$turmaItem->id : $turmaItem->id;
				
			}

			$turma = 'and at.fk_turma in ('.$idTurmas.')';
		}
		
		
		//$c = EASYNC__model_conn::get_conn();
		$q = "
		SELECT a.id aluno_id, a.nome, a.matricula, t.nome turma, t.id turma_id
		FROM si_aluno_turma at
		
		JOIN si_aluno a
		ON at.fk_aluno = a.id
		
		JOIN si_turma t
		ON at.fk_turma = t.id
		
		JOIN si_nivel n
		ON n.id_nivel = t.id_nivel
		
		where 
				a.status = 1
				AND (
						$turma_id <> -1 $turma
						OR $turma_id = -1 AND t.ano = $ano AND t.status = 1
					)
		
		ORDER BY n.ordem, t.ano, t.nome, a.nome
		
		";


		
		$r = (new SI_AlunoTurmaModel())->query($q)->getResultArray();

		
		//$r = $c->qcv($q, "aluno_id,nome,matricula,turma,turma_id");
		
		
		if($r != null) {
			
			$vetor_alunos = array();
			foreach ($r as $v) {
		
			$vetor_aluno = array();
		
			
			
		
			$vetor_aluno["nome"] = $v['nome'];
			$vetor_aluno["matricula"] = $v['matricula'];
			$vetor_aluno["turma"] = $v['turma'];
			$vetor_aluno["turma_id"] = $v['turma_id'];
			
			
		
			array_push($vetor_alunos, $vetor_aluno);
		}

		$turma = (new SI_TurmaModel())->find($vetor_aluno["turma_id"]);
			
		//$turma = EASYNC__si_turma::getByPK($vetor_aluno["turma_id"]);
		
		if($ignorar_disciplina) {
			$disciplina = "_____________________________ ";
		}else{
			$disciplina = parametro::get_disciplina($disciplina_id);
		}
		
		if($turma_id == -2) {
			$pdf->set_vetor_aluno($vetor_alunos);
		}else{
			$pdf->set_vetores($turma->nome, $turma->ano, $disciplina, $vetor_alunos);
		}
			
		//print_r($vetor_alunos);
			
			
		}else{
		echo 'Não existem alunos para esta turma.';
		}
		
		
		
		//$pdf->set_vetores();
		
		
	}
}

class ChamadaPDF extends  tcpdf {

	//Page header
	public function Header() {
		// Logo
		
		$image_file = base_url().'/assets/admin/dist/img/logo_boletim.jpg';


		$this->SetMargins(PDF_MARGIN_LEFT, 30, PDF_MARGIN_RIGHT);

		$html = '<table cellpadding="1" cellspacing="10" border="0" style="text-align:center;">
				<tr><td>&nbsp;</td></tr>
	<tr style="text-align:left;"><td><img src="'.base_url().'/assets/admin/dist/img/logo_boletim.jpg" border="0" height="31" width="0" align="top" /></td></tr>
	</table>';

			// output the HTML content
			$this->writeHTML($html, true, false, true, false, '');

		}

		// Page footer
		public function Footer() {
			// Position at 15 mm from bottom
			$this->SetY(-35);
			// Set font
			$this->SetFont('helvetica', 'I', 8);
			// Page number
			$this->Cell(0, 10, 'Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');

			$x = 250;
			$y = 177;
			$w = 30;
			$h = 30;
			//$this->Image(base_url().'/assets/admin/dist/img/SELO.jpg', $x, $y, $w, $h, 'JPG', '', '', false, 300, '', false, false, 0, true, false, false);
			
			
			
			/*
			$html = '<table cellpadding="1" cellspacing="10" border="0" style="text-align:center;">
					<tr><td>&nbsp;</td></tr>
	<tr style="text-align:left;"><td><img src="visao/pagina/conteudo/relatorio/boletim/SELO.jpg" border="0" height="25" width="25" align="top" /></td></tr>
	</table>';
		
		// output the HTML content
		$this->writeHTML($html, true, false, true, false, '');
		*/
		
	}
}

class relatorio_lista_chamada {


	var $gx = 35; // posição X inicial do gráfico, relativo a borda esquerda da folha;
	var $gy = 70; // posição Y inicial do gráfico, relativo a borda superior da folha;

	var $gw = 160; // largura do gráfico;
	var $gh = 80;  // altura do gráfico;
	var $pdf;

	var $estilo1t = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => '2,5', 'color' => array(0, 0, 164));
	var $estilo2t = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => '9,5', 'color' => array(0, 0, 164));
	var $estilo3t = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 164));


	public function render($alunos, $turma_rel) {

		// "turma,nome,matricula,turma_id"
		$turma = $alunos[0]['turma'];

		// create new PDF document
		$this->pdf = new ChamadaPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


		// set document information
		$this->pdf->SetCreator(PDF_CREATOR);
		$this->pdf->SetAuthor('Lucas Rondon');
		$this->pdf->SetTitle('Relatório - Colégio Portal');
		$this->pdf->SetSubject('Relatório');
		$this->pdf->SetKeywords('Relatório');

		// set default header data
		$this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
		$this->pdf->setFooterData(array(0,64,0), array(0,64,128));

		// set header and footer fonts
		$this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		//$this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		//$this->pdf->SetFooterMargin(150);

		// set auto page breaks
		//$this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$this->pdf->SetAutoPageBreak(TRUE, 40);
		
		// set image scale factor
		$this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$this->pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		// set default font subsetting mode
		$this->pdf->setFontSubsetting(true);

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$this->pdf->SetFont('dejavusans', '', 11, '', true);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$this->pdf->AddPage();

		$html = '<table  style="font-size:10pt;font-weight:bold">
					<tr>
						<td width="390" >PROFESSOR (A):__________________________________________</td>
						<td width="390" >DISCIPLINA: _____________________________________</td>
						<td width="390" >_____º TRIMESTRE</td>
					</tr>
					</table>
						<br />
						<br />';
		//$this->pdf->writeHTML($html, true, false, true, false, '');
		
		

		$presenca = '<td width="14" >&nbsp;</td>';
		
		$html_cabecalho = '
			<table border="1" cellspacing="0" cellpadding="02" style="border:1px solid #000">
				<tr>
					<td width="70" style="border:1px solid #000">NÚMERO</td>
					<td width="90"  style="border:1px solid #000">MATRÍCULA</td>
					<td width="300"  style="border:1px solid #000">ALUNO (A)</td>
		';
		for($k=0; $k<=31; $k++) {
			$html_cabecalho .= $presenca;
		}
		$html_cabecalho .= '</tr>';
		
		$precisa_fechar_table_aluno_turma = false;
		$primeira_turma = true;
		$n=0;
		$i=0;
		$j=1;
		$turma_id_atual = 0;
		foreach($alunos as $v) {
				
			if($v['turma_id'] != $turma_id_atual) {

				$i=0;
				$j=1;
				if($primeira_turma) {
					if($turma_id_atual != 0) {
						$primeira_turma = false;
					}
				}else {
					$this->pdf->AddPage();
				}
		
				if($n != 0) {
					$html.= '</table>';
				}
				
				if($precisa_fechar_table_aluno_turma) {
					$html.= '</table>';
					$precisa_fechar_table_aluno_turma = false;
				}
				$this->pdf->writeHTML($html, true, false, true, false, '');
				
				$html = '<table><tr><td align="center" style="font-size:14pt;font-weight:bold">Lista de Chamada</td></tr>
				<tr><td align="center" style="font-size:10pt;">'.$v['turma'].'</td></tr>
				</table>';
				$html.= $html_cabecalho;
				
				$turma_id_atual = $v['turma_id'];
			}
			
			// aki
			$bg = "#fff";
			if($i % 2 == 0) {
				$bg = "#efefef";
			}
				
			// <td width="290"  style="border:1px solid #000">'.$this->cortar_texto($v[1], 30).'</td>
			$html .= '
			<tr bgcolor="'.$bg.'">
						<td align="center" width="70" style="border:1px solid #000">'.$j.'</td>
						<td width="90"  style="border:1px solid #000">'.$v['matricula'].'</td>
						<td width="300"  style="border:1px solid #000">'.$v['nome'].'</td>
			';
			for($k=0; $k<=31; $k++) {
				$html .= $presenca;
			}
			$html .= '</tr>';
			$i++;
			$j++;
			$precisa_fechar_table_aluno_turma = true;
		}
		

		$html.= '</table>';
		if($turma_rel == '-1') {
			$this->pdf->AddPage();
		}
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		
		// ---------------------------------------------------------

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$this->pdf->Output('Lista_de_chamada.pdf', 'D');
	}


	private function cortar_texto($texto, $largura_maxima = 100) {
		
		
		
		$replace = array(
				'&lt;' => '', '&gt;' => '', '&#039;' => '', '&amp;' => '',
				'&quot;' => '', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'Ae',
				'&Auml;' => 'A', 'Å' => 'A', 'Ā' => 'A', 'Ą' => 'A', 'Ă' => 'A', 'Æ' => 'Ae',
				'Ç' => 'C', 'Ć' => 'C', 'Č' => 'C', 'Ĉ' => 'C', 'Ċ' => 'C', 'Ď' => 'D', 'Đ' => 'D',
				'Ð' => 'D', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ē' => 'E',
				'Ę' => 'E', 'Ě' => 'E', 'Ĕ' => 'E', 'Ė' => 'E', 'Ĝ' => 'G', 'Ğ' => 'G',
				'Ġ' => 'G', 'Ģ' => 'G', 'Ĥ' => 'H', 'Ħ' => 'H', 'Ì' => 'I', 'Í' => 'I',
				'Î' => 'I', 'Ï' => 'I', 'Ī' => 'I', 'Ĩ' => 'I', 'Ĭ' => 'I', 'Į' => 'I',
				'İ' => 'I', 'Ĳ' => 'IJ', 'Ĵ' => 'J', 'Ķ' => 'K', 'Ł' => 'K', 'Ľ' => 'K',
				'Ĺ' => 'K', 'Ļ' => 'K', 'Ŀ' => 'K', 'Ñ' => 'N', 'Ń' => 'N', 'Ň' => 'N',
				'Ņ' => 'N', 'Ŋ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
				'Ö' => 'Oe', '&Ouml;' => 'Oe', 'Ø' => 'O', 'Ō' => 'O', 'Ő' => 'O', 'Ŏ' => 'O',
				'Œ' => 'OE', 'Ŕ' => 'R', 'Ř' => 'R', 'Ŗ' => 'R', 'Ś' => 'S', 'Š' => 'S',
				'Ş' => 'S', 'Ŝ' => 'S', 'Ș' => 'S', 'Ť' => 'T', 'Ţ' => 'T', 'Ŧ' => 'T',
				'Ț' => 'T', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'Ue', 'Ū' => 'U',
				'&Uuml;' => 'Ue', 'Ů' => 'U', 'Ű' => 'U', 'Ŭ' => 'U', 'Ũ' => 'U', 'Ų' => 'U',
				'Ŵ' => 'W', 'Ý' => 'Y', 'Ŷ' => 'Y', 'Ÿ' => 'Y', 'Ź' => 'Z', 'Ž' => 'Z',
				'Ż' => 'Z', 'Þ' => 'T', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
				'ä' => 'ae', '&auml;' => 'ae', 'å' => 'a', 'ā' => 'a', 'ą' => 'a', 'ă' => 'a',
				'æ' => 'ae', 'ç' => 'c', 'ć' => 'c', 'č' => 'c', 'ĉ' => 'c', 'ċ' => 'c',
				'ď' => 'd', 'đ' => 'd', 'ð' => 'd', 'è' => 'e', 'é' => 'e', 'ê' => 'e',
				'ë' => 'e', 'ē' => 'e', 'ę' => 'e', 'ě' => 'e', 'ĕ' => 'e', 'ė' => 'e',
				'ƒ' => 'f', 'ĝ' => 'g', 'ğ' => 'g', 'ġ' => 'g', 'ģ' => 'g', 'ĥ' => 'h',
				'ħ' => 'h', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ī' => 'i',
				'ĩ' => 'i', 'ĭ' => 'i', 'į' => 'i', 'ı' => 'i', 'ĳ' => 'ij', 'ĵ' => 'j',
				'ķ' => 'k', 'ĸ' => 'k', 'ł' => 'l', 'ľ' => 'l', 'ĺ' => 'l', 'ļ' => 'l',
				'ŀ' => 'l', 'ñ' => 'n', 'ń' => 'n', 'ň' => 'n', 'ņ' => 'n', 'ŉ' => 'n',
				'ŋ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'oe',
				'&ouml;' => 'oe', 'ø' => 'o', 'ō' => 'o', 'ő' => 'o', 'ŏ' => 'o', 'œ' => 'oe',
				'ŕ' => 'r', 'ř' => 'r', 'ŗ' => 'r', 'š' => 's', 'ù' => 'u', 'ú' => 'u',
				'û' => 'u', 'ü' => 'ue', 'ū' => 'u', '&uuml;' => 'ue', 'ů' => 'u', 'ű' => 'u',
				'ŭ' => 'u', 'ũ' => 'u', 'ų' => 'u', 'ŵ' => 'w', 'ý' => 'y', 'ÿ' => 'y',
				'ŷ' => 'y', 'ž' => 'z', 'ż' => 'z', 'ź' => 'z', 'þ' => 't', 'ß' => 'ss',
				'ſ' => 'ss', 'ый' => 'iy', 'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G',
				'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I',
				'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
				'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F',
				'Х' => 'H', 'Ц' => 'C', 'Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'SCH', 'Ъ' => '',
				'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA', 'а' => 'a',
				'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo',
				'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l',
				'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's',
				'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
				'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e',
				'ю' => 'yu', 'я' => 'ya'
		);
		
		$texto = str_replace(array_keys($replace), $replace, $texto);
		
		//$texto = utf8_encode($texto);
		//return substr($texto, 0, strlen($texto) - 1) . ' - ' . $this->pdf->GetStringWidth($texto);
		
		//$texto = mb_convert_encoding($texto, 'ISO-8859-1', 'UTF-8');
		if($this->pdf->GetStringWidth($texto) <= $largura_maxima) {
			return $texto;
		}
		
		$ret = " (...)";
		//return substr($texto, 0, 17) . $ret;
		
		
		$largura_maxima = $largura_maxima - (int)$this->pdf->GetStringWidth($ret);
		while($this->pdf->GetStringWidth($texto) > $largura_maxima) {
			$texto = substr($texto, 0, strlen($texto) - 1);
		}
		return $texto . $ret;
	}
	
	private function scale_image($src_image) {
		$src_width = imagesx($src_image);
		$src_height = imagesy($src_image);

		$dst_width = 100;
		$dst_height = 100;

		// Try to match destination image by width
		$new_width = $dst_width;
		$new_height = round($new_width*($src_height/$src_width));
		$new_x = 0;
		$new_y = round(($dst_height-$new_height)/2);

		// FILL and FIT mode are mutually exclusive

		$next = $new_height > $dst_height;

		// If match by width failed and destination image does not fit, try by height
		if ($next) {
			$new_height = $dst_height;
			$new_width = round($new_height*($src_width/$src_height));
			$new_x = round(($dst_width - $new_width)/2);
			$new_y = 0;
		}

		// Copy image on right place
		return array($new_width, $new_height);
		//imagecopyresampled($dst_image, $src_image , $new_x, $new_y, 0, 0, $new_width, $new_height, $src_width, $src_height);
	}

}

class TelefonePDF extends  tcpdf {

		//Page header
	//	public function Header() {
	//		// Logo
	//		$image_file = 'visao/pagina/conteudo/relatorio/boletim/logo_boletim.jpg';
	//
	//
	//		$this->SetMargins(PDF_MARGIN_LEFT, 30, PDF_MARGIN_RIGHT);
	//
	//		$html = '<table cellpadding="1" cellspacing="10" border="0" style="text-align:center;">
	//				<tr><td>&nbsp;</td></tr>
	//<tr style="text-align:left;"><td><img src="visao/pagina/conteudo/relatorio/boletim/TOPO2.jpg" border="0" width="198" width="0" align="top" /></td></tr>
	//</table>';
	//
	//		// output the HTML content
	//		$this->writeHTML($html, true, false, true, false, '');
	//
	//	}

    function Header()
    {
        // To be implemented in your own inherited class
		$caminho_imagem = base_url().'/assets/admin/dist/img/TOPO2.jpg';

        $quadrado = 25;
        $this->Image($caminho_imagem, 6, 4, 198, 15);
    }

    // Page footer
	public function Footer() {
		// Position at 15 mm from bottom
		$this->SetY(-35);
		// Set font
		$this->SetFont('helvetica', 'I', 8);
		// Page number
		$this->Cell(0, 10, 'Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');

        //$this->Image('visao/pagina/conteudo/relatorio/boletim/RODAPEP_LISTRAS.jpg', 6, 286, 198, 3);
	}

	/*
	public function Footer() {
		// Position at 15 mm from bottom
		$this->SetY(-15);
		// Set font
		$this->SetFont('helvetica', 'I', 8);
		// Page number
		$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
	}*/
}



class relatorio_lista_telefone {
	

	var $gx = 35; // posição X inicial do gráfico, relativo a borda esquerda da folha;
	var $gy = 70; // posição Y inicial do gráfico, relativo a borda superior da folha;

	var $gw = 160; // largura do gráfico;
	var $gh = 80;  // altura do gráfico;
	var $pdf;
	
	var $estilo1t = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => '2,5', 'color' => array(0, 0, 164));
	var $estilo2t = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => '9,5', 'color' => array(0, 0, 164));
	var $estilo3t = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 164));
	
	
	public function render($alunos, $turma_rel) {
		
		// "turma,nome,matricula,nasc,fone"
		$turma = $alunos[0]['turma'];
		
		
		// create new PDF document
		$this->pdf = new TelefonePDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		
		
		// set document information
		$this->pdf->SetCreator(PDF_CREATOR);
		$this->pdf->SetAuthor('Lucas Rondon');
		$this->pdf->SetTitle('TELEFONE');
		$this->pdf->SetSubject('Relatório');
		$this->pdf->SetKeywords('Relatório');
		
		// set default header data
		$this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
		$this->pdf->setFooterData(array(0,64,0), array(0,64,128));
		
		// set header and footer fonts
		$this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		// set default monospaced font
		$this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		// set margins
		$this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		
		// set auto page breaks
		$this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		// set image scale factor
		$this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$this->pdf->setLanguageArray($l);
		}
		
		// ---------------------------------------------------------
		
		// set default font subsetting mode
		$this->pdf->setFontSubsetting(true);
		
		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$this->pdf->SetFont('dejavusans', '', 10, '', true);
		
		
		
		$html_cabecalho = '
						<table border="0" cellspacing="0" cellpadding="02" style="border:1px solid #000">
							<tr bgcolor="#FFFFCC" >
								<td width="230" style="border:1px solid #000">Nome</td>
								<td width="90"  style="border:1px solid #000">Matrícula</td>
								<td width="90"  style="border:1px solid #000">Nascimento</td>
								<td width="190"  style="border:1px solid #000">Telefone</td>
							</tr>';
		

		$this->pdf->AddPage();
		$html =
		'<table><tr><td align="center" style="font-size:12pt;font-weight:bold">Telefone / Nascimento</td></tr></table>';

		$this->pdf->SetFont('dejavusans', '', 11, '', true);
		// "turma,nome,matricula,nasc,fone"
		
		$primeira_turma = true;
		
		$i=0;
		$turma_id_atual = 0;
		foreach($alunos as $v) {
			
			if($v['turma_id'] != $turma_id_atual) {


				if($primeira_turma) {
					if($turma_id_atual != 0) {
						$primeira_turma = false;
					}
				}else {
					$this->pdf->AddPage();
				}
				
				if($i != 0) {
					$html.= '</table>';
				}
				
				$this->pdf->writeHTML($html, true, false, true, false, '');
						
					$html = '<table  cellpadding="10" cellspacing="10">
							<tr><td align="center" style="font-size:10pt;">'. $v['turma'] . ':</td></tr>
							</table>';
					$html.= $html_cabecalho;
				
					$turma_id_atual = $v['turma_id'];
			}
			
			$bg = "#fff";
			if($i % 2 == 0) {
				$bg = "#efefef";
			}
			$html .= '
					<tr bgcolor="'.$bg.'">
						<td style="border:1px solid #000">'. $v['nome'] . '</td>
						<td style="border:1px solid #000">'. $v['matricula'] . '</td>
						<td style="border:1px solid #000">'. $v['nasc'] . '</td>
						<td style="border:1px solid #000">'. $v['fone'] . '</td>
					</tr>';
			$i++;
		}
		
		$html.= '</table>';

		if($turma_rel == '-1') {
			$this->pdf->AddPage();
		}
		$this->pdf->writeHTML($html, true, false, true, false, '');
		

		// output the HTML content

		
		// ---------------------------------------------------------
		
		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$this->pdf->Output('Telefone.pdf', 'D');
	}
	
	private function scale_image($src_image) {
		$src_width = imagesx($src_image);
		$src_height = imagesy($src_image);
	
		$dst_width = 100;
		$dst_height = 100;
	
		// Try to match destination image by width
		$new_width = $dst_width;
		$new_height = round($new_width*($src_height/$src_width));
		$new_x = 0;
		$new_y = round(($dst_height-$new_height)/2);
	
		// FILL and FIT mode are mutually exclusive
		
		$next = $new_height > $dst_height;

		// If match by width failed and destination image does not fit, try by height
		if ($next) {
			$new_height = $dst_height;
			$new_width = round($new_height*($src_width/$src_height));
			$new_x = round(($dst_width - $new_width)/2);
			$new_y = 0;
		}

		// Copy image on right place
		return array($new_width, $new_height);
		//imagecopyresampled($dst_image, $src_image , $new_x, $new_y, 0, 0, $new_width, $new_height, $src_width, $src_height);
	}
	
}

class notas_trimestrais{

	private $sem_valor = '-';
	private $provas = array (
			's1',
			's2',
			's3',
			's4',
			's',
			'f' 
	);
	
	private $nota;
	public $tabela;
	
	public function __construct() {
		
		//$this->acao = "$this->objeto.visualizar";
		
		$this->nota = new nota();
	}

	public function boletim_trimestral($aluno_id, $turma_id, $trimestre) {
		

		$obj_turma	= (new SI_TurmaModel())->find($turma_id);
		$obj_aluno 	= (new SI_AlunoModel())->find($aluno_id);

		$this->tabela = [];
		
		$nivel = $obj_turma->id_nivel;
		$ano = $obj_turma->ano;
		
		$vetor_provas_da_turma = $this->get_provas_turma ( $turma_id );
		
		$disciplinas = parametro::disciplinas ();
		$colunas = [];
		$linhas = [];
		
		//$tabela = new modelo__tabela ();
		
		$vetor_colunas = array (
				'-' 
		);

		foreach ( $vetor_provas_da_turma as $prova ) {
			array_push ( $vetor_colunas, parametro::get_prova ( $prova ) );
		}
		array_push ( $vetor_colunas, 'Faltas' );
		array_push ( $vetor_colunas, 'Média trimestral' );
		
		$colunas[] =  $vetor_colunas;
		
		foreach ( $disciplinas as $disciplina => $nome_disciplina ) {
			
			$vetor_linha = array ();
			array_push ( $vetor_linha, "<b>$nome_disciplina</b>" );
			
			
			$s1 = 0;
			$s2 = 0;
			$s3 = 0;
			$s4 = 0;
			$sim = 0;
			$for = 0;
			
			
			
			
			
			$possui_prova_sem_nota = false;
			foreach ( $vetor_provas_da_turma as $prova ) {
				
				if ($this->get_disciplina_possui_prova ( $disciplina, $turma_id, $prova )) {
					
					$q = "
		    			SELECT nota
		    			FROM si_nota n
		    			WHERE
		    				fk_aluno = " . $obj_aluno->id. "
		    				AND fk_turma = " . $obj_turma->id. "
	    	    			AND trimestre = $trimestre
	    	    			AND id_disciplina = '$disciplina'
	    	    			AND id_prova = '$prova'
	    	    		";
					
					// echo $q . "<br>";
					$r = (new SI_NotaModel())->query($q)->getResultArray();
					
					if ($r != null) {
						array_push ( $vetor_linha, str_replace ( ".", ",", $r [0]['nota'] ) );
						
						$nota = $r[0]['nota'];
						
						
						if($prova == 's1') {
							$s1 = $nota;
						}
						if($prova == 's2') {
							$s2 = $nota;
						}
						if($prova == 's3') {
							$s3 = $nota;
						}
						if($prova == 's4') {
							$s4 = $nota;
						}
						if($prova == 's') {
							$sim = $nota;
						}
						if($prova == 'f') {
							$for = $nota;
						}
						
						
					} else {
						$possui_prova_sem_nota = true;
						array_push ( $vetor_linha, $this->sem_valor );
					}
				} else {
					array_push ( $vetor_linha, '<b><i>N.A.</i></b>' );
				}
			}
			
			// $media_trimestral = 0;
			$media_trimestral_arredondada = 0;
			
			$texto_media_trimestral = '-';
			
			if ($possui_prova_sem_nota) {
				// não é possível calcular a média trimestral,
				// pois existem provas sem nota.
				$texto_media_trimestral = parametro::nota_sem_valor();
			} else {
				
				// exibir o cálculo da média trimestral.
				
				$mt = $this->get_media_trimestral($nivel, $disciplina, $s1, $s2, $s3, $s4, $sim, $for, $ano);
				$media_trimestral_arredondada = calculo::arredonda_nota($mt);
				$texto_media_trimestral = "<B>$media_trimestral_arredondada</B>";
			}
			
			array_push ( $vetor_linha, $this->get_faltas ( $aluno_id, $turma_id, $disciplina, $trimestre ) );
			array_push ( $vetor_linha, $texto_media_trimestral );
			
			$linhas[] = $vetor_linha ;
		}
		//$tabela->fecha_tabela ();
		
		//echo 'N.A. = Não se aplica<br/>? = Sem dados suficientes para o cálculo';

		$this->tabela['colunas'] = $colunas;
		$this->tabela['linhas'] = $linhas;

		return $this->tabela;
	}

	private function get_provas_turma($turma_id) {
		return $this->nota->get_provas_turma($turma_id);
		
	}

	private function get_disciplina_possui_prova($disciplina, $turma_id, $prova) {
		return $this->nota->get_disciplina_possui_prova($disciplina, $turma_id, $prova);
	}

	private function get_media_trimestral($nivel, $disciplina, $s1, $s2, $s3, $s4, $sim, $for, $ano) {
		return $this->nota->get_media_trimestral($nivel, $disciplina, $s1, $s2, $s3, $s4, $sim, $for, $ano);
	}

	protected function get_faltas($aluno, $turma, $disciplina, $trimestre) {
		$faltas =  $this->nota->get_faltas($aluno, $turma, $disciplina, $trimestre);
		if($faltas == parametro::nota_sem_valor()) {
			return '-';
		}
		return $faltas;
	}
}


class FPDF_PAGENUMBER extends FPDF{

	// Page footer
	function Footer()
	{
		// Position at 1.5 cm from bottom
		$this->SetY(-15);
		// Arial italic 8
		$this->SetFont('Arial','I',8);
		// Page number
		$this->Cell(0,10, mb_convert_encoding('Página '.$this->PageNo().' de {nb}', 'ISO-8859-1', 'UTF-8'),0,0,'R');
	}
	
}


class relatorio_pai_pdf {

	private $largura_matricula = 23;
	private $largura_pagina = 190;
	private $largura_nome = 165;
	private $pdf;



	public function __construct() {
		$this->pdf = new FPDF_PAGENUMBER();
		//$this->pdf = new FPDF();
		$this->pdf->AliasNbPages();
	}

	public function gerar_lista($pais) {
		


		$caminho_imagem = base_url().'/assets/admin/dist/img/logo_boletim.jpg';
		
		$this->pdf->AddPage ();

		$this->pdf->Cell(170, 20, '', 0, 1);
		$this->pdf->Image($caminho_imagem, 10, 10, 95, 12);
		
		$this->pdf->SetFont ( 'Arial', '', 15 );

		$this->pdf->Cell($this->largura_pagina, 5, mb_convert_encoding('Relatório de Pais', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
		$this->pdf->Ln();
		
        
		$this->pdf->SetFont ( 'Arial', '', 11 );
		$this->pdf->Cell(70, 5, mb_convert_encoding('Ordenados pela Matrícula', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell($this->largura_pagina, 5, mb_convert_encoding('Quantidade de pais: ' . sizeof( $pais ), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell($this->largura_pagina, 5, mb_convert_encoding('Data do relatório: ' . date('d/m/Y'), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Ln();

		$this->pdf->SetFont ( 'Arial', 'B', 11 );
		$this->pdf->Cell($this->largura_matricula, 5, mb_convert_encoding('Matrícula', 'ISO-8859-1', 'UTF-8'), 1, 0);
		$this->pdf->Cell($this->largura_nome, 5, mb_convert_encoding('Nomes', 'ISO-8859-1', 'UTF-8'), 1, 0);
		$this->pdf->Ln();
		
		$this->pdf->SetFont ( 'Arial', '', 9 );
		
		
		
		// para não separar os nomes de uma mesma matrícula:
		foreach ($pais as $pai) {
			$this->gerar_pai($pai);
			if($this->pdf->GetY() > 255) {
				$this->pdf->AddPage();
			}
		}
		
		$this->pdf->Output('Pais.pdf', 'D');
	}

	private function gerar_pai($pai) {
		
		$nome_pai = $pai["nome_pai"];
		$nome_mae = $pai["nome_mae"];
		$nome_resp = $pai["nome_resp"];
		$matricula = $pai["matricula"];

		$this->pdf->Cell($this->largura_matricula, 15, $matricula, 1, 0);

		$this->pdf->Cell(25, 5, mb_convert_encoding('Pai: ', 'ISO-8859-1', 'UTF-8'), 1, 0);
		$this->pdf->Cell(140, 5, mb_convert_encoding($nome_pai, 'ISO-8859-1', 'UTF-8'), 1, 0);
		$this->pdf->Ln();
		
		$this->pdf->Cell($this->largura_matricula, 15, '', 0, 0);
		$this->pdf->Cell(25, 5, mb_convert_encoding('Mãe: ', 'ISO-8859-1', 'UTF-8'), 1, 0);
		$this->pdf->Cell(140, 5, mb_convert_encoding($nome_mae, 'ISO-8859-1', 'UTF-8'), 1, 0);
		$this->pdf->Ln();

		$this->pdf->Cell($this->largura_matricula, 15, '', 0, 0);
		$this->pdf->Cell(25, 5, mb_convert_encoding('Responsável: ', 'ISO-8859-1', 'UTF-8'), 1, 0);
		$this->pdf->Cell(140, 5, mb_convert_encoding($nome_resp, 'ISO-8859-1', 'UTF-8'), 1, 0);
		$this->pdf->Ln();
		$this->pdf->Ln();
		
	}
}


class relatorio_aluno_pdf {

	private $largura_matricula = 23;
	private $largura_pagina = 190;
	private $largura_nome = 165;
	private $pdf;



	public function __construct() {
		$this->pdf = new FPDF_PAGENUMBER();
		$this->pdf->AliasNbPages();
	}

	public function gerar_lista($alunos) {
		


		$caminho_imagem = base_url().'/assets/admin/dist/img/logo_boletim.jpg';
		
		
		
		$this->pdf->AddPage ();

		$this->pdf->Cell(170, 20, '', 0, 1);
		$this->pdf->Image($caminho_imagem, 10, 10, 95, 12);
		
		$this->pdf->SetFont ( 'Arial', '', 15 );

		$this->pdf->Cell($this->largura_pagina, 5, mb_convert_encoding('Relatório de Alunos', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
		$this->pdf->Ln();
		
		$this->pdf->SetFont ( 'Arial', '', 11 );
		$this->pdf->Cell(70, 5, mb_convert_encoding('Ordenados pela Matrícula', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell($this->largura_pagina, 5, mb_convert_encoding('Quantidade de alunos: ' . sizeof( $alunos ), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell($this->largura_pagina, 5, mb_convert_encoding('Data do relatório: ' . date('d/m/Y'), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Ln();

		$this->pdf->SetFont ( 'Arial', 'B', 11 );
		$this->pdf->Cell($this->largura_matricula, 5, mb_convert_encoding('Matrícula', 'ISO-8859-1', 'UTF-8'), 1, 0);
		$this->pdf->Cell($this->largura_nome, 5, mb_convert_encoding('Nome aluno', 'ISO-8859-1', 'UTF-8'), 1, 0);
		$this->pdf->Ln();
		
		$this->pdf->SetFont ( 'Arial', '', 9 );
		
		foreach ($alunos as $aluno) {
			$this->gerar_aluno($aluno);
		}
		
		$this->pdf->Output('Alunos.pdf', 'D');
	}
	private function gerar_aluno($aluno) {
		
		$nome = $aluno["nome"];
		$matricula = $aluno["matricula"];

		$this->pdf->Cell($this->largura_matricula, 5, mb_convert_encoding($matricula, 'ISO-8859-1', 'UTF-8'), 1, 0);
		$this->pdf->Cell($this->largura_nome, 5, mb_convert_encoding($nome, 'ISO-8859-1', 'UTF-8'), 1, 0);
		$this->pdf->Ln();
		
	}
}


class relatorio_aluno_matricula_pdf {

	private $largura_matricula = 23;
	private $largura_pagina = 190;
	private $largura_nome = 165;
	private $pdf;



	public function __construct() {
		$this->pdf = new FPDF_PAGENUMBER();
		$this->pdf->AliasNbPages();
	}

	public function gerar_lista($ano, $alunos) {

		
		/*
		echo '<pre>';
		print_r($alunos);
		exit();
	*/

		$caminho_imagem = base_url().'/assets/admin/dist/img/logo_boletim.jpg';
		
		
		
		$this->pdf->AddPage ();

		$this->pdf->Cell(170, 20, '', 0, 1);
		$this->pdf->Image($caminho_imagem, 10, 10, 95, 12);
		
		$this->pdf->SetFont ( 'Arial', '', 15 );

		$this->pdf->Cell($this->largura_pagina, 5, mb_convert_encoding('Livro de matrículas - Ano ' . $ano, 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
		$this->pdf->Ln();
		
		$this->pdf->SetFont ( 'Arial', '', 11 );
		$this->pdf->Cell(70, 5, mb_convert_encoding('Ordenados pelo Nome do Aluno (exibindo apenas alunos de turmas do ano letivo ' . $ano . ')', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell($this->largura_pagina, 5, mb_convert_encoding('Quantidade de alunos: ' . sizeof( $alunos ), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell($this->largura_pagina, 5, mb_convert_encoding('Data do relatório: ' . date('d/m/Y'), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Ln();

		$this->pdf->SetFont ( 'Arial', 'B', 11 );
		
		$this->pdf->SetFont ( 'Arial', '', 9 );
		
		$turma_atual = '';
		$i=0;
		foreach ($alunos as $aluno) {
			$turma = $aluno->aluno_turma;
			if($turma != $turma_atual) {
				$turma_atual = $turma;
				$this->gerar_aluno($aluno, $i==0, $turma_atual);
			}else{
				$this->gerar_aluno($aluno, $i==0, null);
			}
			$i++;
		}
		
		$this->pdf->Output('Livro_Matricula.pdf', 'D');
	}
	private function gerar_aluno($aluno, $primeiro_aluno, $nova_turma) {
		
		$pagina_adicionada = false;
		if($this->pdf->GetY() > 250) {
			$this->pdf->AddPage();
			$pagina_adicionada = true;
		}
		
		$nome = $aluno->aluno_nome;
		$matricula = $aluno->aluno_matricula;
		$nasc = $aluno->aluno_nasc;
		$fone_aluno = $aluno->aluno_fone;
		$end = $aluno->aluno_end;
		$turma = $aluno->aluno_turma;
		$pai = $aluno->nome_pai;
		$mae = $aluno->nome_mae;
		$resp = $aluno->nome_resp;
		
		$fone_pai = $aluno->fone_pai;
		$cel_pai = $aluno->cel_pai;
		
		$fone_mae = $aluno->fone_mae;
		$cel_mae = $aluno->cel_mae;
		
		$fone_resp = $aluno->fone_resp;
		$cel_resp = $aluno->cel_resp;
		
		$padding = 9;

		$this->pdf->SetFont ( 'Arial', 'B', 13 );
		if($nova_turma != null) {
			if(!$pagina_adicionada && !$primeiro_aluno) {
				$this->pdf->AddPage();
				
			}
			$this->pdf->Cell(170, 10, mb_convert_encoding("Turma: " . $nova_turma, 'ISO-8859-1', 'UTF-8'), 0, 0, 'C');
			$this->pdf->Ln();
			$this->pdf->Ln();
		}

		$this->pdf->SetFont ( 'Arial', 'B', 9 );
		$this->pdf->Cell(170, 5, mb_convert_encoding("Aluno(a): " . $nome, 'ISO-8859-1', 'UTF-8'), 1, 0);
		$this->pdf->SetFont ( 'Arial', '', 9 );
		$this->pdf->Ln();
		$this->pdf->Cell($padding, 5, "", 0, 0);
		$this->pdf->Cell(40, 5, mb_convert_encoding("Matrícula: " . $matricula, 'ISO-8859-1', 'UTF-8'), 0, 0);
		//$this->pdf->Cell(40, 5, mb_convert_encoding("Turma: " . $turma, 'ISO-8859-1', 'UTF-8'), 0, 0);
		$this->pdf->Cell(40, 5, mb_convert_encoding("Nasc.: " . $nasc, 'ISO-8859-1', 'UTF-8'), 0, 0);
		$this->pdf->Cell(40, 5, mb_convert_encoding("Fone aluno: " . $fone_aluno, 'ISO-8859-1', 'UTF-8'), 0, 0);
		$this->pdf->Ln();
		$this->pdf->Cell($padding, 5, "", 0, 0);
		
		
		
		

		$this->pdf->SetFont ( 'Arial', 'B', 9 );		
		$this->pdf->Cell(100, 5, "", 0, 0);
		$this->pdf->Cell(30, 5, "Telefone", 0, 0);
		$this->pdf->Cell($padding, 5, "Celular", 0, 0);
		$this->pdf->Ln();
		
		
		
		$this->pdf->SetFont ( 'Arial', '', 9 );
		
		$this->pdf->Cell($padding, 5, "", 0, 0);
		$this->pdf->Cell(100, 5, mb_convert_encoding("Pai: " . $pai, 'ISO-8859-1', 'UTF-8'), 'B', 0);
		$this->pdf->Cell(30, 5, mb_convert_encoding($fone_pai, 'ISO-8859-1', 'UTF-8'), 'B', 0);
		$this->pdf->Cell(30, 5, mb_convert_encoding($cel_pai, 'ISO-8859-1', 'UTF-8'),'B', 0);
		$this->pdf->Ln();
		$this->pdf->Cell($padding, 5, "", 0, 0);
		$this->pdf->Cell(100, 5, mb_convert_encoding("Mãe: " . $mae, 'ISO-8859-1', 'UTF-8'), 'B', 0);
		$this->pdf->Cell(30, 5, mb_convert_encoding($fone_mae, 'ISO-8859-1', 'UTF-8'), 'B', 0);
		$this->pdf->Cell(30, 5, mb_convert_encoding($cel_mae, 'ISO-8859-1', 'UTF-8'), 'B', 0);
		$this->pdf->Ln();
		$this->pdf->Cell($padding, 5, "", 0, 0);
		$this->pdf->Cell(100, 5, mb_convert_encoding("Resp.: " . $resp, 'ISO-8859-1', 'UTF-8'), 'B', 0);
		$this->pdf->Cell(30, 5, mb_convert_encoding($fone_resp, 'ISO-8859-1', 'UTF-8'), 'B', 0);
		$this->pdf->Cell(30, 5, mb_convert_encoding($cel_resp, 'ISO-8859-1', 'UTF-8'), 'B', 0);
		
		$this->pdf->Ln();
		$this->pdf->Ln();
		$this->pdf->Ln();
		
	}
}


class relatorio_media_nucleo_materia_montar extends Controller{

	public $sem_valor, $vetor_disciplina_possui_prova;
	
	public function __construct() {

		$this->sem_valor = parametro::nota_sem_valor();
		
	}
	
	public function render($turma_id) {

		$pdf = new relatorio_media_nucleo_materia_pdf();

		
		
		
		//$turma_id = $this->request->getPost('turma');
		$turma = (new SI_TurmaModel())->find($turma_id);
		

		$vetor_medias_trimestrais = array();
		
		$vetor_disciplinas = parametro::disciplinas();
		
		foreach ($vetor_disciplinas as $k => $v) {
			$disciplina_id = $k;
			$vetor_medias_trimestrais[$disciplina_id] = array();
			
			$nota1t_sem_arredondar = $this->get_media_turma($turma_id, $disciplina_id, 1, false);
			$nota2t_sem_arredondar = $this->get_media_turma($turma_id, $disciplina_id, 2, false);
			$nota3t_sem_arredondar = $this->get_media_turma($turma_id, $disciplina_id, 3, false);
			
			$nota1t_arredondada = $this->limitar_decimais ( $nota1t_sem_arredondar);
			$nota2t_arredondada = $this->limitar_decimais ( $nota2t_sem_arredondar);
			$nota3t_arredondada = $this->limitar_decimais ( $nota3t_sem_arredondar);

			$vetor_medias_trimestrais[$disciplina_id]['1t'] = $nota1t_arredondada;
			$vetor_medias_trimestrais[$disciplina_id]['2t'] = $nota2t_arredondada;
			$vetor_medias_trimestrais[$disciplina_id]['3t'] = $nota3t_arredondada;
			
		}
		
		$pdf->set_vetores($turma->nome, $turma->ano, $vetor_medias_trimestrais
		);
		
	}

	public function get_media_turma($turma_id, $disciplina, $trimestre, $arredondar = true) {

	//dd($turma_id);
		$qtde_alunos_encontrados = 0;
		$soma_medias_trimestrais_todos_alunos = 0;

		$turmaModel = new SI_TurmaModel();
		$notaModel = new SI_NotaModel();
	/*
		$q = "SELECT id_nivel FROM si_turma WHERE id = $turma_id";
		$r = $this->conn->qcv ( $q, "id_nivel" );
		$nivel = $r [0];*/
	//$query = "select id_nivel from si_turma where id = $turma_id";

								$result = $turmaModel->query("select id_nivel from si_turma where id = $turma_id")->getResult();

		//$result = $turmaModel->query("select id_nivel from si_turma where id = ".$turma_id."")->getResult();
		//$result = $turmaModel->where('id', $turma_id)->findAll();

		//dd($result);

		$nivel = $result[0]->id_nivel;
		
		$raluno = $notaModel->query("
										SELECT n.fk_aluno
										FROM si_nota n
										
										JOIN si_aluno a
										ON n.fk_aluno = a.id
										
										WHERE
										a.status = '1' 
										AND trimestre = $trimestre
										AND id_disciplina = '$disciplina'
										AND fk_turma = $turma_id
										
										AND (
												SELECT COUNT(*) qtde 
												FROM si_aluno_turma at 
												WHERE at.fk_aluno = n.fk_aluno 
												AND at.fk_turma = n.fk_turma
											) > 0
										
										GROUP BY fk_aluno
									")->getResult();
									
		
		if ($raluno != null) {
			$soma_medias_trimestrais_todos_alunos = 0;
			foreach ( $raluno as $alun_id ) {
				// média trimestral de apenas 1 aluno:
				$mt = $this->get_media_trimestral_periodo_anual ( $nivel, $alun_id->fk_aluno, $disciplina, $turma_id, $trimestre );
				
				if ($mt == $this->sem_valor) {

					
					// passa para o próximo aluno, pois este está com alguma nota faltando.
					continue;
				}
				
				$soma_medias_trimestrais_todos_alunos += (float)$mt;
				$qtde_alunos_encontrados ++;
			}
		} else {
			return $this->sem_valor;
		}
		if ($qtde_alunos_encontrados == 0) {
			return $this->sem_valor;
		}
		$media_turma = $soma_medias_trimestrais_todos_alunos / $qtde_alunos_encontrados;
		

		if($arredondar) {
			$media_turma = calculo::arredonda_nota ( $media_turma, false );
		}
		return $media_turma;
	}

	public function get_media_trimestral_periodo_anual($nivel, $aluno_id, $disciplina, $turma_id, $trimestre) {
		
		$todas_notas = (new nota)->get_vetor_todas_notas( $turma_id );

		$vetor_provas_da_turma = (new nota)->get_provas_turma ( $turma_id );
		
		$q = (new SI_TurmaModel())->query("SELECT ano FROM si_turma WHERE id = $turma_id")->getResult();
		
		$r = (array)$q;
		$ano = $r[0]->ano;
		
		
		
		
		
		$s1 = 0;
		$s2 = 0;
		$s3 = 0;
		$s4 = 0;
		$sim = 0;
		$for = 0;
		
		
		$this->popular_disciplina_possui_prova();
		
		$possui_prova_sem_nota = false;
		foreach ( $vetor_provas_da_turma as $prova ) {
			if ((new nota)->get_disciplina_possui_prova ( $disciplina, $turma_id, $prova )) {
				
				
				$nota_encontrada = null;
				
				// buscar a nota do: aluno, turma, trimestre, disciplina e prova especificados:
				foreach ($todas_notas as $nota_obj) {
					if(
						$nota_obj->fk_aluno == $aluno_id &&
						$nota_obj->fk_turma == $turma_id &&
						$nota_obj->trimestre == $trimestre &&
						$nota_obj->id_disciplina == $disciplina &&
						$nota_obj->id_prova == $prova 
						) 
					{
						
						
						//exit();
						$nota_encontrada = $nota_obj->nota;
						
						//echo "BUSCAR: $aluno_id, $turma_id, $trimestre, $disciplina, $prova;  ENCONTROU: $nota_encontrada<br />";
						
						//echo "(" . $nota_encontrada . ")";
						break;
					}
				}
				
				
				//echo $nota_encontrada . " -- ";
				//exit();
				
				
				if ($nota_encontrada !== null) {
					
					$nota = $nota_encontrada;
					
					if ($prova == 's1') {
						$s1 = $nota;
					}
					if ($prova == 's2') {
						$s2 = $nota;
					}
					if ($prova == 's3') {
						$s3 = $nota;
					}
					if ($prova == 's4') {
						$s4 = $nota;
					}
					if ($prova == 's') {
						$sim = $nota;
					}
					if ($prova == 'f') {
						$for = $nota;
					}
				} else {
					$possui_prova_sem_nota = true;
				}
			}else{
				//echo "NÃO POSSUI $disciplina, $turma_id, $prova<br />";
			}
		}
		
		//echo "<pre>$nivel, $disciplina, $s1, $s2, $s3, $sim, $for;</pre>";
		
		// $media_trimestral = 0;
		$media_trimestral_arredondada = 0;
		
		$texto_media_trimestral = '';
		
		if ($possui_prova_sem_nota) {
			// não é possível calcular a média trimestral,
			// pois existem provas sem nota.
			$texto_media_trimestral = $this->sem_valor;
		} else {
			
			// exibir o cálculo da média trimestral.
			
			
			$mt = $this->get_media_trimestral ( $nivel, $disciplina, $s1, $s2, $s3, $s4, $sim, $for, $ano );
			
			//echo "CONSULTA: $nivel, $aluno_id, $disciplina, $turma_id, $trimestre<br />";
			//echo "MT =get_media_trimestral -> $nivel, $disciplina, $s1, $s2, $s3, $sim, $for ------ ".sizeof($todas_notas)."<br /><br />";
			
			//echo "<pre>--- Média Trimestral ALUNO ID ($aluno_id, DISCIPLINA $disciplina, TURMA $turma_id, TRIM $trimestre ------- " . $mt . " -.- </pre>";
			$media_trimestral_arredondada = calculo::arredonda_nota ( $mt );
			$texto_media_trimestral = "$media_trimestral_arredondada";
		}
		return $texto_media_trimestral;
	}

	public function get_media_trimestral($nivel, $disciplina, $s1, $s2, $s3, $s4, $sim, $for, $ano = -1) {

        if($ano == 2020) {
            return (new nota)->get_media_trimestral_2020($nivel, $disciplina, $s1, $s2, $s3, $s4, $sim, $for);
        }

        if($ano >= 2021) {
            return (new nota)->get_media_trimestral_2021_em_diante($nivel, $disciplina, $s1, $s2, $s3, $s4, $sim, $for);
        }

		$mt = 0;
		
		if ($nivel == '1a') {
			// todas disciplinas usam tipo 1
			$mt = calculo::get_media_trimestral_tipo_1 ( $s1, $for );
		}
		
		if ($nivel == '2a' || $nivel == '3a') {
			if ($disciplina == 'p' || $disciplina == 'ma' || $disciplina == 'h' || $disciplina == 'g' || $disciplina == 'c') {
				$mt = calculo::get_media_trimestral_tipo_2 ( $s1, $s2, $for );
			} else {
				$mt = calculo::get_media_trimestral_tipo_1 ( $s1, $for );
			}
		}
	
		
		
		
		
		// 2018 - NOVA regra de português do 4º ao 9º ANO campo novo S4:
		if($ano >= 2018 && $disciplina == 'p') {
			if($nivel == '4a' || $nivel == '5a') {
				$mt = calculo::get_media_trimestral_4a5_ano_portugues_2018 ( $s1, $s2, $s3, $s4, $for );
				return $mt;
			}
			if($nivel == '6a' || $nivel == '7a' || $nivel == '8a' || $nivel == '9a') {
				$mt = calculo::get_media_trimestral_6a9_ano_portugues_2018 ( $s1, $s2, $s3, $s4, $sim, $for );
				return $mt;
			}
		}
		
		// 2018 - NOVA regra de português do 1º ao 3º ANO campo S2 INGLÊS:
		if($ano >= 2018 && $disciplina == 'l') {
			if($nivel == '1a' || $nivel == '2a' || $nivel == '3a') {
				$mt = calculo::get_media_trimestral_1a3_ano_ingles_2018 ( $s1, $s2, $for );
				return $mt;
			}
		}
		
		
		
		
		
		

		if ($nivel == '4a' || $nivel == '5a') {
			if ($disciplina == 'p' || $disciplina == 'ma') {
				
				$mt = calculo::get_media_trimestral_tipo_3 ( $s1, $s2, $s3, $for );
				
				// echo "$s1, $s2, $s3, $for";
			} else if ($disciplina == 'h' || $disciplina == 'g' || $disciplina == 'c' || $disciplina == 'l' || $disciplina == 'e') {
				$mt = calculo::get_media_trimestral_tipo_2 ( $s1, $s2, $for );
			} else {
				$mt = calculo::get_media_trimestral_tipo_1 ( $s1, $for );
			}
		}
		
		
		if ($nivel == '6a' || $nivel == '7a' || $nivel == '8a') {
			if ($disciplina == 'p' || $disciplina == 'ma') {
				$mt = calculo::get_media_trimestral_tipo_5 ( $s1, $s2, $s3, $sim, $for );
			} else if ($disciplina == 'h' || $disciplina == 'g' || $disciplina == 'l' || $disciplina == 'c') {
				$mt = calculo::get_media_trimestral_tipo_4 ( $s1, $s2, $sim, $for );
			} else if ($disciplina == 'e') {
				$mt = calculo::get_media_trimestral_tipo_2 ( $s1, $s2, $for );
			} else {
				$mt = calculo::get_media_trimestral_tipo_1 ( $s1, $for );
			}
		}
		
		
		if ($nivel == '9a') {
			if ($disciplina == 'p' || $disciplina == 'ma' || $disciplina == 'c') {
				$mt = calculo::get_media_trimestral_tipo_5 ( $s1, $s2, $s3, $sim, $for );
			} else if ($disciplina == 'h' || $disciplina == 'g' || $disciplina == 'l') {
				$mt = calculo::get_media_trimestral_tipo_4 ( $s1, $s2, $sim, $for );
			} else if ($disciplina == 'e') {
				$mt = calculo::get_media_trimestral_tipo_2 ( $s1, $s2, $for );
			} else {
				$mt = calculo::get_media_trimestral_tipo_1 ( $s1, $for );
			}
		}
		
		return $mt;
	}

	private function popular_disciplina_possui_prova() {
		
		if($this->vetor_disciplina_possui_prova == null) {

			$q = (new SI_NivelDisciplinaProvaModel())->query("
				
			SELECT t.id, id_disciplina, id_prova
			FROM si_nivel_disciplina_prova p
				
			JOIN si_turma t
			ON p.id_nivel = t.id_nivel
			AND (
					(t.ano <= 2017 AND p.ano_vigencia = 2017)
				OR	(t.ano >= 2018 AND p.ano_vigencia = t.ano)
			)
			")->getResult();
			// echo "$q<br/>";
			
			$this->vetor_disciplina_possui_prova = (array)$q;
		}
		
	}

	public function limitar_decimais($nota, $com_virgula = true) {
		$cortado = number_format($nota, 1);
		if($com_virgula ){ 
			return str_replace('.', ',', $cortado);
		}
		return $cortado;
	}
}


class relatorio_media_nucleo_materia_pdf {

	

	private $largura_matricula = 19;
	private $largura_pagina = 190;
	private $largura_nome = 100;
	private $largura_faltas = 150;
	private $pdf;



	public function __construct() {
		
		$this->pdf = new FPDF();

	}

	public function set_vetores($turma, $ano, $vetor_medias_trimestrais) {

		print_r(@$vetor_alunos);

		
		$caminho_imagem = base_url().'/assets/admin/dist/img/logo_boletim.jpg';

		$this->pdf->AddPage();

		$this->pdf->Image($caminho_imagem, 10, 10, 95, 12);
		$this->pdf->Cell($this->largura_pagina, 5, '', 0, 1, 'C');
		$this->pdf->Ln();
		$this->pdf->Ln();
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', 16 );

		$this->pdf->Cell($this->largura_pagina, 7, mb_convert_encoding("MÉDIA DA TURMA POR MATÉRIA 1", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

		$this->pdf->SetFont ( 'Arial', 'B', 13 );
		$this->pdf->Cell($this->largura_pagina, 7, mb_convert_encoding("$turma - $ano", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

		


		$this->pdf->SetFont ( 'Arial', '', 8 );
		$this->pdf->Ln();
		$this->gerar_notas($vetor_medias_trimestrais);



		$this->pdf->Output('Media_turma.pdf', 'D');
	}


	private function gerar_notas($vetor_medias_trimestrais) {

		//print_r($vetor_medias_trimestrais);
		

		$largura_disciplina = 30;
		$largura_nota = 12;
		$altura = 10;
		
		$altura_topo = 6;
		

		$this->pdf->SetFont ( 'Arial', 'B', 9 );
		$this->pdf->Ln();
		$this->pdf->Cell($largura_disciplina, $altura_topo, mb_convert_encoding("Disciplina", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		$this->pdf->Cell($largura_nota, $altura_topo, mb_convert_encoding("1º T", 'ISO-8859-1', 'UTF-8'), 0, 0, 'R');
		$this->pdf->Cell($largura_nota, $altura_topo, mb_convert_encoding("2º T", 'ISO-8859-1', 'UTF-8'), 0, 0, 'R');
		$this->pdf->Cell($largura_nota, $altura_topo, mb_convert_encoding("3º T", 'ISO-8859-1', 'UTF-8'), 0, 0, 'R');
		

		$this->pdf->SetFont ( 'Arial', '', 9 );
		for($i=0; $i<=10; $i++) {
			//$this->pdf->SetX($this->pdf->GetX() + 10);
			$this->pdf->Cell(11, $altura_topo, $i, 0, 0, 'C');
		}
			
		$this->pdf->SetFont ( 'Arial', '', 8 );
		
		$i=0;
		foreach ($vetor_medias_trimestrais as $k => $v) {
			
			$materia_id = $k;
			$notas = $v;

			$nota_trim1 = $notas['1t'];
			$nota_trim2 = $notas['2t'];
			$nota_trim3 = $notas['3t'];
			
			$this->pdf->Ln();
			

			if($i % 2 == 0) {
				$this->pdf->SetFillColor(240);
				$this->pdf->Cell($largura_disciplina, $altura, mb_convert_encoding(parametro::get_disciplina($materia_id), 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
				$this->pdf->Cell($largura_nota, $altura, mb_convert_encoding($nota_trim1, 'ISO-8859-1', 'UTF-8'), 1, 0, 'R', true);
				$this->pdf->Cell($largura_nota, $altura, mb_convert_encoding($nota_trim2, 'ISO-8859-1', 'UTF-8'), 1, 0, 'R', true);
				$this->pdf->Cell($largura_nota, $altura, mb_convert_encoding($nota_trim3, 'ISO-8859-1', 'UTF-8'), 1, 0, 'R', true);
			}else{
				$this->pdf->Cell($largura_disciplina, $altura, mb_convert_encoding(parametro::get_disciplina($materia_id), 'ISO-8859-1', 'UTF-8'), 1, 0, 'L');
				$this->pdf->Cell($largura_nota, $altura, mb_convert_encoding($nota_trim1, 'ISO-8859-1', 'UTF-8'), 1, 0, 'R');
				$this->pdf->Cell($largura_nota, $altura, mb_convert_encoding($nota_trim2, 'ISO-8859-1', 'UTF-8'), 1, 0, 'R');
				$this->pdf->Cell($largura_nota, $altura, mb_convert_encoding($nota_trim3, 'ISO-8859-1', 'UTF-8'), 1, 0, 'R');
			}
			
			
			$px = $this->pdf->GetX();
			$py = $this->pdf->GetY();

			$this->pdf->SetDrawColor(220);
			
			if($i % 2 == 0) {
				$this->pdf->SetFillColor(240);
				$this->pdf->Cell(115, $altura, "", 1, 0, 'R', true);
			}else{
				$this->pdf->Cell(115, $altura, "", 1, 0, 'R');
			}
			
			$this->pdf->SetDrawColor(cor::$preto);

			$inicio = 5.5;
			$fim = 115;
			
			$distancia_total = $fim - $inicio; // 109,5 
			$multiplica = $distancia_total / 10; // 10,95
			
			
			
			$posicao_1trim = str_replace(",", ".", $nota_trim1) * $multiplica + $inicio;
			$posicao_2trim = str_replace(",", ".", $nota_trim2) * $multiplica + $inicio;
			$posicao_3trim = str_replace(",", ".", $nota_trim3) * $multiplica + $inicio; 
			
			
			$py_offset = 3.1;
			$distancia_entre = 1.4;
			
			$cor_destaque = cor::$escala_cinza_3;
			$this->traco10($posicao_1trim, $cor_destaque, $px, $py + $py_offset);


			
			$cor_destaque = cor::$escala_cinza_4;
			$this->traco10($posicao_2trim, $cor_destaque, $px, $py + $py_offset + $distancia_entre);
			
			
			$cor_destaque = cor::$preto;
			$this->traco10($posicao_3trim, $cor_destaque, $px, $py + $py_offset + $distancia_entre * 2);
			
			
			$this->pdf->SetY($py);

			$this->pdf->Cell(1, $altura, "", 0, 0, 'R');
			$i++;
		}
		
		$this->barras_escala_nota();
		
	}
	
	private function barras_escala_nota() {
		
		$px = 81.4;
		$py = 64;
		
		for($i=0; $i<=10; $i++) {
			$espaco = 11;

			$this->pdf->SetDrawColor(150);
			$this->pdf->Line($px + ($i * $espaco), $py, $px + ($i * $espaco), $py + 100);

			$this->pdf->SetDrawColor(cor::$preto);
		}
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
	

	private function traco10($comprimento, $cor_destaque, $x = null, $y = null) {
	
	
		$altura_linha = 0.5;
	
		if($x == null) {
			$x = $this->pdf->GetX();
		}
	
		$px_inicial = $x;
	
		if($y != null) {
			$this->pdf->SetY($y);
		}
	
		$this->pdf->SetX($px_inicial + @$i * @$comprimento_unitario);
	
		$this->pdf->SetFillColor($cor_destaque);
		$this->pdf->Cell($comprimento, $altura_linha, "", 0, 0, 'L', true);
	
	}
	
	
	
	private function traco1($comprimento, $cor_destaque, $x = null, $y = null) {
	
		$altura_linha = 1.2;
	
		$largura_cor1 = 2;
		$largura_sem_cor1 = 2;
	
		$largura_cor2 = 5;
		$largura_sem_cor2 = .8;
	
		$comprimento_unitario = $largura_cor1 + $largura_cor2 + $largura_sem_cor1 + $largura_sem_cor2;
	
		$real = $comprimento / $comprimento_unitario; // 14,28
		$qtde_loops = floor( $real ); // 100/7 = 14,28 = 14
		$restante = $comprimento_unitario * ( $real - $qtde_loops); // 14,28 - 14
	
		if($x == null) {
			$x = $this->pdf->GetX();
		}
	
		$px_inicial = $x;
	
		if($y != null) {
			$this->pdf->SetY($y);
		}
	
		for( $i = 0; $i < $qtde_loops; $i++ ) {
	
			$this->pdf->SetX($px_inicial + $i * $comprimento_unitario);
	
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($largura_cor1, $altura_linha, "", 0, 0, 'L', true);
	
			$this->pdf->SetFillColor(cor::$branco);
			$this->pdf->Cell($largura_sem_cor1 , $altura_linha , "", 0, 0, 'L', true);
	
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($largura_cor2, $altura_linha, "", 0, 0, 'L', true);
	
			$this->pdf->SetFillColor(cor::$branco);
			$this->pdf->Cell($largura_sem_cor2 , $altura_linha , "", 0, 0, 'L', true);
		}
	
		if($restante > 0.01) {
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($restante , $altura_linha , "", 0, 0, 'L', true);
		}
	}
	
	
	private function traco2($comprimento, $cor_destaque, $x = null, $y = null) {
	
		$altura_linha = 1.2;
	
		$largura_cor1 = 0.6;
		$largura_sem_cor1 = 1.6;
	
		$comprimento_unitario = $largura_cor1 + $largura_sem_cor1;
	
		$real = $comprimento / $comprimento_unitario; // 14,28
		$qtde_loops = floor( $real ); // 100/7 = 14,28 = 14
		$restante = $comprimento_unitario * ( $real - $qtde_loops); // 14,28 - 14
	
		if($x == null) {
			$x = $this->pdf->GetX();
		}
	
		$px_inicial = $x;
	
		if($y != null) {
			$this->pdf->SetY($y);
		}
	
		for( $i = 0; $i < $qtde_loops; $i++ ) {
	
			$this->pdf->SetX($px_inicial + $i * $comprimento_unitario);
	
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($largura_cor1, $altura_linha, "", 0, 0, 'L', true);
	
			$this->pdf->SetFillColor(cor::$branco);
			$this->pdf->Cell($largura_sem_cor1 , $altura_linha , "", 0, 0, 'L', true);
	
		}
	
		if($restante > 0.1) {
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($restante , $altura_linha , "", 0, 0, 'L', true);
		}
	}
	
	
	private function traco3($comprimento, $cor_destaque, $x = null, $y = null) {
	
		$altura_linha = 1.2;
	
		$largura_cor1 = 3.6;
		$largura_sem_cor1 = 0.01;
	
		$comprimento_unitario = $largura_cor1 + $largura_sem_cor1;
	
		$real = $comprimento / $comprimento_unitario; // 14,28
		$qtde_loops = floor( $real ); // 100/7 = 14,28 = 14
		$restante = $comprimento_unitario * ( $real - $qtde_loops); // 14,28 - 14
	
		if($x == null) {
			$x = $this->pdf->GetX();
		}
	
		$px_inicial = $x;
	
		if($y != null) {
			$this->pdf->SetY($y);
		}
	
		for( $i = 0; $i < $qtde_loops; $i++ ) {
	
			$this->pdf->SetX($px_inicial + $i * $comprimento_unitario);
	
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($largura_cor1, $altura_linha, "", 0, 0, 'L', true);
	
			$this->pdf->SetFillColor(cor::$branco);
			$this->pdf->Cell($largura_sem_cor1 , $altura_linha , "", 0, 0, 'L', true);
	
		}
	
		if($restante > 0.1) {
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($restante , $altura_linha , "", 0, 0, 'L', true);
		}
	}
	

	private function traco5($comprimento, $cor_destaque, $x = null, $y = null) {
	
		$altura_linha = 0.5;
	
		$largura_cor1 = 2;
		$largura_sem_cor1 = 2;
	
		$comprimento_unitario = $largura_cor1 + $largura_sem_cor1;
	
		$real = $comprimento / $comprimento_unitario; // 14,28
		$qtde_loops = floor( $real ); // 100/7 = 14,28 = 14
		$restante = $comprimento_unitario * ( $real - $qtde_loops); // 14,28 - 14
	
		if($x == null) {
			$x = $this->pdf->GetX();
		}
	
		$px_inicial = $x;
	
		if($y != null) {
			$this->pdf->SetY($y);
		}
	
		for( $i = 0; $i < $qtde_loops; $i++ ) {
	
			$this->pdf->SetX($px_inicial + $i * $comprimento_unitario);
	
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($largura_cor1, $altura_linha, "", 0, 0, 'L', true);
	
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($largura_sem_cor1 , $altura_linha , "", 0, 0, 'L', true);
	
		}
	
		if($restante > 0.1) {
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($restante , $altura_linha , "", 0, 0, 'L', true);
		}
	}
	
	private function traco4($comprimento, $cor_destaque, $x = null, $y = null) {
	
		$altura_linha = 1.2;
	
		$largura_cor1 = 8.6;
		$largura_sem_cor1 = 2;
	
		$comprimento_unitario = $largura_cor1 + $largura_sem_cor1;
	
		$real = $comprimento / $comprimento_unitario; // 14,28
		$qtde_loops = floor( $real ); // 100/7 = 14,28 = 14
		$restante = $comprimento_unitario * ( $real - $qtde_loops); // 14,28 - 14
	
		if($x == null) {
			$x = $this->pdf->GetX();
		}
	
		$px_inicial = $x;
	
		if($y != null) {
			$this->pdf->SetY($y);
		}
	
		for( $i = 0; $i < $qtde_loops; $i++ ) {
	
			$this->pdf->SetX($px_inicial + $i * $comprimento_unitario);
	
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($largura_cor1, $altura_linha, "", 0, 0, 'L', true);
	
			$this->pdf->SetFillColor(cor::$branco);
			$this->pdf->Cell($largura_sem_cor1 , $altura_linha , "", 0, 0, 'L', true);
	
		}
	
		if($restante > 0.1) {
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($restante , $altura_linha , "", 0, 0, 'L', true);
		}
	}
	
	

}


class relatorio_media_aluno_montar_turma{
	
	public function render($turma_id) {

		//echo '<pre>';
		
		//$turma_id = (int)util::GET('turma_id');

		$q = (new SI_AlunoTurmaModel())->query(
												"
													SELECT a.id aluno
													FROM si_aluno_turma at
													
													JOIN si_aluno a
													ON at.fk_aluno = a.id
													
													WHERE a.status = 1
													AND at.fk_turma = $turma_id
													
													ORDER BY a.nome
													
													"
											)->getResultArray();

											//dd($q);
		
		//echo $q;
		$r = $q;
		
		if($r != null) {
			
			$nota = new nota();
			
			
			$disciplinas = parametro::disciplinas();
			$vetor_media_da_turma = array();
			for($trimestre=1; $trimestre<=3; $trimestre++) {
				foreach ($disciplinas as $disciplina => $nome_disc) {
					$media = $nota->get_media_turma($turma_id, $disciplina, $trimestre);
					
					$vetor_media_da_turma[$trimestre . "t"][$disciplina] = $media;
				}
			}
			
			$vetor_alunos = array();
			foreach ($r as $v) {
				$aluno_id = $v;
				
				
				$vetor_aluno = array();
				
				
				//print_r($vetor_media_da_turma);
				//exit();
				
				$vetor_boletim_aluno = $nota->boletim_anual_aluno($aluno_id, $turma_id, $vetor_media_da_turma);

				
				array_push($vetor_alunos, $vetor_boletim_aluno);
				
			}
			/*
			echo '<pre>';
			print_r($vetor_alunos);
			exit();*/
			//echo 'PRIONTO';
			
			$pdf = new relatorio_media_aluno_pdf();
			$pdf->set_vetor($vetor_alunos);
			
			
			
		}else{
			echo 'Não existem alunos para esta turma.';
		}
		
		
		
		
		
		
		
		//$pdf->set_vetor($aluno_id);
		
		
	}
}


class relatorio_media_aluno_pdf {

	private $largura_matricula = 19;
	private $largura_pagina = 190;
	private $largura_nome = 100;
	private $largura_faltas = 150;
	private $pdf;



	public function __construct() {
		$this->pdf = new FPDF();
	}

	public function set_vetor($boletim_alunos) {
	/*
		echo '<pre>';
		print_r($boletim_alunos);
		exit();
		*/
		
		$this->gerar_notas_turma($boletim_alunos);

		$this->pdf->Output('Media_alunos.pdf', 'D');
	}


	private function gerar_notas_turma($boletim_alunos) {
		
		$caminho_imagem = base_url().'/assets/admin/dist/img/logo_boletim.jpg';
	
		//dd($boletim_alunos);
		foreach ($boletim_alunos as $boletim_do_aluno) {
			
			$nome_aluno = $boletim_do_aluno['nome'];
			$turma = $boletim_do_aluno['nome_turma'];
			$ano = $boletim_do_aluno['ano'];
				
			$this->pdf->AddPage();
	
			$this->pdf->Image($caminho_imagem, 10, 10, 95, 12);
			$this->pdf->Cell($this->largura_pagina, 5, '', 0, 1, 'C');
			$this->pdf->Ln();
			$this->pdf->Ln();
			$this->pdf->Ln();
			$this->pdf->SetFont ( 'Arial', '', 16 );
	
			$this->pdf->Cell($this->largura_pagina, 7, mb_convert_encoding("MÉDIA DO ALUNO POR MATÉRIA", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
	
			$this->pdf->SetFont ( 'Arial', 'B', 13 );
			$this->pdf->Cell($this->largura_pagina, 7, mb_convert_encoding("$turma - $ano", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
			$this->pdf->Cell($this->largura_pagina, 7, mb_convert_encoding("$nome_aluno", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
	
			
			$this->pdf->SetFont ( 'Arial', '', 8 );
			$this->pdf->Ln();
			$this->gerar_notas_aluno($boletim_do_aluno);
	
				
		}
		
		//print_r($vetor_medias_trimestrais);
		
	}
	
	
	private function gerar_notas_aluno($boletim_do_aluno) {
		
		$largura_disciplina = 30;
		$largura_nota = 12;
		$altura = 10;
		
		$altura_topo = 6;
		

		$this->pdf->SetFont ( 'Arial', 'B', 9 );
		$this->pdf->Ln();
		$this->pdf->Cell($largura_disciplina, $altura_topo, mb_convert_encoding("Disciplina", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		$this->pdf->Cell($largura_nota, $altura_topo, mb_convert_encoding("1º T", 'ISO-8859-1', 'UTF-8'), 0, 0, 'R');
		$this->pdf->Cell($largura_nota, $altura_topo, mb_convert_encoding("2º T", 'ISO-8859-1', 'UTF-8'), 0, 0, 'R');
		$this->pdf->Cell($largura_nota, $altura_topo, mb_convert_encoding("3º T", 'ISO-8859-1', 'UTF-8'), 0, 0, 'R');
		

		$this->pdf->SetFont ( 'Arial', '', 9 );
		for($i=0; $i<=10; $i++) {
			$this->pdf->Cell(11, $altura_topo, $i, 0, 0, 'C');
		}
		
		$this->pdf->SetFont ( 'Arial', '', 8 );
		
		
		$i=0;
		foreach ($boletim_do_aluno['notas'] as $nota) {
			
			
			$nota_trim1 = $nota['1t'];
			$nota_trim2 = $nota['2t'];
			$nota_trim3 = $nota['3t'];
			
			
			$this->pdf->Ln();
			

			if($i % 2 == 0) {
				$this->pdf->SetFillColor(240);
				$this->pdf->Cell($largura_disciplina, $altura, mb_convert_encoding($nota['disciplina'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
				$this->pdf->Cell($largura_nota, $altura, mb_convert_encoding($nota_trim1, 'ISO-8859-1', 'UTF-8'), 1, 0, 'R', true);
				$this->pdf->Cell($largura_nota, $altura, mb_convert_encoding($nota_trim2, 'ISO-8859-1', 'UTF-8'), 1, 0, 'R', true);
				$this->pdf->Cell($largura_nota, $altura, mb_convert_encoding($nota_trim3, 'ISO-8859-1', 'UTF-8'), 1, 0, 'R', true);
			}else{
				$this->pdf->Cell($largura_disciplina, $altura, mb_convert_encoding($nota['disciplina'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'L');
				$this->pdf->Cell($largura_nota, $altura, mb_convert_encoding($nota_trim1, 'ISO-8859-1', 'UTF-8'), 1, 0, 'R');
				$this->pdf->Cell($largura_nota, $altura, mb_convert_encoding($nota_trim2, 'ISO-8859-1', 'UTF-8'), 1, 0, 'R');
				$this->pdf->Cell($largura_nota, $altura, mb_convert_encoding($nota_trim3, 'ISO-8859-1', 'UTF-8'), 1, 0, 'R');
			}
			
			
			$px = $this->pdf->GetX();
			$py = $this->pdf->GetY();

			$this->pdf->SetDrawColor(220);
			
			if($i % 2 == 0) {
				$this->pdf->SetFillColor(240);
				$this->pdf->Cell(115, $altura, "", 1, 0, 'R', true);
			}else{
				$this->pdf->Cell(115, $altura, "", 1, 0, 'R');
			}
			
			$this->pdf->SetDrawColor(cor::$preto);

			$inicio = 5.5;
			$fim = 115;
			
			$distancia_total = $fim - $inicio; // 109,5 
			$multiplica = $distancia_total / 10; // 10,95
			
			
			$posicao_1trim = str_replace(",", ".", (float)$nota_trim1) * $multiplica + $inicio;
			$posicao_2trim = str_replace(",", ".", (float)$nota_trim2) * $multiplica + $inicio;
			$posicao_3trim = str_replace(",", ".", (float)$nota_trim3) * $multiplica + $inicio; 
			
			
			
			$media_turma_1t = $nota['1t_media_turma']* $multiplica + $inicio;
			$media_turma_2t = $nota['2t_media_turma']* $multiplica + $inicio;
			$media_turma_3t = $nota['3t_media_turma']* $multiplica + $inicio;
			
			
			$py_offset = 1.5;
			$distancia_entre = 2.8;
			
			$cor_destaque = cor::$escala_cinza_3;
			$this->traco10($posicao_1trim, $cor_destaque, $px, $py + $py_offset);
			$this->traco2($media_turma_1t, $cor_destaque, $px, $py + $py_offset + 1);


			
			$cor_destaque = cor::$escala_cinza_4;
			$this->traco10($posicao_2trim, $cor_destaque, $px, $py + $py_offset + $distancia_entre);
			$this->traco2($media_turma_2t, $cor_destaque, $px, $py + $py_offset + $distancia_entre + 1);
			
			
			$cor_destaque = cor::$preto;
			$this->traco10($posicao_3trim, $cor_destaque, $px, $py + $py_offset + $distancia_entre * 2);
			$this->traco2($media_turma_3t, $cor_destaque, $px, $py + $py_offset + $distancia_entre * 2 + 1);
			
			
			$this->pdf->SetY($py);

			$this->pdf->Cell(1, $altura, "", 0, 0, 'R');
			$i++;
		}
		
		$this->barras_escala_nota();
		$this->legenda();
	}
	
	private function legenda() {
		$this->pdf->Ln();
		$this->pdf->Ln();
		
		
		$this->pdf->SetFont ( 'Arial', 'B', 8 );
		$this->pdf->Cell(100, 7, "Legenda: ", 0, 1);
		
		$this->pdf->SetFont ( 'Arial', '', 8 );
		$this->pdf->Cell(100, 5, mb_convert_encoding("Linha contínua = nota do aluno;", 'ISO-8859-1', 'UTF-8'), 0, 1);
		$this->pdf->Cell(100, 5, mb_convert_encoding("Linha tracejada = média da turma.", 'ISO-8859-1', 'UTF-8'), 0, 1);
	}
	
	private function barras_escala_nota() {
		
		$px = 81.4;
		$py = 71;
		
		for($i=0; $i<=10; $i++) {
			$espaco = 11;

			$this->pdf->SetDrawColor(150);
			$this->pdf->Line($px + ($i * $espaco), $py, $px + ($i * $espaco), $py + 100);

			$this->pdf->SetDrawColor(cor::$preto);
		}
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
	

	private function traco10($comprimento, $cor_destaque, $x = null, $y = null) {
	
	
		$altura_linha = 0.5;
	
		if($x == null) {
			$x = $this->pdf->GetX();
		}
	
		$px_inicial = $x;
	
		if($y != null) {
			$this->pdf->SetY($y);
		}
	
		$this->pdf->SetX($px_inicial + @$i * @$comprimento_unitario);
	
		$this->pdf->SetFillColor($cor_destaque);
		$this->pdf->Cell($comprimento, $altura_linha, "", 0, 0, 'L', true);
	
	}
	
	
	
	private function traco1($comprimento, $cor_destaque, $x = null, $y = null) {
	
		$altura_linha = 1.2;
	
		$largura_cor1 = 2;
		$largura_sem_cor1 = 2;
	
		$largura_cor2 = 5;
		$largura_sem_cor2 = .8;
	
		$comprimento_unitario = $largura_cor1 + $largura_cor2 + $largura_sem_cor1 + $largura_sem_cor2;
	
		$real = $comprimento / $comprimento_unitario; // 14,28
		$qtde_loops = floor( $real ); // 100/7 = 14,28 = 14
		$restante = $comprimento_unitario * ( $real - $qtde_loops); // 14,28 - 14
	
		if($x == null) {
			$x = $this->pdf->GetX();
		}
	
		$px_inicial = $x;
	
		if($y != null) {
			$this->pdf->SetY($y);
		}
	
		for( $i = 0; $i < $qtde_loops; $i++ ) {
	
			$this->pdf->SetX($px_inicial + $i * $comprimento_unitario);
	
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($largura_cor1, $altura_linha, "", 0, 0, 'L', true);
	
			$this->pdf->SetFillColor(cor::$branco);
			$this->pdf->Cell($largura_sem_cor1 , $altura_linha , "", 0, 0, 'L', true);
	
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($largura_cor2, $altura_linha, "", 0, 0, 'L', true);
	
			$this->pdf->SetFillColor(cor::$branco);
			$this->pdf->Cell($largura_sem_cor2 , $altura_linha , "", 0, 0, 'L', true);
		}
	
		if($restante > 0.01) {
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($restante , $altura_linha , "", 0, 0, 'L', true);
		}
	}
	
	
	private function traco2($comprimento, $cor_destaque, $x = null, $y = null) {
	
		$altura_linha = 0.5;
	
		$largura_cor1 = 0.8;
		$largura_sem_cor1 = 0.3;
	
		$comprimento_unitario = $largura_cor1 + $largura_sem_cor1;
	
		$real = $comprimento / $comprimento_unitario; // 14,28
		$qtde_loops = floor( $real ); // 100/7 = 14,28 = 14
		$restante = $comprimento_unitario * ( $real - $qtde_loops); // 14,28 - 14
	
		if($x == null) {
			$x = $this->pdf->GetX();
		}
	
		$px_inicial = $x;
	
		if($y != null) {
			$this->pdf->SetY($y);
		}
	
		for( $i = 0; $i < $qtde_loops; $i++ ) {
	
			$this->pdf->SetX($px_inicial + $i * $comprimento_unitario);
	
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($largura_cor1, $altura_linha, "", 0, 0, 'L', true);
	
			$this->pdf->SetFillColor(cor::$branco);
			$this->pdf->Cell($largura_sem_cor1 , $altura_linha , "", 0, 0, 'L', true);
	
		}
	
		if($restante > 0.1) {
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($restante , $altura_linha , "", 0, 0, 'L', true);
		}
	}
	
	
	private function traco3($comprimento, $cor_destaque, $x = null, $y = null) {
	
		$altura_linha = 1.2;
	
		$largura_cor1 = 3.6;
		$largura_sem_cor1 = 0.01;
	
		$comprimento_unitario = $largura_cor1 + $largura_sem_cor1;
	
		$real = $comprimento / $comprimento_unitario; // 14,28
		$qtde_loops = floor( $real ); // 100/7 = 14,28 = 14
		$restante = $comprimento_unitario * ( $real - $qtde_loops); // 14,28 - 14
	
		if($x == null) {
			$x = $this->pdf->GetX();
		}
	
		$px_inicial = $x;
	
		if($y != null) {
			$this->pdf->SetY($y);
		}
	
		for( $i = 0; $i < $qtde_loops; $i++ ) {
	
			$this->pdf->SetX($px_inicial + $i * $comprimento_unitario);
	
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($largura_cor1, $altura_linha, "", 0, 0, 'L', true);
	
			$this->pdf->SetFillColor(cor::$branco);
			$this->pdf->Cell($largura_sem_cor1 , $altura_linha , "", 0, 0, 'L', true);
	
		}
	
		if($restante > 0.1) {
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($restante , $altura_linha , "", 0, 0, 'L', true);
		}
	}
	

	private function traco5($comprimento, $cor_destaque, $x = null, $y = null) {
	
		$altura_linha = 0.5;
	
		$largura_cor1 = 2;
		$largura_sem_cor1 = 2;
	
		$comprimento_unitario = $largura_cor1 + $largura_sem_cor1;
	
		$real = $comprimento / $comprimento_unitario; // 14,28
		$qtde_loops = floor( $real ); // 100/7 = 14,28 = 14
		$restante = $comprimento_unitario * ( $real - $qtde_loops); // 14,28 - 14
	
		if($x == null) {
			$x = $this->pdf->GetX();
		}
	
		$px_inicial = $x;
	
		if($y != null) {
			$this->pdf->SetY($y);
		}
	
		for( $i = 0; $i < $qtde_loops; $i++ ) {
	
			$this->pdf->SetX($px_inicial + $i * $comprimento_unitario);
	
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($largura_cor1, $altura_linha, "", 0, 0, 'L', true);
	
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($largura_sem_cor1 , $altura_linha , "", 0, 0, 'L', true);
	
		}
	
		if($restante > 0.1) {
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($restante , $altura_linha , "", 0, 0, 'L', true);
		}
	}
	
	private function traco4($comprimento, $cor_destaque, $x = null, $y = null) {
	
		$altura_linha = 1.2;
	
		$largura_cor1 = 8.6;
		$largura_sem_cor1 = 2;
	
		$comprimento_unitario = $largura_cor1 + $largura_sem_cor1;
	
		$real = $comprimento / $comprimento_unitario; // 14,28
		$qtde_loops = floor( $real ); // 100/7 = 14,28 = 14
		$restante = $comprimento_unitario * ( $real - $qtde_loops); // 14,28 - 14
	
		if($x == null) {
			$x = $this->pdf->GetX();
		}
	
		$px_inicial = $x;
	
		if($y != null) {
			$this->pdf->SetY($y);
		}
	
		for( $i = 0; $i < $qtde_loops; $i++ ) {
	
			$this->pdf->SetX($px_inicial + $i * $comprimento_unitario);
	
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($largura_cor1, $altura_linha, "", 0, 0, 'L', true);
	
			$this->pdf->SetFillColor(cor::$branco);
			$this->pdf->Cell($largura_sem_cor1 , $altura_linha , "", 0, 0, 'L', true);
	
		}
	
		if($restante > 0.1) {
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($restante , $altura_linha , "", 0, 0, 'L', true);
		}
	}

}


class visao_pagina_conteudo_relatorio__media_individual__nucleo_comum__montar{

	
	public function render($turma_id) {
	
		$pdf = new visao_pagina_conteudo_relatorio__media_individual__nucleo_comum__pdf();
	
	
		$turma = (new SI_TurmaModel())->find($turma_id);
		
		
		$disciplinas = parametro::disciplinas(true);
		
		$q = "
		
		SELECT a.id, matricula, a.nome
		FROM si_aluno a
	
		JOIN si_aluno_turma at
		ON a.id = at.fk_aluno
	
		JOIN si_turma t
		ON at.fk_turma = t.id
	
		WHERE
		a.status = '1'
		AND t.status = '1'
		AND t.id = $turma_id
	
		ORDER BY a.nome
		
		";
	
		//$r = $this->conn->qcv($q, "id,matricula,nome");
		$r = (new SI_AlunoModel())->query($q)->getResultArray();
		$nota = new nota();
		
		$qtde_disciplinas = sizeof($disciplinas);

		$vetor_resposta = array();
		$vetor_alunos = array();

		foreach($r as $v) {

			$aluno_id = $v['id'];

					
			$soma_media_1trim_todas_disc = 0.0;
			$soma_media_2trim_todas_disc = 0.0;
			$soma_media_3trim_todas_disc = 0.0;
				
				
			for($trimestre=1; $trimestre<=3; $trimestre++) {
				$nota_trim = 0;
				foreach ($disciplinas as $disciplina => $nome_disc) {
					$possui_nota = true;
						
					$nota_trim = $nota->get_media_trimestral_periodo_anual($turma->id_nivel, $aluno_id, $disciplina, $turma_id, $trimestre);
						
					if($nota_trim == parametro::nota_sem_valor()) {
					// não tem nota para esta disciplina.
			
						$possui_nota = false;
						break;
					}
					if($possui_nota) {
						
						$nota_trim = str_replace(",", ".", $nota_trim);
							
							
						if($trimestre == 1) {
							$soma_media_1trim_todas_disc += $nota_trim;
								
						}else if($trimestre == 2) {
							$soma_media_2trim_todas_disc += $nota_trim;
								
						}else if($trimestre == 3) {
							$soma_media_3trim_todas_disc += $nota_trim;
								
						}
					}else{
						if($trimestre == 1) {
							$soma_media_1trim_todas_disc = 0;
						}
						if($trimestre == 2) {
							$soma_media_2trim_todas_disc = 0;
						}
						if($trimestre == 3) {
							$soma_media_3trim_todas_disc = 0;
						}
					}
				}
			}
				
			$vetor_aluno = array();
				
			$vetor_aluno['matricula'] = $v['matricula'];
			$vetor_aluno['nome'] = $v['nome'];
			
			$vetor_aluno['1t'] = number_format( $soma_media_1trim_todas_disc / $qtde_disciplinas, 1 );
			$vetor_aluno['2t'] = number_format( $soma_media_2trim_todas_disc / $qtde_disciplinas, 1 );
			$vetor_aluno['3t'] = number_format( $soma_media_3trim_todas_disc / $qtde_disciplinas, 1 );
	

			$vetor_aluno['1t'] = calculo::arredonda_nota($vetor_aluno['1t']);
			$vetor_aluno['2t'] = calculo::arredonda_nota($vetor_aluno['2t']);
			$vetor_aluno['3t'] = calculo::arredonda_nota($vetor_aluno['3t']);
			
			array_push($vetor_alunos, $vetor_aluno);
			
		}
		$vetor_resposta['turma'] = $turma->nome . " - " . $turma->ano;
		$vetor_resposta['alunos'] = $vetor_alunos;
		
		
		
		

		$pdf->set_vetores($vetor_resposta);
	
	}
}

class visao_pagina_conteudo_relatorio__media_individual__nucleo_comum__pdf {

	private $largura_matricula = 19;
	private $largura_pagina = 190;
	private $largura_nome = 100;
	private $largura_faltas = 150;
	private $pdf;
	
	
	
	private $altura_linha = 4.6;
	
	
	public function __construct() {
		$this->pdf = new FPDF();
	}
	
	public function set_vetores($vetor_resposta)
	{
		/*
			print_r($vetor_resposta);
		exit();
		*/
	
		$caminho_imagem = base_url().'/assets/admin/dist/img/logo_boletim.jpg';
	
		$this->pdf->AddPage('L');
	
		$this->pdf->Image($caminho_imagem, 10, 15, 95, 12);
	
		$this->pdf->Cell($this->largura_pagina, 5, '', 0, 1, 'C');
		$this->pdf->SetFont ( 'Arial', '', 16 );
	
		$this->pdf->Cell(70, 7, "", 0, 0, 'C');
		$this->pdf->Cell($this->largura_pagina, 7, mb_convert_encoding("MÉDIA INDIVIDUAL - NÚCLEO COMUM", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
		$this->pdf->Cell(70, 7, "", 0, 0, 'C');
		$this->pdf->Cell($this->largura_pagina, 7, mb_convert_encoding($vetor_resposta["turma"], 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
	
		$this->pdf->SetFont ( 'Arial', 'B', 10 );
	
		$altura_linha = 6;
		$largura_matricula = 20;
		$largura_nome = 80;
		$largura_notatrim = 10;
	
	
		$this->pdf->Ln();
		$this->pdf->Cell($largura_matricula, $altura_linha, mb_convert_encoding("Matrícula", 'ISO-8859-1', 'UTF-8'), "B", 0, 'L');
		$this->pdf->Cell($largura_nome, $altura_linha, mb_convert_encoding("Nome", 'ISO-8859-1', 'UTF-8'), "B", 0, 'L');
		$this->pdf->Cell($largura_notatrim, $altura_linha, mb_convert_encoding("1º T", 'ISO-8859-1', 'UTF-8'), "B", 0, 'R');
		$this->pdf->Cell($largura_notatrim, $altura_linha, mb_convert_encoding("2º T", 'ISO-8859-1', 'UTF-8'), "B", 0, 'R');
		$this->pdf->Cell($largura_notatrim, $altura_linha, mb_convert_encoding("3º T", 'ISO-8859-1', 'UTF-8'), "B", 0, 'R');
	
		for($i=0;$i<=10;$i++)
		{
		$this->pdf->Cell(12, $altura_linha, $i, 0, 0, 'R');
		}
	
		$this->pdf->SetFont ( 'Arial', '', 8 );
	
		$qtde_alunos = 0;
	
	
		foreach ($vetor_resposta['alunos'] as $aluno) {
			
			
		$mat = $aluno['matricula'];
		$nome = $aluno['nome'];
		$trim1 = $aluno['1t'];
		$trim2 = $aluno['2t'];
		$trim3 = $aluno['3t'];
			
			
		$trim1_exibir = str_replace(".", ",", $trim1);
		$trim2_exibir = str_replace(".", ",", $trim2);
		$trim3_exibir = str_replace(".", ",", $trim3);
			
			
		$this->pdf->Ln();
		$this->pdf->Cell($largura_matricula, $altura_linha, mb_convert_encoding($mat, 'ISO-8859-1', 'UTF-8'), "B", 0, 'L');
		$this->pdf->Cell($largura_nome, $altura_linha, $this->cortar_texto(mb_convert_encoding($nome, 'ISO-8859-1', 'UTF-8'), $largura_nome),"B", 0, 'L');
		$this->pdf->Cell($largura_notatrim, $altura_linha, $trim1_exibir, "B", 0, 'R');
		$this->pdf->Cell($largura_notatrim, $altura_linha, $trim2_exibir, "B", 0, 'R');
		$this->pdf->Cell($largura_notatrim, $altura_linha, $trim3_exibir, "B", 0, 'R');
			
		$px = $this->pdf->GetX();
				$py = $this->pdf->GetY();
					
				$posX_offset = 3;
				$posY_offset = 1.4;
			
		$this->traco10($this->get_comprimento_barra_nota($trim1), 150, $px + $posX_offset, $py + $posY_offset);
		$this->traco10($this->get_comprimento_barra_nota($trim2), 100, $px + $posX_offset, $py + $posY_offset + 1);
		$this->traco10($this->get_comprimento_barra_nota($trim3), 50,  $px + $posX_offset, $py + $posY_offset + 2);
			
		$this->pdf->Cell(10,$altura_linha);
		$this->pdf->SetXY($px, $py);
			
		$qtde_alunos++;
			
		}
	
	
		$this->pdf->Ln();
		$this->barras_escala_nota($qtde_alunos);
	
		$this->pdf->Output('Media_individual_nucleo_comum.pdf', 'D');
	}
	
	private function get_comprimento_barra_nota($nota) {

	$c = (float)$nota * 12 + 7;
	return $c;
	}
	
	
	private function barras_escala_nota($qtde_linhas) {
	
	$px = 150;
	$py = 42.5;
	
	$altura = $qtde_linhas * 6;
	
	for($i=0; $i<=10; $i++) {
		$espaco = 12;
	
		$this->pdf->SetDrawColor(200);
		$this->pdf->Line($px + ($i * $espaco), $py, $px + ($i * $espaco), $py + $altura);
	
		$this->pdf->SetDrawColor(cor::$preto);
		}
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
	
		private function traco10($comprimento, $cor_destaque, $x = null, $y = null) {
	
	
		$altura_linha = 0.5;
	
		if($x == null) {
		$x = $this->pdf->GetX();
	}
	
	$px_inicial = $x;
	
	if($y != null) {
	$this->pdf->SetY($y);
		}
	
		$this->pdf->SetX($px_inicial + @$i * @$comprimento_unitario);
	
		$this->pdf->SetFillColor($cor_destaque);
		$this->pdf->Cell($comprimento, $altura_linha, "", 0, 0, 'L', true);
	
		}
	
	
	
		private function traco1($comprimento, $cor_destaque, $x = null, $y = null) {
	
		$altura_linha = 1.2;
	
		$largura_cor1 = 2;
		$largura_sem_cor1 = 2;
	
		$largura_cor2 = 5;
		$largura_sem_cor2 = .8;
	
		$comprimento_unitario = $largura_cor1 + $largura_cor2 + $largura_sem_cor1 + $largura_sem_cor2;
	
		$real = $comprimento / $comprimento_unitario; // 14,28
		$qtde_loops = floor( $real ); // 100/7 = 14,28 = 14
		$restante = $comprimento_unitario * ( $real - $qtde_loops); // 14,28 - 14
	
		if($x == null) {
		$x = $this->pdf->GetX();
		}
	
		$px_inicial = $x;
	
		if($y != null) {
		$this->pdf->SetY($y);
		}
	
		for( $i = 0; $i < $qtde_loops; $i++ ) {
	
		$this->pdf->SetX($px_inicial + $i * $comprimento_unitario);
	
		$this->pdf->SetFillColor($cor_destaque);
		$this->pdf->Cell($largura_cor1, $altura_linha, "", 0, 0, 'L', true);
	
		$this->pdf->SetFillColor(cor::$branco);
		$this->pdf->Cell($largura_sem_cor1 , $altura_linha , "", 0, 0, 'L', true);
	
		$this->pdf->SetFillColor($cor_destaque);
		$this->pdf->Cell($largura_cor2, $altura_linha, "", 0, 0, 'L', true);
	
		$this->pdf->SetFillColor(cor::$branco);
		$this->pdf->Cell($largura_sem_cor2 , $altura_linha , "", 0, 0, 'L', true);
		}
	
		if($restante > 0.01) {
		$this->pdf->SetFillColor($cor_destaque);
		$this->pdf->Cell($restante , $altura_linha , "", 0, 0, 'L', true);
		}
		}
	
	
		private function traco2($comprimento, $cor_destaque, $x = null, $y = null) {
	
		$altura_linha = 1.2;
	
		$largura_cor1 = 0.6;
		$largura_sem_cor1 = 1.6;
	
		$comprimento_unitario = $largura_cor1 + $largura_sem_cor1;
	
		$real = $comprimento / $comprimento_unitario; // 14,28
		$qtde_loops = floor( $real ); // 100/7 = 14,28 = 14
		$restante = $comprimento_unitario * ( $real - $qtde_loops); // 14,28 - 14
	
		if($x == null) {
		$x = $this->pdf->GetX();
		}
	
		$px_inicial = $x;
	
		if($y != null) {
		$this->pdf->SetY($y);
		}
	
		for( $i = 0; $i < $qtde_loops; $i++ ) {
	
		$this->pdf->SetX($px_inicial + $i * $comprimento_unitario);
	
		$this->pdf->SetFillColor($cor_destaque);
		$this->pdf->Cell($largura_cor1, $altura_linha, "", 0, 0, 'L', true);
	
		$this->pdf->SetFillColor(cor::$branco);
		$this->pdf->Cell($largura_sem_cor1 , $altura_linha , "", 0, 0, 'L', true);
	
		}
	
		if($restante > 0.1) {
		$this->pdf->SetFillColor($cor_destaque);
		$this->pdf->Cell($restante , $altura_linha , "", 0, 0, 'L', true);
		}
		}
	
	
		private function traco3($comprimento, $cor_destaque, $x = null, $y = null) {
	
			$altura_linha = 0.5;
	
			$largura_cor1 = 3.6;
			$largura_sem_cor1 = 0.1;
	
			$comprimento_unitario = $largura_cor1 + $largura_sem_cor1;
	
			$real = $comprimento / $comprimento_unitario; // 14,28
			$qtde_loops = floor( $real ); // 100/7 = 14,28 = 14
			$restante = $comprimento_unitario * ( $real - $qtde_loops); // 14,28 - 14
	
			if($x == null) {
			$x = $this->pdf->GetX();
			}
	
			$px_inicial = $x;
	
			if($y != null) {
			$this->pdf->SetY($y);
			}
	
			for( $i = 0; $i < $qtde_loops; $i++ ) {
	
			$this->pdf->SetX($px_inicial + $i * $comprimento_unitario);
	
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($largura_cor1, $altura_linha, "", 0, 0, 'L', true);
	
			$this->pdf->SetFillColor(cor::$branco);
			$this->pdf->Cell($largura_sem_cor1 , $altura_linha , "", 0, 0, 'L', true);
	
			}
	
			if($restante > 0.1) {
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($restante , $altura_linha , "", 0, 0, 'L', true);
			}
			}
	
			private function traco4($comprimento, $cor_destaque, $x = null, $y = null) {
	
			$altura_linha = 1.2;
	
			$largura_cor1 = 8.6;
				$largura_sem_cor1 = 2;
	
				$comprimento_unitario = $largura_cor1 + $largura_sem_cor1;
	
				$real = $comprimento / $comprimento_unitario; // 14,28
				$qtde_loops = floor( $real ); // 100/7 = 14,28 = 14
					$restante = $comprimento_unitario * ( $real - $qtde_loops); // 14,28 - 14
	
					if($x == null) {
					$x = $this->pdf->GetX();
				}
	
					$px_inicial = $x;
	
					if($y != null) {
					$this->pdf->SetY($y);
				}
	
				for( $i = 0; $i < $qtde_loops; $i++ ) {
	
				$this->pdf->SetX($px_inicial + $i * $comprimento_unitario);
	
				$this->pdf->SetFillColor($cor_destaque);
				$this->pdf->Cell($largura_cor1, $altura_linha, "", 0, 0, 'L', true);
	
				$this->pdf->SetFillColor(cor::$branco);
				$this->pdf->Cell($largura_sem_cor1 , $altura_linha , "", 0, 0, 'L', true);
	
				}
	
				if($restante > 0.1) {
				$this->pdf->SetFillColor($cor_destaque);
				$this->pdf->Cell($restante , $altura_linha , "", 0, 0, 'L', true);
				}
				}
	
	
	
}



class visao_pagina_conteudo_relatorio__media_individual__todas_disciplinas__montar{
	
	
	public function render($turma_id) {

		$pdf = new visao_pagina_conteudo_relatorio__media_individual__todas_disciplinas__pdf();
		
		
		$turma = (new SI_TurmaModel())->find($turma_id);
		
		
		$disciplinas = parametro::disciplinas();
		
		$q = "
				SELECT a.id, matricula, a.nome
				FROM si_aluno a
				
				JOIN si_aluno_turma at
				ON a.id = at.fk_aluno
				
				JOIN si_turma t
				ON at.fk_turma = t.id
				
				WHERE 
				a.status = '1'
				AND t.status = '1'
				AND t.id = $turma_id
				
				ORDER BY a.nome
				
				";
		
		$r = (new SI_AlunoModel())->query($q)->getResultArray();
		$nota = new nota();
		

		$qtde_disciplinas = sizeof($disciplinas);
		
		$vetor_resposta = array();
		$vetor_alunos = array();
		
		foreach($r as $v) {

			$aluno_id = $v['id'];
			
			$soma_media_1trim_todas_disc = 0.0;
			$soma_media_2trim_todas_disc = 0.0;
			$soma_media_3trim_todas_disc = 0.0;
			
			
			for($trimestre=1; $trimestre<=3; $trimestre++) {
				$nota_trim = 0;
				foreach ($disciplinas as $disciplina => $nome_disc) {
					$possui_nota = true;
					
					$nota_trim = $nota->get_media_trimestral_periodo_anual($turma->id_nivel, $aluno_id, $disciplina, $turma_id, $trimestre);
					
					if($nota_trim == parametro::nota_sem_valor()) {
						// não tem nota para esta disciplina.
						
						$possui_nota = false;
						break;
					}
					if($possui_nota) {
							
						$nota_trim = str_replace(",", ".", $nota_trim);
							
							
						if($trimestre == 1) {
							$soma_media_1trim_todas_disc += $nota_trim;
							
						}else if($trimestre == 2) {
							$soma_media_2trim_todas_disc += $nota_trim;
							
						}else if($trimestre == 3) {
							$soma_media_3trim_todas_disc += $nota_trim;
							
						}
					}else{
						if($trimestre == 1) {
							$soma_media_1trim_todas_disc = 0;
						}
						if($trimestre == 2) {
							$soma_media_2trim_todas_disc = 0;
						}
						if($trimestre == 3) {
							$soma_media_3trim_todas_disc = 0;
						}
					}
				}
			}
			
			$vetor_aluno = array();
			
			$vetor_aluno['matricula'] = $v['matricula'];
			$vetor_aluno['nome'] = $v['nome'];
			$vetor_aluno['1t'] = number_format( $soma_media_1trim_todas_disc / $qtde_disciplinas, 1 );
			$vetor_aluno['2t'] = number_format( $soma_media_2trim_todas_disc / $qtde_disciplinas, 1 );
			$vetor_aluno['3t'] = number_format( $soma_media_3trim_todas_disc / $qtde_disciplinas, 1 );

			$vetor_aluno['1t'] = calculo::arredonda_nota($vetor_aluno['1t']);
			$vetor_aluno['2t'] = calculo::arredonda_nota($vetor_aluno['2t']);
			$vetor_aluno['3t'] = calculo::arredonda_nota($vetor_aluno['3t']);
				
			array_push($vetor_alunos, $vetor_aluno);
			
		}
		$vetor_resposta['turma'] = $turma->nome . " - " . $turma->ano;
		$vetor_resposta['alunos'] = $vetor_alunos;
		
		
		
		
		
		$pdf->set_vetores($vetor_resposta);
		
	}
}


class visao_pagina_conteudo_relatorio__media_individual__todas_disciplinas__pdf {

	private $largura_matricula = 19;
	private $largura_pagina = 190;
	private $largura_nome = 100;
	private $largura_faltas = 150;
	private $pdf;
	
	
	
	private $altura_linha = 4.6;
	
	
	public function __construct() {
		$this->pdf = new FPDF();
	}
	
	public function set_vetores($vetor_resposta)
	{
		/*
		print_r($vetor_resposta);
		exit();
		*/
		
		$caminho_imagem = base_url().'/assets/admin/dist/img/logo_boletim.jpg';
	
		$this->pdf->AddPage('L');
	
		$this->pdf->Image($caminho_imagem, 10, 15, 95, 12);
	
		$this->pdf->Cell($this->largura_pagina, 5, '', 0, 1, 'C');
		$this->pdf->SetFont ( 'Arial', '', 16 );
	
		$this->pdf->Cell(70, 7, "", 0, 0, 'C');
		$this->pdf->Cell($this->largura_pagina, 7, mb_convert_encoding("MÉDIA INDIVIDUAL - TODAS DISCIPLINAS", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
		$this->pdf->Cell(70, 7, "", 0, 0, 'C');
		$this->pdf->Cell($this->largura_pagina, 7, mb_convert_encoding($vetor_resposta["turma"], 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
		
		$this->pdf->SetFont ( 'Arial', 'B', 10 );
		
		$altura_linha = 6;
		$largura_matricula = 20;
		$largura_nome = 80;
		$largura_notatrim = 10;
	
	
		$this->pdf->Ln();
		$this->pdf->Cell($largura_matricula, $altura_linha, mb_convert_encoding("Matrícula", 'ISO-8859-1', 'UTF-8'), "B", 0, 'L');
		$this->pdf->Cell($largura_nome, $altura_linha, mb_convert_encoding("Nome", 'ISO-8859-1', 'UTF-8'), "B", 0, 'L');
		$this->pdf->Cell($largura_notatrim, $altura_linha, mb_convert_encoding("1º T", 'ISO-8859-1', 'UTF-8'), "B", 0, 'R');
		$this->pdf->Cell($largura_notatrim, $altura_linha, mb_convert_encoding("2º T", 'ISO-8859-1', 'UTF-8'), "B", 0, 'R');
		$this->pdf->Cell($largura_notatrim, $altura_linha, mb_convert_encoding("3º T", 'ISO-8859-1', 'UTF-8'), "B", 0, 'R');
	
		for($i=0;$i<=10;$i++) 
		{
			$this->pdf->Cell(12, $altura_linha, $i, 0, 0, 'R');
		}
		
		$this->pdf->SetFont ( 'Arial', '', 8 );
	
		$qtde_alunos = 0;
	
		
		foreach ($vetor_resposta['alunos'] as $aluno) {
			
			
			$mat = $aluno['matricula'];
			$nome = $aluno['nome'];
			$trim1 = $aluno['1t'];
			$trim2 = $aluno['2t'];
			$trim3 = $aluno['3t'];
			
			
			$trim1_exibir = str_replace(".", ",", $trim1);
			$trim2_exibir = str_replace(".", ",", $trim2);
			$trim3_exibir = str_replace(".", ",", $trim3);
			
			
			$this->pdf->Ln();
			$this->pdf->Cell($largura_matricula, $altura_linha, mb_convert_encoding($mat, 'ISO-8859-1', 'UTF-8'), "B", 0, 'L');
			$this->pdf->Cell($largura_nome, $altura_linha, $this->cortar_texto(mb_convert_encoding($nome, 'ISO-8859-1', 'UTF-8'), $largura_nome),"B", 0, 'L');
			$this->pdf->Cell($largura_notatrim, $altura_linha, $trim1_exibir, "B", 0, 'R');
			$this->pdf->Cell($largura_notatrim, $altura_linha, $trim2_exibir, "B", 0, 'R');
			$this->pdf->Cell($largura_notatrim, $altura_linha, $trim3_exibir, "B", 0, 'R');
			
			$px = $this->pdf->GetX();
			$py = $this->pdf->GetY();
			
			$posX_offset = 3;
			$posY_offset = 1.4;
			
			$this->traco10($this->get_comprimento_barra_nota($trim1), 150, $px + $posX_offset, $py + $posY_offset);
			$this->traco10($this->get_comprimento_barra_nota($trim2), 100, $px + $posX_offset, $py + $posY_offset + 1);
			$this->traco10($this->get_comprimento_barra_nota($trim3), 50,  $px + $posX_offset, $py + $posY_offset + 2);
			
			$this->pdf->Cell(10,$altura_linha);
			$this->pdf->SetXY($px, $py);
			
			$qtde_alunos++;
			
		}
		
		
		$this->pdf->Ln();
		$this->barras_escala_nota($qtde_alunos);
	
		$this->pdf->Output('Media_individual_todas_as_disciplinas.pdf', 'D');
	}
	
	private function get_comprimento_barra_nota($nota) {
		$c = (float)$nota * 12 + 7;
		return $c;
	}
	
	
	private function barras_escala_nota($qtde_linhas) {
	
		$px = 150;
		$py = 42.5;
	
		$altura = $qtde_linhas * 6;
	
		for($i=0; $i<=10; $i++) {
			$espaco = 12;
	
			$this->pdf->SetDrawColor(200);
			$this->pdf->Line($px + ($i * $espaco), $py, $px + ($i * $espaco), $py + $altura);
	
			$this->pdf->SetDrawColor(cor::$preto);
		}
	}
	
	
	
	
	private function traco10($comprimento, $cor_destaque, $x = null, $y = null) {
	
	
		$altura_linha = 0.5;
	
		if($x == null) {
			$x = $this->pdf->GetX();
		}
	
		$px_inicial = $x;
	
		if($y != null) {
			$this->pdf->SetY($y);
		}
	
		$this->pdf->SetX($px_inicial + @$i * @$comprimento_unitario);
	
		$this->pdf->SetFillColor($cor_destaque);
		$this->pdf->Cell($comprimento, $altura_linha, "", 0, 0, 'L', true);
	
	}
	
	
	
	private function traco1($comprimento, $cor_destaque, $x = null, $y = null) {
	
		$altura_linha = 1.2;
	
		$largura_cor1 = 2;
		$largura_sem_cor1 = 2;
	
		$largura_cor2 = 5;
		$largura_sem_cor2 = .8;
	
		$comprimento_unitario = $largura_cor1 + $largura_cor2 + $largura_sem_cor1 + $largura_sem_cor2;
	
		$real = $comprimento / $comprimento_unitario; // 14,28
		$qtde_loops = floor( $real ); // 100/7 = 14,28 = 14
		$restante = $comprimento_unitario * ( $real - $qtde_loops); // 14,28 - 14
	
		if($x == null) {
			$x = $this->pdf->GetX();
		}
	
		$px_inicial = $x;
	
		if($y != null) {
			$this->pdf->SetY($y);
		}
	
		for( $i = 0; $i < $qtde_loops; $i++ ) {
	
			$this->pdf->SetX($px_inicial + $i * $comprimento_unitario);
	
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($largura_cor1, $altura_linha, "", 0, 0, 'L', true);
	
			$this->pdf->SetFillColor(cor::$branco);
			$this->pdf->Cell($largura_sem_cor1 , $altura_linha , "", 0, 0, 'L', true);
	
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($largura_cor2, $altura_linha, "", 0, 0, 'L', true);
	
			$this->pdf->SetFillColor(cor::$branco);
			$this->pdf->Cell($largura_sem_cor2 , $altura_linha , "", 0, 0, 'L', true);
		}
	
		if($restante > 0.01) {
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($restante , $altura_linha , "", 0, 0, 'L', true);
		}
	}
	
	
	private function traco2($comprimento, $cor_destaque, $x = null, $y = null) {
	
		$altura_linha = 1.2;
	
		$largura_cor1 = 0.6;
		$largura_sem_cor1 = 1.6;
	
		$comprimento_unitario = $largura_cor1 + $largura_sem_cor1;
	
		$real = $comprimento / $comprimento_unitario; // 14,28
		$qtde_loops = floor( $real ); // 100/7 = 14,28 = 14
		$restante = $comprimento_unitario * ( $real - $qtde_loops); // 14,28 - 14
	
		if($x == null) {
			$x = $this->pdf->GetX();
		}
	
		$px_inicial = $x;
	
		if($y != null) {
			$this->pdf->SetY($y);
		}
	
		for( $i = 0; $i < $qtde_loops; $i++ ) {
	
			$this->pdf->SetX($px_inicial + $i * $comprimento_unitario);
	
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($largura_cor1, $altura_linha, "", 0, 0, 'L', true);
	
			$this->pdf->SetFillColor(cor::$branco);
			$this->pdf->Cell($largura_sem_cor1 , $altura_linha , "", 0, 0, 'L', true);
	
		}
	
		if($restante > 0.1) {
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($restante , $altura_linha , "", 0, 0, 'L', true);
		}
	}
	
	
	private function traco3($comprimento, $cor_destaque, $x = null, $y = null) {
	
		$altura_linha = 0.5;
	
		$largura_cor1 = 3.6;
		$largura_sem_cor1 = 0.1;
	
		$comprimento_unitario = $largura_cor1 + $largura_sem_cor1;
	
		$real = $comprimento / $comprimento_unitario; // 14,28
		$qtde_loops = floor( $real ); // 100/7 = 14,28 = 14
		$restante = $comprimento_unitario * ( $real - $qtde_loops); // 14,28 - 14
	
		if($x == null) {
			$x = $this->pdf->GetX();
		}
	
		$px_inicial = $x;
	
		if($y != null) {
			$this->pdf->SetY($y);
		}
	
		for( $i = 0; $i < $qtde_loops; $i++ ) {
	
			$this->pdf->SetX($px_inicial + $i * $comprimento_unitario);
	
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($largura_cor1, $altura_linha, "", 0, 0, 'L', true);
	
			$this->pdf->SetFillColor(cor::$branco);
			$this->pdf->Cell($largura_sem_cor1 , $altura_linha , "", 0, 0, 'L', true);
	
		}
	
		if($restante > 0.1) {
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($restante , $altura_linha , "", 0, 0, 'L', true);
		}
	}
	
	private function traco4($comprimento, $cor_destaque, $x = null, $y = null) {
	
		$altura_linha = 1.2;
	
		$largura_cor1 = 8.6;
		$largura_sem_cor1 = 2;
	
		$comprimento_unitario = $largura_cor1 + $largura_sem_cor1;
	
		$real = $comprimento / $comprimento_unitario; // 14,28
		$qtde_loops = floor( $real ); // 100/7 = 14,28 = 14
		$restante = $comprimento_unitario * ( $real - $qtde_loops); // 14,28 - 14
	
		if($x == null) {
			$x = $this->pdf->GetX();
		}
	
		$px_inicial = $x;
	
		if($y != null) {
			$this->pdf->SetY($y);
		}
	
		for( $i = 0; $i < $qtde_loops; $i++ ) {
	
			$this->pdf->SetX($px_inicial + $i * $comprimento_unitario);
	
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($largura_cor1, $altura_linha, "", 0, 0, 'L', true);
	
			$this->pdf->SetFillColor(cor::$branco);
			$this->pdf->Cell($largura_sem_cor1 , $altura_linha , "", 0, 0, 'L', true);
	
		}
	
		if($restante > 0.1) {
			$this->pdf->SetFillColor($cor_destaque);
			$this->pdf->Cell($restante , $altura_linha , "", 0, 0, 'L', true);
		}
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
	
	
	
	
	}

	

class monta_pdf_boletim_turma extends monta_pdf_boletim_aluno{

	public $nota;

	public function render($turma_id, $aluno_id = null) {

		$this->nota = new nota();
	
		//$turma_id = 145;
		//$turma_id = (int)util::GET('turma_id');
	
		// o boletim com gráfico é dos anos 3 ao 9:
		//$turma = EASYNC5__si_turma::getByPK($turma_id);
		$turma = (new SI_TurmaModel())->find($turma_id);
		$nivel = $turma->id_nivel;
	
		if($nivel == '1a' || $nivel == '2a' || $nivel == '3a') {
			$r = $this->boletim_anual_turma($turma_id, $aluno_id);
	
			//$pdf_turma = new pdf_turma_com_grafico();
			$pdf_turma = new pdf_turma_sem_grafico();
			$pdf_turma->gerar_turma($r);
		}else{

			
			

			$pdf_aluno = new relatorio_tcpdf_boletim_individual();
			$pdf_aluno->create();

			$disciplinas = parametro::disciplinas();
			
			$vetor_media_da_turma = array();
			for($trimestre=1; $trimestre<=3; $trimestre++) {
				foreach ($disciplinas as $disciplina => $nome_disc) {
					$media = $this->nota->get_media_turma($turma_id, $disciplina, $trimestre);
					$vetor_media_da_turma[$trimestre . "t"][$disciplina] = $media;
				}
			}
			$qAluno = !empty($aluno_id) ? 'and at.fk_aluno = '.$aluno_id : '';
			$q = "SELECT fk_aluno FROM si_aluno_turma at 
					JOIN si_aluno a ON a.id = at.fk_aluno
					WHERE fk_turma = $turma_id
					".$qAluno."
					ORDER BY a.nome
					";
					
			//$alunos = $this->conn->qcv($q, "fk_aluno");
			$alunos = (new SI_AlunoTurmaModel())->query($q)->getResultArray();
			
			foreach($alunos as $v) {
				//parent::__construct($v['id']);
				$aluno_id = $v['fk_aluno'];
				$r = $this->boletim_anual_aluno($aluno_id, $turma_id, $vetor_media_da_turma);
				$pdf_aluno->addAluno($r);
			}
			
			header('Content-Type: application/pdf');
			header('Content-Disposition: attachment; filename="Boletim_turma.pdf"');
			header('Cache-Control: no-cache, no-store, must-revalidate');
			header('Pragma: no-cache');
			header('Expires: 0');

			
			//$pdf_aluno->output('Boletim_turma.pdf', 'D');

			$filePath = 'Boletim_turma.pdf';
			
			$pdf_aluno->output($filePath, 'I');

			$response = \Config\Services::response();

			return $response->download($filePath, null)->setFileName('Boletim_do_aluno.pdf');
		}
		//exit();
	
	
	}
	
	public function boletim_anual_turma($turma_id, $alunoId = null) {
		
		$qAluno = !empty($alunoId) ? " and at.fk_aluno = ".$alunoId : '';
		
		$q = "
		SELECT a.id 
		FROM si_aluno_turma at

		JOIN si_aluno a
		ON at.fk_aluno = a.id
		
		WHERE
		at.fk_turma = $turma_id
		".$qAluno."
		AND a.status = '1'
		
		ORDER BY a.nome
		";
		
		//$r = $this->conn->qcv($q, "id");
		
		$r = (new SI_AlunoTurmaModel())->query($q)->getResultArray();

		if($r != null) {
			
			$vetor_geral = array();
			
			
			$nota = new nota();
			$disciplinas = parametro::disciplinas();
			$vetor_media_da_turma = array();
			for($trimestre=1; $trimestre<=3; $trimestre++) {
				foreach ($disciplinas as $disciplina => $nome_disc) {
					$media = $nota->get_media_turma($turma_id, $disciplina, $trimestre);
					$vetor_media_da_turma[$trimestre . "t"][$disciplina] = $media;
				}
			}
			
			
			
			foreach ($r as $v) {
				$aluno_id = $v['id'];
				$boletim_aluno = $this->boletim_anual_aluno($aluno_id, $turma_id, $vetor_media_da_turma);
				array_push($vetor_geral, $boletim_aluno);
			}
			return $vetor_geral;
		}else{
			return "NENHUM_ALUNO_ENCONTRADO_NA_TURMA";
		}
	}
	
}	

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


		$caminho_imagem = base_url().'/assets/admin/dist/img/logo_boletim.jpg';
		

		
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

		$this->pdf->Cell(95, 12, mb_convert_encoding('', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', 0);
		$this->pdf->Cell(90, 12, 'Boletim Escolar', 1, 0, 'C', 0);
		$this->pdf->Ln();
		$this->pdf->Cell(185, 7, mb_convert_encoding($nome_turma .' - '. $grau .' - '. $ano, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		$this->pdf->Ln();
		$this->pdf->Cell(185, 7, mb_convert_encoding('Matrícula: ' . $matricula. ' - ' . $nome, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		$this->pdf->Ln();


		$this->pdf->SetFont ( 'Arial', '', 11 );

		$this->pdf->Cell($this->largura_disciplinas, 13, 'Componentes Curriculares', 1, 0, 'C', 1);
		$this->pdf->Cell ( 90, 8,	'Notas Trimestrais', 	1,		0, 'C', 1 );
		$this->pdf->Cell ( 45, 8,	'Notas Anuais', 	1,		0, 'C', 1);
		$this->pdf->Ln();
		$this->pdf->Cell($this->largura_disciplinas, 5, '', 'LB', 0, 'C', 1);

		$this->pdf->Cell ( 15, 5, mb_convert_encoding('1º Trim.', 'ISO-8859-1', 'UTF-8'),	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, 'Faltas',	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, mb_convert_encoding('2º Trim.', 'ISO-8859-1', 'UTF-8'),	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, 'Faltas',	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, mb_convert_encoding('3º Trim.', 'ISO-8859-1', 'UTF-8'),	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, 'Faltas',	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, 'MPA',	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, 'REC',	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, 'MA',	1, 		0, 'C', 1);


		$this->notas_boletim($vetor_notas_boletim);
		
		
		$this->pdf->SetFont ( 'Arial', '', 8 );
		$this->pdf->Ln();
		$this->pdf->Ln();
		$this->pdf->Cell ( 50, 4, mb_convert_encoding('Legenda: MPA = Média Parcial Anual; REC = Nota de Recuperação; MA = Média Anual', 'ISO-8859-1', 'UTF-8'),	0, 0, 'L', 0);
		
		/*
		$this->pdf->Cell ( 50, 4, mb_convert_encoding('MPA = Média Parcial Anual', 'ISO-8859-1', 'UTF-8'),	0, 0, 'L', 0);
		$this->pdf->Ln();
		$this->pdf->Cell ( 50, 4, mb_convert_encoding('REC = Nota de Recuperação', 'ISO-8859-1', 'UTF-8'),	0, 0, 'L', 0);
		$this->pdf->Ln();
		$this->pdf->Cell ( 50, 4, mb_convert_encoding('MA = Média Anual', 'ISO-8859-1', 'UTF-8'),	0, 0, 'L', 0);
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
			$this->pdf->Cell ( 145, 7, mb_convert_encoding('Notas 1°, 2º e 3° Trimestres', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', 1 );
			$this->pdf->Ln();
			$this->pdf->Cell ( 40, 5, 'Disciplina', 1, 0, 'L', 1 );
	
	
	
			$y = $this->pdf->GetY() + 5;
			$altura_y = 106;
	
	
			$this->pdf->SetFont ( 'Arial', '', 9 );
			for($i=0; $i<=10; $i++) {
				//$this->pdf->SetDrawColor(cor::$escala_cinza_3, cor::$escala_cinza_3, cor::$escala_cinza_3);
				
				$offset = 3;
				
				$this->pdf->SetX($this->pdf->GetX() + $offset);
				$this->pdf->Cell ( 13.37, 5, $i, 0, 0, 'C', 0 );
				$this->pdf->SetX($this->pdf->GetX() - $offset);
				
				$x = $this->pdf->GetX() - 3.6;
				$this->pdf->Line($x, $y, $x, $y + $altura_y);
			}
			
			

			// NUCLEOA
			$trim1_nucleo_comum = $info_aluno["media_nucleo_comum_1trim"];
			//$this->pdf->Cell ( 6, 5, $trim1_nucleo_comum, 0, 0, 'C', 0 );
				
				
				
			
			
			//$this->pdf->SetDrawColor(cor::$escala_cinza_3, cor::$escala_cinza_3, cor::$escala_cinza_3);
	
			
			$this->pdf->Ln();
	
	
			$this->set_materias_grafico($vetor_materias_grafico, $info_aluno);
	
	
	
			$this->pdf->SetFont ( 'Arial', '', 11 );
	
	
			$this->legendas();
			
			
			
			
			
			//$this->media_materias_nucleo();
		}
		
		if($output_pdf) {
			$this->pdf->Output('Boletim_turma.pdf', 'D');
		}	
		
		
		
	}
	
	protected function media_materias_nucleo() {
		$this->pdf->Ln();
		$this->pdf->Cell( 6, 6, mb_convert_encoding('Matérias núcleo', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L', 1 );
	}


	protected function get_comprimento_barra($nota) {
		//$nota = 10.0;
		if($nota ===  parametro::nota_sem_valor()) {
			return 0.01;
		}
		
		$nota_zero = 9.9;
		$largura_maxima_nota = 143.5;
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
			
			
			
			$this->pdf->Cell ( $this->largura_disciplinas, 5, mb_convert_encoding($notas['disciplina'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 1);
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

			$this->pdf->Cell ( 40, 6, mb_convert_encoding($materia[0], 'ISO-8859-1', 'UTF-8'), 0, 1, 'L', 1 );


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


			$this->pdf->Ln();
			$this->pdf->Ln();
			$this->pdf->SetX($padding_left_barras);

			$this->pdf->SetFillColor(cor::$comparativo1_cor3_R, cor::$comparativo1_cor3_G, cor::$comparativo1_cor3_B);
			$this->pdf->Cell (  $this->get_comprimento_barra($individual_3), $altura_barras, '',	0, 0, 'R', 1 );

			$this->pdf->Ln();
			$this->pdf->Ln();
			$this->pdf->SetX($padding_left_barras);

			$this->pdf->SetFillColor(cor::$comparativo1_cor4_R, cor::$comparativo1_cor4_G, cor::$comparativo1_cor4_B);
			$this->pdf->Cell (  $this->get_comprimento_barra($turma_3), $altura_barras, '',	0, 0, 'R', 1 );

			



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


		

		$linhaX = $this->pdf->GetX();
		$linhaY = $this->pdf->GetY() - 19;
		$linha_largura = 183.5;
		
		
		
		
		

		$this->pdf->Cell ( 40, 2, '', 0, 0, 'L', 0 );
		
		//$this->pdf->Cell ( 10, 6, "$trim1_nucleo_comum - $trim2_nucleo_comum - $trim3_nucleo_comum",	0, 1, 'R', 1 );
		
		
		
		$this->pdf->SetXY($px, $py);
		
		
		
		// barras verticais separadoras dos gráficos:
		$this->pdf->SetDrawColor(cor::$escala_cinza_3, cor::$escala_cinza_3, cor::$escala_cinza_3);
		$y_inicio = $this->pdf->GetY() - 110;
		$y = 234;
		$x = 50;
		$this->pdf->Line($x, $y_inicio, $x, $y);
		$x = 127;
		//$this->pdf->Line($x, $y_inicio, $x, $y);
		
		
		
		
	}

	protected function legendas() {

		//global $this->pdf;

		
		
		
		
		// legenda ESQUERDA início

		$this->pdf->SetFont ( 'Arial', '', 7 );
		$legenda_x = 51;
		$legenda_x_texto = $legenda_x + 3;
		$posicao_y_esquerda = $this->pdf->GetY() + 3;
		$ALTURA_CELULA_LEGENDA_CORES = 4;

		
		$this->pdf->SetY($posicao_y_esquerda);
		
		
		$this->pdf->SetX($legenda_x);
		$this->pdf->SetFillColor(cor::$comparativo1_cor1_R, cor::$comparativo1_cor1_G, cor::$comparativo1_cor1_B);
		$this->pdf->Cell ( 2, 2, '',	0, 0, 'R', 1 );

		$this->pdf->SetFillColor(cor::$branco, cor::$branco, cor::$branco);
		$this->pdf->SetXY($legenda_x_texto, $this->pdf->GetY() - 1);
		$this->pdf->Cell( 6, $ALTURA_CELULA_LEGENDA_CORES, mb_convert_encoding('Nota individual no Trimestre', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L', 1 );
		$this->pdf->Ln();



		


		$this->pdf->SetX($legenda_x);
		$this->pdf->SetFillColor(cor::$comparativo1_cor2_R, cor::$comparativo1_cor2_G, cor::$comparativo1_cor2_B);
		$this->pdf->Cell ( 2, 2, '',	0, 0, 'R', 1 );

		$this->pdf->SetFillColor(cor::$branco, cor::$branco, cor::$branco);
		$this->pdf->SetXY($legenda_x_texto, $this->pdf->GetY() - 1);
		$this->pdf->Cell ( 6, $ALTURA_CELULA_LEGENDA_CORES,  mb_convert_encoding('Média da turma no Trimestre', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L', 1 );
		$this->pdf->Ln();


		



		/*
		$this->pdf->SetX($legenda_x);
		
		$this->pdf->SetFillColor(cor::$branco, cor::$branco, cor::$branco);
		$this->pdf->SetXY($legenda_x - 1, $this->pdf->GetY() - 1);
		$this->pdf->Cell ( 6, $ALTURA_CELULA_LEGENDA_CORES,  mb_convert_encoding('MNC = Média do Núcleo Comum', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L', 1 );
		$this->pdf->Ln();
		*/
		
		
		// Legenda fim


		
		
		
		
		$this->pdf->SetDrawColor(cor::$escala_cinza_2, cor::$escala_cinza_2, cor::$escala_cinza_2);



	}







}

class pdf_turma_com_grafico extends pdf_aluno_com_grafico {

	public function gerar_turma($vetor_info) {
		
		
		foreach ($vetor_info as $info_aluno) {
			$this->gerar_aluno($info_aluno, false);
		}
		
		
		//$this->gerar_aluno($vetor_info[0]);
		$this->pdf->Output();
	}





}

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
		$this->pdf->Output('Boletim_turma.pdf', 'D');
	}
}

/*
*/


class MYPDF_BOLETIM_INDIVIDUAL extends  tcpdf {

	//Page header
	public function Header() {
		// Logo
		$image_file = base_url().'/assets/admin/dist/img/logo_boletim.jpg';


		$this->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);

		$html = '<table cellpadding="1" cellspacing="10" border="0" style="text-align:center;">
				<tr><td>&nbsp;</td></tr>
	<tr style="text-align:left;"><td><img src="'.base_url().'/assets/admin/dist/img/logo_boletim.jpg'.'" border="0" height="31" width="0" align="top" /></td></tr>
	</table>';

		// output the HTML content
		//$this->writeHTML($html, true, false, true, false, '');

	}

	// Page footer
	public function Footer() {
		// Position at 15 mm from bottom
		$this->SetY(-35);
		// Set font
		$this->SetFont('helvetica', 'I', 8);
		// Page number
		//$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');

		$y = 265;
		$x = 167;
		$w = 30;
		$h = 30;
		//$this->Image('visao/pagina/conteudo/relatorio/boletim/SELO.jpg', $x, $y, $w, $h, 'JPG', '', '', false, 300, '', false, false, 0, $fitbox, false, false);
	}
}



class relatorio_tcpdf_boletim_individual {


	var $gx = 35; // posição X inicial do gráfico, relativo a borda esquerda da folha;
	var $gy = 120; // posição Y inicial do gráfico, relativo a borda superior da folha;

	var $gw = 160; // largura do gráfico;
	var $gh = 80;  // altura do gráfico;
	var $pdf;

	var $estilo1t = array('width' => 0.7, 'cap' => 'butt', 'join' => 'miter', 'dash' => '2,3', 'color' => array(0, 0, 164));
	var $estilo2t = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => '9,5', 'color' => array(164, 0, 0));
	var $estilo3t = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 164, 0));

	var $bordaRet1 = array('L' => 0, 'T' => 0,  'R' => 0, 'B' => 0);
	var $bordaRet2 = array('L' => 0, 'T' => 0,  'R' => 0, 'B' => 0);
	var $bordaRet3 = array('L' => 0, 'T' => 0,  'R' => 0, 'B' => 0);

	var $fillRet3 = array(150, 150, 150);
	var $fillRet2 = array(139, 139, 139);
	var $fillRet1 = array(208, 55, 55);


	var $notas1t = array(6.0, 9.8, 6.9, 7.2, 6.6, 6.2, 7.2, 8.8, 7.3, 7.9);
	var $notas2t = array(7.2, 6.6, 6.2, 6.0, 9.8, 6.9, 8.8, 7.3, 7.9, 7.2);
	var $notas3t = array(8.8, 7.3, 7.9, 6.9, 7.2, 6.6, 6.2, 7.2, 6.0, 9.8);


	public function create() {
		$this->pdf = new MYPDF_BOLETIM_INDIVIDUAL(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		
		
		// set document information
		$this->pdf->SetCreator(PDF_CREATOR);
		$this->pdf->SetAuthor('Lucas Rondon');
		$this->pdf->SetTitle('Boletim Turma');
		$this->pdf->SetSubject('Boletim Turma');
		$this->pdf->SetKeywords('Relatório');
		
		// set default header data
		$this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
		$this->pdf->setFooterData(array(0,64,0), array(0,64,128));
		
		// set header and footer fonts
		$this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		// set default monospaced font
		$this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		// set margins
		$this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		
		// set auto page breaks
		$this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		// set image scale factor
		$this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$this->pdf->setLanguageArray($l);
		}
		
		// ---------------------------------------------------------
		
		// set default font subsetting mode
		$this->pdf->setFontSubsetting(true);
		
		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$this->pdf->SetFont('dejavusans', '', 11, '', true);
		
	}
	
	public function addAluno($info_aluno) {
		
		// create new PDF document
		
		// Add a page
		// This method has several options, check the source code documentation for more information.
		$this->pdf->AddPage();


		/*
		 $this->pdf->MultiCell($larg, 5, 'Legenda:', 0, 'L', 0, 1,
		 30,
		 50,
		 true);

		 $this->Legenda();
		 */
		$this->pdf->SetFont('dejavusans', '', 9, '', true);

	//		echo '<pre>';
	//		print_r($info_aluno);
		$this->Tabela($info_aluno);

		
	}
	public function output($texto, $tipo) {
		// ---------------------------------------------------------

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$this->pdf->Output($texto, $tipo);
	}
	private function Tabela($info_aluno) {
		

		$nome = $info_aluno["nome"];
		$matricula = $info_aluno["matricula"];
		
		$nome_turma = $info_aluno["nome_turma"];
		$grau = $info_aluno["grau"];
		$ano = $info_aluno["ano"];
		
		$vetor_notas_boletim = $info_aluno["notas"];
		
		
		//echo '<pre>'; print_r($vetor_notas_boletim);
		

		$largura_nota = 50;
		$htmlNotas = '';
		$i=0;
		foreach($vetor_notas_boletim as $notas) {

			$estilo = 'border:1px solid #ccc;';
			
			if($i % 2 != 0) {
				$estilo .= 'background-color:#F0F0F0;';
			}
			
			$htmlNotas .= '<tr>
    <td  style="'.$estilo.'">'.$notas['disciplina'].'</td>
    <td align="right" width="'.$largura_nota.'" style="'.$estilo.'">'.$notas['1t'].'</td>
    <td align="right" width="'.$largura_nota.'" style="'.$estilo.'">'.$notas['1tf'].'</td>
    <td align="right" width="'.$largura_nota.'" style="'.$estilo.'">'.$notas['2t'].'</td>
    <td align="right" width="'.$largura_nota.'" style="'.$estilo.'">'.$notas['2tf'].'</td>
    <td align="right" width="'.$largura_nota.'" style="'.$estilo.'">'.$notas['3t'].'</td>
    <td align="right" width="'.$largura_nota.'" style="'.$estilo.'">'.$notas['3tf'].'</td>
    <td align="right" width="'.$largura_nota.'" style="'.$estilo.'">'.$notas['mpa'].'</td>
    <td align="right" width="'.$largura_nota.'" style="'.$estilo.'">'.$notas['rec'].'</td>
    <td align="right" width="'.$largura_nota.'" style="'.$estilo.'">'.$notas['ma'].'</td>
  </tr>';
			$i++;
		}
		
		//$vetor_materias_grafico = $info_aluno[2];
		

		
		
		$html = '
	<table border="0" cellpadding="2">
					<tr>
		<td  style="border:1px solid #ccc;"  colspan="5" align="center"><img src="'.base_url().'/assets/admin/dist/img/logo_boletim.jpg'.'" border="0" height="31" width="0" align="top" /></td>
		<td  style="border:1px solid #ccc;"  colspan="5" align="center"><div style="font-size:8px;">&nbsp;</div>Boletim Escolar</td>
	</tr>
	<tr>
		<td  style="border:1px solid #ccc;" colspan="10"><table cellpadding="5"><tr><td>'.$nome_turma.' - '.$grau.' - '.$ano.'</td></tr></table></td>
	</tr>
	<tr>
		<td  style="border:1px solid #ccc;"  colspan="10"><table cellpadding="5"><tr><td>Matrícula: '.$matricula.' - '.$nome.'</td></tr></table></td>
	</tr>
	<tr style="background-color:#DCDCDC;">
		<td  rowspan="2" width="188" style="border:1px solid #aaa;" ><div style="font-size:8px;">&nbsp;</div>Componentes Curriculares</td>
		<td colspan="6" align="center" width="'.($largura_nota * 6).'"  style="border:1px solid #aaa;" >Notas Trimestrais</td>
		<td colspan="3" align="center" width="'.($largura_nota * 3).'" style="border:1px solid #aaa;" >Notas Anuais</td>
	</tr>
	<tr style="background-color:#DCDCDC;">
		<td align="center" width="'.$largura_nota.'" style="border:1px solid #aaa;" >1º Trim.</td>
		<td align="center" width="'.$largura_nota.'" style="border:1px solid #aaa;" >Faltas</td>
		<td align="center" width="'.$largura_nota.'" style="border:1px solid #aaa;" >2º Trim.</td>
		<td align="center" width="'.$largura_nota.'" style="border:1px solid #aaa;" >Faltas</td>
		<td align="center" width="'.$largura_nota.'" style="border:1px solid #aaa;" >3º Trim.</td>
		<td align="center" width="'.$largura_nota.'" style="border:1px solid #aaa;" >Faltas</td>
		<td align="center" width="'.$largura_nota.'" style="border:1px solid #aaa;" >MPA</td>
		<td align="center" width="'.$largura_nota.'" style="border:1px solid #aaa;" >REC</td>
		<td align="center" width="'.$largura_nota.'" style="border:1px solid #aaa;" >MA</td>
	</tr>
	'.$htmlNotas.'
			<tr><td colspan="10">Legenda X4: MPA = Média Parcial Anual; REC = Nota de Recuperação; MA = Média Anual
			</td></tr>
	</table>';

		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		$this->MontaPlanilha($vetor_notas_boletim);
		$this->MontaBarras($vetor_notas_boletim);
		
		
	}
	private function MontaBarras($vetor_notas_boletim) {
		$x = $this->gx;
		$y = $this->gy;
		

		for($i=0; $i<10; $i++) {


			$xinicio = 0;
			
			$nota1t = str_replace(",", ".", $vetor_notas_boletim[$i]['1t']);
			//$nota1t = "8.0";
			$calc = $this->Calculo1($nota1t, $i);
			$x = $calc[0] + $xinicio;
			$y = $calc[1];
			$this->pdf->Rect($x, $y, 0.91, $this->gh - ($y - $this->gy), 'DF', $this->bordaRet3, $this->fillRet3);
				

			$nota2t = str_replace(",", ".", $vetor_notas_boletim[$i]['2t']);
			//$nota1t = "8.0";
			$calc = $this->Calculo1($nota2t, $i);
			$x = $calc[0] + $xinicio + 3;
			$y = $calc[1];
			$this->pdf->Rect($x, $y, 0.91, $this->gh - ($y - $this->gy), 'DF', $this->bordaRet3, $this->fillRet3);
			

			$nota3t = str_replace(",", ".", $vetor_notas_boletim[$i]['3t']);
			//$nota1t = "8.0";
			$calc = $this->Calculo1($nota3t, $i);
			$x = $calc[0] + $xinicio + 6;
			$y = $calc[1];
			$this->pdf->Rect($x, $y, 0.91, $this->gh - ($y - $this->gy), 'DF', $this->bordaRet3, $this->fillRet3);
			
			
			
			
			// turma:

			$nota1t = str_replace(",", ".", $vetor_notas_boletim[$i]['1t_media_turma']);
			//$nota1t = "8.0";
			$calc = $this->Calculo1($nota1t, $i);
			$x = $calc[0] + $xinicio + 1.5;
			$y = $calc[1];
			$this->pdf->Rect($x, $y, 0.91, $this->gh - ($y - $this->gy), 'DF', $this->bordaRet1, $this->fillRet1);
				

			$nota2t = str_replace(",", ".", $vetor_notas_boletim[$i]['2t_media_turma']);
			//$nota1t = "8.0";
			$calc = $this->Calculo1($nota2t, $i);
			$x = $calc[0] + $xinicio + 4.5;
			$y = $calc[1];
			$this->pdf->Rect($x, $y, 0.91, $this->gh - ($y - $this->gy), 'DF', $this->bordaRet1, $this->fillRet1);
				

			$nota3t = str_replace(",", ".", $vetor_notas_boletim[$i]['3t_media_turma']);
			//$nota1t = "8.0";
			$calc = $this->Calculo1($nota3t, $i);
			$x = $calc[0] + $xinicio + 7.5;
			$y = $calc[1];
			$this->pdf->Rect($x, $y, 0.91, $this->gh - ($y - $this->gy), 'DF', $this->bordaRet1, $this->fillRet1);
				
			
			
		}
	}
	private function MontaPlanilha($vetor_notas_boletim) {


		// verticais:
		$this->pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(200, 200, 200)));

		$larguraMateria = $this->gw / 12.0;
		$alturaLinha = $this->gh / 10.0;
		$disciplinas = parametro::disciplinas(false);
		$d2 = array();
		foreach ($disciplinas as $k => $v) {
			array_push($d2, $v);
		}

		$it = count($vetor_notas_boletim);

		//dd($vetor_notas_boletim);

		for($i=0; $i < $it; $i++) {	
			/*
			 $this->pdf->PolyLine(array(
			 $this->gx + $larguraMateria * $i,
			 $this->gy,
			 $this->gx + $larguraMateria * $i,
			 $this->gy + $this->gh

			 ), 'D', array(), array());
			 */


			$x = $this->gx + $larguraMateria * $i + 2;
			$y = $this->gy + $this->gh + 1;

			$espacoY = 4;
			$larg = 10;


			
			//echo '<pre>';print_r($this->notas1t);			exit;
			//var_dump($vetor_notas_boletim[$i]['1t']);die;

			$this->pdf->MultiCell($larg, 5, $vetor_notas_boletim[$i]['1t'], 0, 'C', 0, 1,
					$x,
					$y,
					true);

			$y += $espacoY;

			$this->pdf->MultiCell($larg, 5, $vetor_notas_boletim[$i]['2t'], 0, 'C', 0, 1,
					$x,
					$y,
					true);
			$y += $espacoY;

			$this->pdf->MultiCell($larg, 5, $vetor_notas_boletim[$i]['3t'], 0, 'C', 0, 1,
					$x,
					$y,
					true);
			$y += $espacoY;


			$x = $this->gx + $larguraMateria * $i - 43;

			$this->pdf->StartTransform();
			$this->pdf->Rotate(35, $x + 50, $y);
			$this->pdf->MultiCell(50, 5, $d2[$i], 0, 'R', 0, 1,
					$x,
					$y,
					true);
			$this->pdf->StopTransform();
			// Rotate 20 degrees counter-clockwise centered by (70,110) which is the lower left corner of the rectangle

		}


		// horizontais:
		$this->pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(200, 200, 200)));


		for($i=0; $i<=10; $i++ ) {
				
			$this->pdf->PolyLine(array(
					$this->gx,
					$this->gy + $alturaLinha * $i,
					$this->gx + $this->gw - $larguraMateria,
					$this->gy + $alturaLinha * $i
			), 'D', array(), array());

			if($i < 10) {
				$this->pdf->MultiCell(50, 5, 10 - $i . "", 0, 'R', 0, 1,
						$this->gx - 52,
						$this->gy + $alturaLinha * $i - 2,
						true);

				$this->pdf->MultiCell(50, 5, 10 - $i . "", 0, 'L', 0, 1,
						$this->gx + $this->gw - 14,
						$this->gy + $alturaLinha * $i - 2,
						true);
			}
		}



		$x = $this->gx - 10;
		$y = $this->gy + $this->gh - ($this->gh / 10.0) + 9;


		$this->pdf->MultiCell($larg, 5, '1º T', 0, 'L', 0, 1,
				$x,
				$y,
				true);
		$y += $espacoY;

		$yLinha = 5;
		$this->linhaTrimestre($yLinha);

		$this->pdf->MultiCell($larg, 5, '2º T', 0, 'L', 0, 1,
				$x,
				$y,
				true);
		$y += $espacoY;

		$yLinha = 9;
		$this->linhaTrimestre($yLinha);

		$this->pdf->MultiCell($larg, 5, '3º T', 0, 'L', 0, 1,
				$x,
				$y,
				true);
		$y += $espacoY;
			
		$this->pdf->SetFillColor(220, 255, 220);

		
		
		$this->pdf->Rect($x + 5, $y + 21, 3, 3, 'DF', $this->bordaRet3, $this->fillRet3);
		$this->pdf->MultiCell(30, 5, 'Nota do aluno', 0, 'L', 0, 1,
				$x + 10,
				$y + 20,
				true);
		
		

		$this->pdf->Rect($x + 5, $y + 26, 3, 3, 'DF', $this->bordaRet1, $this->fillRet1);
		$this->pdf->MultiCell(30, 5, 'Média da turma', 0, 'L', 0, 1,
				$x + 10,
				$y + 25,
				true);
		$y += $espacoY;
		
			
		
	}

	private function linhaTrimestre($yLinha) {
		$larguraMateria = $this->gw / 10.0;
		$this->pdf->PolyLine(array(
				$this->gx - 15,
				$this->gy + $this->gh +  $yLinha,
				$this->gx + $this->gw - $larguraMateria + 5,
				$this->gy + $this->gh +  $yLinha
		), 'D', array(), array());
	}
	private function Calculo1($nota, $indiceMateria) {

		$nota = !empty($nota) ? $nota : 0;
		$r3 = (int)$nota * $this->gh / 10.0; // 24
		$pos1 = $this->gh - $r3; // 16 de cima pra baixo.

		$larguraMateria = $this->gw / 12.0;

		return array($indiceMateria * $larguraMateria + $this->gx + 3, $pos1 + $this->gy);
	}
	private function Legenda() {

		$x = 60;
		$y = 52.5;

		$larg = 20;

		$this->pdf->SetFont('dejavusans', '', 9, '', true);


		$this->pdf->MultiCell($larg, 5, '1º T', 0, 'L', 0, 1,
				$x - 10,
				$y - 2,
				true);



		$this->pdf->SetLineStyle($this->estilo1t);
		$this->pdf->PolyLine(array($x, $y, $x + $larg, $y), 'D', array(), array());

		$y += 5;

		$this->pdf->MultiCell($larg, 5, '2º T', 0, 'L', 0, 1,
				$x - 10,
				$y - 2,
				true);
		$this->pdf->SetLineStyle($this->estilo2t);
		$this->pdf->PolyLine(array($x, $y, $x + $larg, $y), 'D', array(), array());

		$y += 5;

		$this->pdf->MultiCell($larg, 5, '3º T', 0, 'L', 0, 1,
				$x - 10,
				$y - 2,
				true);
		$this->pdf->SetLineStyle($this->estilo3t);
		$this->pdf->PolyLine(array($x, $y, $x + $larg, $y), 'D', array(), array());
	}
}


class monta_pdf_boletim_aluno {
	
	protected $conn;
	protected $nota;
	
	
	protected $sem_valor;
	/*
	public function __construct() {
		//$this->conn = EASYNC5__model_conn::get_conn();
		
		$this->nota = new nota();
		
		$this->sem_valor = parametro::nota_sem_valor();
		

		$aluno_id = 0;
		$turma_id = 0;
		


		$aluno_turma_id = (int)util::GET('aluno_turma_id');
		if($aluno_turma_id != 0) {
			$aluno_turma = EASYNC5__si_aluno_turma::getByPK($aluno_turma_id);

			//$turma_id = 145;
			$aluno_id = (int)$aluno_turma->getFk_aluno();
			$turma_id = (int)$aluno_turma->getFk_turma();
			
		}else{
		
			//$turma_id = 145;
			$aluno_id = (int)util::GET('aluno_id');
			$turma_id = (int)util::GET('turma_id');
		}
		
		//echo '$r = $this->boletim_anual_aluno($aluno_id, $turma_id);';
		//exit();
		
		
		
		$disciplinas = parametro::disciplinas();
		
		$vetor_media_da_turma = array();
		for($trimestre=1; $trimestre<=3; $trimestre++) {
			foreach ($disciplinas as $disciplina => $nome_disc) {
				$media = $this->nota->get_media_turma($turma_id, $disciplina, $trimestre);
				
				$vetor_media_da_turma[$trimestre . "t"][$disciplina] = $media;
			}
		}
		
		
		
		$r = $this->boletim_anual_aluno($aluno_id, $turma_id, $vetor_media_da_turma);
		/*
		 echo '<pre>';
		 print_r($r);
		 exit;
		 */
		//$pdf_aluno = new pdf_aluno_com_grafico();
		//$pdf_aluno->gerar_aluno($r);
		
		
		/*
		
		$pdf_aluno = new relatorio_tcpdf_boletim_individual();
		$pdf_aluno->create();
		$pdf_aluno->addAluno($r);
		$pdf_aluno->output();
	}*/

	public function boletim_anual_aluno($aluno_id, $turma_id, $vetor_media_da_turma) {
		return (new nota)->boletim_anual_aluno($aluno_id, $turma_id, $vetor_media_da_turma);
	}	
}


class monta_pdf_ficha_individual {
	
	protected $conn;
	protected $nota;
	
	
	protected $sem_valor;
	
	/*
	protected $provas = array (
			's1',
			's2',
			's3',
			's',
			'f' 
	);
	*/
	public function render($turma_id, $aluno_id) {

		//$this->conn = EASYNC5__model_conn::get_conn();
		
		$this->nota = new nota();
		
		$this->sem_valor = parametro::nota_sem_valor();
		/*

		$aluno_id = 0;
		$turma_id = 0;
		


		$aluno_turma_id = (int)util::GET('aluno_turma_id');
		if($aluno_turma_id != 0) {
			$aluno_turma = EASYNC5__si_aluno_turma::getByPK($aluno_turma_id);

			//$turma_id = 145;
			$aluno_id = (int)$aluno_turma->getFk_aluno();
			$turma_id = (int)$aluno_turma->getFk_turma();
			
		}else{
		
			//$turma_id = 145;
			$aluno_id = (int)util::GET('aluno_id');
			$turma_id = (int)util::GET('turma_id');
		}

		*/
		
		//echo '$r = $this->boletim_anual_aluno($aluno_id, $turma_id);';
		//exit();
		
		
		
		
		$disciplinas = parametro::disciplinas();
		
		$vetor_media_da_turma = array();
		for($trimestre=1; $trimestre<=3; $trimestre++) {
			foreach ($disciplinas as $disciplina => $nome_disc) {
				$media = $this->nota->get_media_turma($turma_id, $disciplina, $trimestre);
				
				$vetor_media_da_turma[$trimestre . "t"][$disciplina] = $media;
			}
		}
		
		
		
		$r = $this->boletim_anual_aluno($aluno_id, $turma_id, $vetor_media_da_turma);
		//echo 'g2';
		$pdf_aluno = new pdf_ficha_individual();
		$pdf_aluno->gerar_aluno($r);
		
	}

	public function boletim_anual_aluno($aluno_id, $turma_id, $vetor_media_da_turma) {
		return $this->nota->boletim_anual_aluno($aluno_id, $turma_id, $vetor_media_da_turma);
	}	
}

class pdf_ficha_individual {

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
		$turno = $info_aluno["turno"];
		
		$dt_nasc = $info_aluno["dt_nasc"];
		$cid_nasc = $info_aluno["cid_nasc"];
		$uf_nasc = $info_aluno["uf_nasc"];
		
		$nome_pai = $info_aluno["nome_pai"];
		$nome_mae = $info_aluno["nome_mae"];
		
		
		$vetor_notas_boletim = $info_aluno["notas"];


		$caminho_imagem = base_url().'/assets/admin/dist/img/logo_boletim.jpg';
        $assinatura_regina = base_url().'/assets/admin/dist/img/assinatura_regina.png';
        $assinatura_toninho = base_url().'/assets/admin/dist/img/assinatura_toninho.png';
		

		
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

		$this->pdf->Cell(95, 12, mb_convert_encoding('', 'ISO-8859-1', 'UTF-8'), 0, 0, 'C', 0);
		$this->pdf->Cell(90, 12, '', 0, 0, 'C', 0);
		$this->pdf->Ln();
		$this->pdf->Cell(185, 7, mb_convert_encoding('ESTADO DE MATO GROSSO - SECRETARIA MUNICIPAL DE EDUCAÇÃO - ENSINO FUNDAMENTAL', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', 10 );
		$this->pdf->Cell(185, 7, mb_convert_encoding('Autorização:  Nº 365/07-CEE-MT, Resolução:  Nº 536/07-CEE-MT ', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		$this->pdf->Ln();
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', 14 );
		$this->pdf->Cell(185, 12, 'FICHA INDIVIDUAL', 0, 0, 'C', 0);
		$this->pdf->SetFont ( 'Arial', 'B', 11 );
		
		/// total: 175
		$this->pdf->Ln();
		$this->pdf->Cell(5, 7, mb_convert_encoding('', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L', 0);
		$this->pdf->SetFont ( 'Arial', 'B', 10 );
		$this->pdf->Cell(18, 7, mb_convert_encoding('Matrícula: ', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		$this->pdf->SetFont ( 'Arial', '', 10 );
		$this->pdf->Cell(157, 7, mb_convert_encoding($matricula, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		
		
		$this->pdf->Ln();
		$this->pdf->Cell(5, 7, mb_convert_encoding('', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L', 0);
		$this->pdf->SetFont ( 'Arial', 'B', 10 );
		$this->pdf->Cell(30, 7, mb_convert_encoding('Nome do Aluno: ', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		$this->pdf->SetFont ( 'Arial', '', 10 );
		$this->pdf->Cell(145, 7, mb_convert_encoding($nome, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		
		
		$this->pdf->Ln();
		$this->pdf->Cell(5, 7, mb_convert_encoding('', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L', 0);
		$this->pdf->SetFont ( 'Arial', 'B', 10 );
		$this->pdf->Cell(40, 7, mb_convert_encoding('Data de nascimento: ', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		$this->pdf->SetFont ( 'Arial', '', 10 );
		$this->pdf->Cell(30, 7, mb_convert_encoding($dt_nasc, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		
		$this->pdf->SetFont ( 'Arial', 'B', 10 );
		$this->pdf->Cell(25, 7, mb_convert_encoding('Naturalidade:', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		$this->pdf->SetFont ( 'Arial', '', 10 );
		$this->pdf->Cell(60, 7, mb_convert_encoding($cid_nasc, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		
		$this->pdf->SetFont ( 'Arial', 'B', 10 );
		$this->pdf->Cell(10, 7, mb_convert_encoding('UF:', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		$this->pdf->SetFont ( 'Arial', '', 10 );
		$this->pdf->Cell(10, 7, mb_convert_encoding($uf_nasc, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		
		
		$this->pdf->Ln();
		$this->pdf->Cell(5, 7, mb_convert_encoding('', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L', 0);
		$this->pdf->SetFont ( 'Arial', 'B', 10 );
		$this->pdf->Cell(28, 7, mb_convert_encoding('Nome do Pai: ', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		$this->pdf->SetFont ( 'Arial', '', 10 );
		$this->pdf->Cell(147, 7, mb_convert_encoding($nome_pai, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		
		
		
		$this->pdf->Ln();
		$this->pdf->Cell(5, 7, mb_convert_encoding('', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L', 0);
		$this->pdf->SetFont ( 'Arial', 'B', 10 );
		$this->pdf->Cell(28, 7, mb_convert_encoding('Nome da Mãe: ', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		$this->pdf->SetFont ( 'Arial', '', 10 );
		$this->pdf->Cell(147, 7, mb_convert_encoding($nome_mae, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		
		
		
		
		
		$this->pdf->Ln();
		$this->pdf->Cell(5, 7, mb_convert_encoding('', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L', 0);
		$this->pdf->SetFont ( 'Arial', 'B', 10 );
		$this->pdf->Cell(20, 7, mb_convert_encoding('Turma: ', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		$this->pdf->SetFont ( 'Arial', '', 10 );
		$this->pdf->Cell(50, 7, mb_convert_encoding($nome_turma, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		
		$this->pdf->SetFont ( 'Arial', 'B', 10 );
		$this->pdf->Cell(20, 7, mb_convert_encoding('Turno: ', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		$this->pdf->SetFont ( 'Arial', '', 10 );
		$this->pdf->Cell(30, 7, mb_convert_encoding($turno, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		
		
		$this->pdf->SetFont ( 'Arial', 'B', 10 );
		$this->pdf->Cell(20, 7, mb_convert_encoding('Ano letivo: ', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		$this->pdf->SetFont ( 'Arial', '', 10 );
		$this->pdf->Cell(35, 7, mb_convert_encoding($ano, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		
		
		
		
		
		
		$this->pdf->Ln();
		$this->pdf->Ln();


		$this->pdf->SetFont ( 'Arial', '', 11 );

		$this->pdf->Cell($this->largura_disciplinas, 13, 'Componentes Curriculares', 1, 0, 'C', 1);
		$this->pdf->Cell ( 90, 8,	'Notas Trimestrais', 	1,		0, 'C', 1 );
		$this->pdf->Cell ( 45, 8,	'Notas Anuais', 	1,		0, 'C', 1);
		$this->pdf->Ln();
		$this->pdf->Cell($this->largura_disciplinas, 5, '', 'LB', 0, 'C', 1);

		$this->pdf->Cell ( 15, 5, mb_convert_encoding('1º Trim.', 'ISO-8859-1', 'UTF-8'),	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, 'Faltas',	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, mb_convert_encoding('2º Trim.', 'ISO-8859-1', 'UTF-8'),	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, 'Faltas',	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, mb_convert_encoding('3º Trim.', 'ISO-8859-1', 'UTF-8'),	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, 'Faltas',	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, 'MPA',	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, 'REC',	1, 		0, 'C', 1);
		$this->pdf->Cell ( 15, 5, 'MA',	1, 		0, 'C', 1);


		$this->notas_boletim($vetor_notas_boletim);
		
		
		$this->pdf->SetFont ( 'Arial', '', 8 );
		$this->pdf->Ln();
		$this->pdf->Ln();
		$this->pdf->Cell ( 50, 4, mb_convert_encoding('Legenda X3: MPA = Média Parcial Anual; REC = Nota de Recuperação; MA = Média Anual', 'ISO-8859-1', 'UTF-8'),	0, 0, 'L', 0);
		
		$this->pdf->Ln();
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', 12 );
		$this->pdf->Cell(5, 7, mb_convert_encoding('', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L', 0);
		$this->pdf->Cell ( 175, 20, mb_convert_encoding('', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 0);
		
		$this->pdf->Ln();
		$this->pdf->SetY($this->pdf->GetY() - 15);
		$this->pdf->Cell(5, 7, mb_convert_encoding('', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L', 0);
		$this->pdf->Cell ( 70, 7, mb_convert_encoding('Observação:', 'ISO-8859-1', 'UTF-8'), 0, 0, 'TL', 0);
		
		//$this->pdf->Cell(5, 7, mb_convert_encoding('', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L', 0);
		$this->pdf->SetFont ( 'Arial', 'B', 14 );
		$this->pdf->Cell ( 40, 7, mb_convert_encoding($info_aluno['aluno_aprovado_ficha_individual'], 'ISO-8859-1', 'UTF-8'), 0, 0, 'C', 0);
		
		$this->pdf->Ln();
		$this->pdf->Ln();
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', 10 );
		$this->pdf->Cell ( 5, 7, mb_convert_encoding('', 'ISO-8859-1', 'UTF-8'), 0, 0, 'C', 0);
		$this->pdf->Cell ( 175, 7, mb_convert_encoding("Cuiabá, "  . $info_aluno["data_atual"], 'ISO-8859-1', 'UTF-8'), 0, 0, 'C', 0);
		
		
		$this->pdf->SetDrawColor(0);
		
		
		$this->pdf->Ln();
		$this->pdf->Ln();
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', 10 );
		$this->pdf->Cell ( 15, 7, mb_convert_encoding('', 'ISO-8859-1', 'UTF-8'), 0, 0, 'C', 0);
		$this->pdf->Cell ( 70, 7, mb_convert_encoding('Secretário (a)', 'ISO-8859-1', 'UTF-8'), 'T', 0, 'C', 0);
		$this->pdf->Cell ( 10, 7, mb_convert_encoding('', 'ISO-8859-1', 'UTF-8'), 0, 0, 'C', 0);
		$this->pdf->Cell ( 70, 7, mb_convert_encoding('Diretor (a)', 'ISO-8859-1', 'UTF-8'), 'T', 0, 'C', 0);


        $padding_assinatura = -5;

        $this->pdf->Image($assinatura_regina, 35, 222 + $padding_assinatura, 59, 18);
        $this->pdf->Image($assinatura_toninho, 121, 216 + $padding_assinatura, 37, 29);
		
		if($output_pdf) {
			$this->pdf->Output('Ficha_individual.pdf', 'D');
		}	
		
		
		
	}
	
	protected function media_materias_nucleo() {
		$this->pdf->Ln();
		$this->pdf->Cell( 6, 6, mb_convert_encoding('Matérias núcleo', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L', 1 );
	}


	protected function get_comprimento_barra($nota) {
		//$nota = 10.0;
		if($nota ===  parametro::nota_sem_valor()) {
			return 0.01;
		}
		
		$nota_zero = 9.9;
		$largura_maxima_nota = 143.5;
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
			
			
			
			$this->pdf->Cell ( $this->largura_disciplinas, 5, mb_convert_encoding($notas['disciplina'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', 1);
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

			$this->pdf->Cell ( 40, 6, mb_convert_encoding($materia[0], 'ISO-8859-1', 'UTF-8'), 0, 1, 'L', 1 );


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


			$this->pdf->Ln();
			$this->pdf->Ln();
			$this->pdf->SetX($padding_left_barras);

			$this->pdf->SetFillColor(cor::$comparativo1_cor3_R, cor::$comparativo1_cor3_G, cor::$comparativo1_cor3_B);
			$this->pdf->Cell (  $this->get_comprimento_barra($individual_3), $altura_barras, '',	0, 0, 'R', 1 );

			$this->pdf->Ln();
			$this->pdf->Ln();
			$this->pdf->SetX($padding_left_barras);

			$this->pdf->SetFillColor(cor::$comparativo1_cor4_R, cor::$comparativo1_cor4_G, cor::$comparativo1_cor4_B);
			$this->pdf->Cell (  $this->get_comprimento_barra($turma_3), $altura_barras, '',	0, 0, 'R', 1 );

			



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


		

		$linhaX = $this->pdf->GetX();
		$linhaY = $this->pdf->GetY() - 19;
		$linha_largura = 183.5;
		
		
		
		
		

		$this->pdf->Cell ( 40, 2, '', 0, 0, 'L', 0 );
		
		//$this->pdf->Cell ( 10, 6, "$trim1_nucleo_comum - $trim2_nucleo_comum - $trim3_nucleo_comum",	0, 1, 'R', 1 );
		
		
		
		$this->pdf->SetXY($px, $py);
		
		
		
		// barras verticais separadoras dos gráficos:
		$this->pdf->SetDrawColor(cor::$escala_cinza_3, cor::$escala_cinza_3, cor::$escala_cinza_3);
		$y_inicio = $this->pdf->GetY() - 110;
		$y = 234;
		$x = 50;
		$this->pdf->Line($x, $y_inicio, $x, $y);
		$x = 127;
		//$this->pdf->Line($x, $y_inicio, $x, $y);
		
		
		
		
	}

	protected function legendas() {

		//global $this->pdf;

		
		
		
		
		// legenda ESQUERDA início

		$this->pdf->SetFont ( 'Arial', '', 7 );
		$legenda_x = 51;
		$legenda_x_texto = $legenda_x + 3;
		$posicao_y_esquerda = $this->pdf->GetY() + 3;
		$ALTURA_CELULA_LEGENDA_CORES = 4;

		
		$this->pdf->SetY($posicao_y_esquerda);
		
		
		$this->pdf->SetX($legenda_x);
		$this->pdf->SetFillColor(cor::$comparativo1_cor1_R, cor::$comparativo1_cor1_G, cor::$comparativo1_cor1_B);
		$this->pdf->Cell ( 2, 2, '',	0, 0, 'R', 1 );

		$this->pdf->SetFillColor(cor::$branco, cor::$branco, cor::$branco);
		$this->pdf->SetXY($legenda_x_texto, $this->pdf->GetY() - 1);
		$this->pdf->Cell( 6, $ALTURA_CELULA_LEGENDA_CORES, mb_convert_encoding('Nota individual no Trimestre', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L', 1 );
		$this->pdf->Ln();



		


		$this->pdf->SetX($legenda_x);
		$this->pdf->SetFillColor(cor::$comparativo1_cor2_R, cor::$comparativo1_cor2_G, cor::$comparativo1_cor2_B);
		$this->pdf->Cell ( 2, 2, '',	0, 0, 'R', 1 );

		$this->pdf->SetFillColor(cor::$branco, cor::$branco, cor::$branco);
		$this->pdf->SetXY($legenda_x_texto, $this->pdf->GetY() - 1);
		$this->pdf->Cell ( 6, $ALTURA_CELULA_LEGENDA_CORES,  mb_convert_encoding('Média da turma no Trimestre', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L', 1 );
		$this->pdf->Ln();


		



		/*
		$this->pdf->SetX($legenda_x);
		
		$this->pdf->SetFillColor(cor::$branco, cor::$branco, cor::$branco);
		$this->pdf->SetXY($legenda_x - 1, $this->pdf->GetY() - 1);
		$this->pdf->Cell ( 6, $ALTURA_CELULA_LEGENDA_CORES,  mb_convert_encoding('MNC = Média do Núcleo Comum', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L', 1 );
		$this->pdf->Ln();
		*/
		
		
		// Legenda fim


		
		
		
		
		$this->pdf->SetDrawColor(cor::$escala_cinza_2, cor::$escala_cinza_2, cor::$escala_cinza_2);



	}

}


class FPDF_Relatorio_matricula extends FPDF {

    public $empresa = 1;

    public function setEmpresa($numero) {
        $this->empresa = $numero;
    }
    function Header()
    {
        // To be implemented in your own inherited class
        $caminho_imagem = 'visao/pagina/conteudo/relatorio/boletim/TOPO2.jpg';

        if($this->empresa == 2) {
            $caminho_imagem = 'visao/pagina/conteudo/relatorio/boletim/TOPO2_EMPRESA2.jpg';
        }

        $quadrado = 25;
        $this->Image($caminho_imagem, 6, 4, 198, 15);
    }
    function Footer()
    {
        // To be implemented in your own inherited class
        $caminho_imagem = 'visao/pagina/conteudo/relatorio/boletim/RODAPEP_LISTRAS.jpg';

        $quadrado = 25;
        $this->Image($caminho_imagem, 6, 286, 198, 3);
    }

}


class relatorio_matricula_pdf extends BaseController {

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

	public function adicionar_vetor($aluno_id, $turma_id) {

		dd($aluno_id);
		
		$cep = util::GET('cep');
		
		
		$altura_linha_texto = 4;
		$altura_linha_separar = $altura_linha_separar;
		
		$tamanho_fonte = 9;
		$aluno = EASYNC5__si_aluno::getByPK($aluno_id);
		
	//		$caminho_imagem = 'visao/pagina/conteudo/relatorio/boletim/logo_boletim.jpg';
		

		
		$this->pdf->AddPage();

		//$this->pdf->Image($caminho_imagem, 10, 10, 95, 1$altura_linha_separar);


		$this->pdf->SetFont ( 'Arial', '', 16 );
        $this->pdf->Cell($this->largura_pagina, 7, mb_convert_encoding("", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $this->pdf->Cell($this->largura_pagina, 7, mb_convert_encoding("", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
		$this->pdf->Cell($this->largura_pagina, 7, mb_convert_encoding("REQUERIMENTO DE MATRÍCULA", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
		
		
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($this->largura_pagina, $altura_linha_texto, mb_convert_encoding("Ilmo. Sr. Diretor", 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($this->largura_pagina, $altura_linha_texto, mb_convert_encoding("O a aluno (a) abaixo qualificado (a), por seu responsável, requer sua matrícula neste Colégio", 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
		
		
		
		
		
		
		
		
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
		$turma = EASYNC5__si_turma::getByPK($turma_id);

		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding(parametro::get_grau( $turma->getId_grau()), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding($turma->getNome(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->Cell($larg3, $altura_linha_texto, mb_convert_encoding(parametro::get_periodo( $turma->getId_periodo()), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		$this->pdf->Cell($larg4, $altura_linha_texto, mb_convert_encoding($turma->getAno(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		
		
		
		
		
		
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
		$this->pdf->Cell($larg_nome, $altura_linha_texto, $this->cortar_texto(mb_convert_encoding($aluno->getNome(), 'ISO-8859-1', 'UTF-8'), $larg_nome), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_mat, $altura_linha_texto, mb_convert_encoding($aluno->getMatricula(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		// quebra de linha:
		$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell(1, 6, "", 0, 0, 'L');
		
		
		
		
		if($aluno->getArquivo_foto()->hasValue()) {
            $caminho_foto = '../../arquivo_foto_aluno/' . $aluno->getArquivo_foto()->value();
            $localh = $_SERVER['SERVER_NAME'] == 'localhost';

            if($localh) {
                $caminho_foto = '../../arquivo_foto_aluno/example.jpg';
            }
			
			$im = imagecreatefromjpeg($caminho_foto);
			if(imagesx($im) != imagesy($im)) {
				$size = min(imagesx($im), imagesy($im));
				$im2 = util::mycrop($im, array('x' => 0, 'y' => 0, 'width' => $size, 'height' => $size));
				if ($im2 !== FALSE) {
					imagejpeg($im2, $caminho_foto, 96);
				}
			}
			
			$this->pdf->Image($caminho_foto, 150, 55, 35);
		}else{
			
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
		$this->pdf->Cell($larg_nasc, $altura_linha_texto, mb_convert_encoding($aluno->getNasc(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_loc_nasc, $altura_linha_texto, mb_convert_encoding($aluno->getCid_nasc(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_rg, $altura_linha_texto, mb_convert_encoding($aluno->getRg(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		
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
		$this->pdf->Cell($larg_end, $altura_linha_texto, mb_convert_encoding($aluno->getEnd(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		
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
		$this->pdf->Cell($larg_bairro, $altura_linha_texto, $this->cortar_texto(mb_convert_encoding($aluno->getBairro(), 'ISO-8859-1', 'UTF-8'), $larg_bairro), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_cidade_estado, $altura_linha_texto, mb_convert_encoding($aluno->getCidade() .'/'. $aluno->getUf(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
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
		
		$pai = $aluno->get_REFERENCE_Si_pai__USING_COLUMN__Fk_pai();
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		//$this->pdf->Cell($larg_cep, $altura_linha_texto, $this->cortar_texto(mb_convert_encoding($cep, 'ISO-8859-1', 'UTF-8'), $larg_cep), 0, 0, 'L');
		$this->pdf->Cell($larg_cep, $altura_linha_texto, $this->cortar_texto(mb_convert_encoding($pai->getRm_resp_financeiro_cep(), 'ISO-8859-1', 'UTF-8'), $larg_cep), 0, 0, 'L');
		

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_fone_residencial, $altura_linha_texto, mb_convert_encoding($aluno->getFone(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
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
		$this->pdf->Cell($larg_end, $altura_linha_texto, mb_convert_encoding($pai->getNome_pai(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		
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
		$this->pdf->Cell($larg_estado_civil, $altura_linha_texto, mb_convert_encoding($pai->getRm_pai_estado_civil(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_loc_nasc, $altura_linha_texto, mb_convert_encoding($pai->getRm_pai_nacionalidade(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_rg, $altura_linha_texto, mb_convert_encoding($pai->getNasc_pai(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		
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
		$this->pdf->Cell($larg_profissao, $altura_linha_texto, mb_convert_encoding($pai->getProfissao_pai(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_pai_rg, $altura_linha_texto, mb_convert_encoding($pai->getRg_pai(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_pai_cpf, $altura_linha_texto, mb_convert_encoding($pai->getCpf_pai(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		
		
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
		$this->pdf->Cell($larg_email, $altura_linha_texto, mb_convert_encoding($pai->getEmail_pai(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_fone_comercial, $altura_linha_texto, mb_convert_encoding($pai->getFone_pai(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_fone_celular, $altura_linha_texto, mb_convert_encoding($pai->getCel_pai(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		
		// quebra de linha:
		$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell(1, 6, "", 0, 0, 'L');
		
		
		
		
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_end, $altura_linha_texto, mb_convert_encoding("Nome da mãe:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_end, $altura_linha_texto, mb_convert_encoding($pai->getNome_mae(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		
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
		$this->pdf->Cell($larg_estado_civil, $altura_linha_texto, mb_convert_encoding($pai->getRm_mae_estado_civil(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_loc_nasc, $altura_linha_texto, mb_convert_encoding($pai->getRm_mae_nacionalidade(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_rg, $altura_linha_texto, mb_convert_encoding($pai->getNasc_mae(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		
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
		$this->pdf->Cell($larg_profissao, $altura_linha_texto, mb_convert_encoding($pai->getProfissao_mae(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_pai_rg, $altura_linha_texto, mb_convert_encoding($pai->getRg_mae(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_pai_cpf, $altura_linha_texto, mb_convert_encoding($pai->getCpf_mae(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		
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
		$this->pdf->Cell($larg_email, $altura_linha_texto, mb_convert_encoding($pai->getEmail_mae(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_fone_comercial, $altura_linha_texto, mb_convert_encoding($pai->getFone_mae(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_fone_celular, $altura_linha_texto, mb_convert_encoding($pai->getCel_mae(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		
		
		
		
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
		$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding($pai->getNome_resp(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding($pai->getFone_resp(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg3, $altura_linha_texto, mb_convert_encoding($pai->getRm_grau_parentesco_responsavel(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		
		
		
		
		
		
		
		// quebra de linha:
		$this->pdf->Cell(1, $altura_linha_separar, "", 0, 0, 'L');
		$this->pdf->Ln();
		$this->pdf->Cell(1, 6, "", 0, 0, 'L');
		
		
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', 'B', $tamanho_fonte );
		$this->pdf->Cell($larg_end, $altura_linha_texto, mb_convert_encoding("Nome do responsável financeiro:", 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->Ln();
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg_end, $altura_linha_texto, mb_convert_encoding($pai->getRm_resp_financeiro_nome(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		
		
		
		
		
		
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
		$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding($pai->getRm_resp_financeiro_rg(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding($pai->getRm_resp_financeiro_cpf(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg3, $altura_linha_texto, mb_convert_encoding($pai->getRm_resp_financeiro_grau_parentesco(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		
		
		
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
		$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding($pai->getRm_resp_financeiro_endereco_correspondencia(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding($pai->getRm_resp_financeiro_bairro(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		
		
		
		
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
		$this->pdf->Cell($larg1, $altura_linha_texto, mb_convert_encoding($pai->getRm_resp_financeiro_cep(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

		
		$this->pdf->SetFont ( 'Arial', '', $tamanho_fonte );
		$this->pdf->Cell($larg2, $altura_linha_texto, mb_convert_encoding($pai->getRm_resp_financeiro_cidade_estado(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
		
		
		
		
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

	

	
	
}

?>
