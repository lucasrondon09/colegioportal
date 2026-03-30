<?php

namespace App\Controllers\Admin;

use App\Models\Admin\SI_TurmaModel;
use App\Models\Admin\SI_AlunoTurmaModel;
use App\Models\Admin\SI_AlunoModel;
use App\Models\Admin\SI_DisciplinaModel;
use App\Models\Admin\SI_FaltaModel;
use App\Models\Admin\SI_LogModel;
use App\Models\Admin\SI_NivelDisciplinaProvaModel;
use App\Models\Admin\SI_NotaModel;
use App\Models\Admin\SI_ParametroModel;
use App\Models\Admin\SI_RecuperacaoModel;
use App\Models\Admin\SI_PaiModel;
use App\Models\Admin\SI_ContratoModel;
use App\Models\Admin\SI_Parcelas_ContratoModel;
use CodeIgniter\Controller;

class SI_Turma extends Controller
{
    private $model, $session, $validation, $data, $alunosTurmaModel, $alunoModel, $paisModel, $contratoModel, $parcelasContratoModel;

    

    //--------------------------------------------------------------------
    public function __construct()
	{
        helper('auth');
        helper('parametros');
        permission();

		$this->model	= new SI_TurmaModel();
        $this->paisModel = new SI_PaiModel();
        $this->contratoModel = new SI_ContratoModel();
        $this->parcelasContratoModel = new SI_Parcelas_ContratoModel();
		$this->alunosTurmaModel	= new SI_AlunoTurmaModel();
		$this->alunoModel	= new SI_AlunoModel();
        $this->session  = session();
        $this->validation = \Config\Services::validation();

        define('GRAUS', [
                            'ei' => 'Educação infantil',
                            'ef' => 'Ensino fundamental',
                            'm' => 'Maternal'
                            ]);

        define('PERIODOS', [
                            'm' => 'MATUTINO',
                            'v' => 'VESPERTINO',
                            'd' => 'DIURNO'
                            ]);

        define('NIVEIS', [
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
                            ]);

    $listaAnos = (new SI_AnoLetivo)->getListaAnos();                            

    $this->data['graus'] = GRAUS;
    $this->data['niveis'] = NIVEIS;
    $this->data['periodos'] = PERIODOS;
    $this->data['listaAnos'] = $listaAnos;

	}

    //--------------------------------------------------------------------
    public function index()
    {
        helper('form');

        if($search = $this->request->getVar('search')){

            $this->data += 	[
                            'table'     => $this->model->like('nome', $search)
                                                        ->orLike('ano', $search)
                                                        ->orderBy('ano', 'desc')
                                                        ->where('status', 1)
                                                        ->paginate(10),

                            'pager'      => $this->model->pager                                                            
                            ];

        }else{

            $this->data += 	[
                            'table'     => $this->model->where('status', 1)->orderBy('ano', 'desc')->paginate(10),
                            'pager'     => $this->model->pager
                            ];
        }

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Turma/index.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    public function create()
    {
        helper('form');
        

        //csrf_hash ();
        
        if($this->request->getMethod() === 'post'){

            if(csrf_hash() === $this->request->getVar('csrf_test_name'))
            {
                $rules = $this->validation->setRules    ([
                                                            'nome'         => ['label' => 'Nome', 'rules' => 'required|min_length[3]|max_length[255]']
                                                        ]);

                if ($this->validation->withRequest($this->request)->run()){

                    if($this->save()){
                    $alert = 'success';
                    $message = 'O registro foi cadastrado com sucesso!';
                    }else{
                    $alert = 'error';
                    $message = 'Não foi possível salvar o registro tente novamente!';
                    }

                    $this->session->setFlashdata($alert, $message);
                    return redirect()->to('/Admin/Turma/cadastrar');
                }

            }else{
                $alert = 'error';
                $message = 'Não foi possível salvar o registro, tente novamente!';

                $this->session->setFlashdata($alert, $message);
                return redirect()->to('/Admin/Turma/cadastrar');
            }

           

        }

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Turma/create.php', $this->data);
        echo view('admin/template/footer.php');

    }


    //--------------------------------------------------------------------
    private function save()
    {

        $fields = 	$this->request->getVar();
        $fields['status'] = 1;

        if($this->model->insert($fields)){

            return true;

        }

        return false;

    }


    //--------------------------------------------------------------------
    public function alunos($idTurma)
    {

        helper('form');

        if($this->request->getMethod() === 'post'){

            $search = $this->request->getVar('search');

            $this->data = 	[
                            'table'     => $this->alunosTurmaModel->select('si_aluno.id, si_aluno.matricula, si_aluno.nome')
                                                                  ->like('nome', $search)
                                                                  ->where('fk_turma', $idTurma)
                                                                  ->join('si_aluno', 'si_aluno.id = si_aluno_turma.fk_aluno')
                                                                  ->orderBy('si_aluno.nome asc')   
                                                                  ->findAll()                                                          
                            ];

        }else{

            $this->data = ['table' => $this->alunosTurmaModel->select('si_aluno.id, si_aluno.matricula, si_aluno.nome')
                                                            ->where('fk_turma', $idTurma)
                                                            ->join('si_aluno', 'si_aluno.id = si_aluno_turma.fk_aluno') 
                                                            ->orderBy('si_aluno.nome asc')  
                                                            ->findAll()
                        ];
        } 
        
        $this->data['idTurma'] = $idTurma;

        $turmaModel = new SI_TurmaModel();
        $anoAtual = date('Y');
        $this->data['turma'] = $turmaModel->where('ano > '.$anoAtual)
                                            ->where('status = 1')
                                            ->findAll();

        $this->data['turmaTransferir'] = $turmaModel->where('ano >= '.$anoAtual)
                                                    ->where('status = 1')
                                                    ->findAll();

        $this->data['turmaAtual'] = $turmaModel->find($idTurma);


        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Turma/alunos.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    public function matriculas($idTurma)
    {

        helper('form');

        if($this->request->getMethod() === 'post'){

            $search = $this->request->getVar('search');

            $this->data = 	[
                            'table'     => $this->alunosTurmaModel->select('si_aluno.id, si_aluno.matricula, si_aluno.nome')
                                                                  ->like('nome', $search)
                                                                  ->where('fk_turma', $idTurma)
                                                                  ->join('si_aluno', 'si_aluno.id = si_aluno_turma.fk_aluno') 
                                                                  ->paginate(10),
                            'pager'      => $this->alunosTurmaModel->pager                                                            
                            ];

        }else{

            $this->data = ['table' => $this->alunosTurmaModel->select('si_aluno.id, si_aluno.matricula, si_aluno.nome')
                                                            ->where('fk_turma', $idTurma)
                                                            ->join('si_aluno', 'si_aluno.id = si_aluno_turma.fk_aluno')   
                                                            ->findAll()
                        ];
        } 
        
        $this->data['idTurma'] = $idTurma;

        $turmaModel = new SI_TurmaModel();
        $anoAtual = date('Y');
        $this->data['turma'] = $turmaModel->where('ano > '.$anoAtual)
                                            ->where('status = 1')
                                            ->findAll();

        $this->data['turmaTransferir'] = $turmaModel->where('ano >= '.$anoAtual)
                                                    ->where('status = 1')
                                                    ->findAll();

        $this->data['turmaAtual'] = $turmaModel->find($idTurma);


        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Turma/matriculas.php', $this->data);
        echo view('admin/template/footer.php');

    }

    public function matriculasSend(){
        $fields = $this->request->getPost();

        foreach($fields['matricular'] as $matriculaItem){

            $field = [
                        'fk_aluno' => $matriculaItem,
                        'fk_turma' => $fields['turmaId']
            ];

            $this->alunosTurmaModel->insert($field);

        }
        

        $alert = 'success';
        $message = 'Matriculas processadas com sucesso!';

        $this->session->setFlashdata($alert, $message);
        return redirect()->to('/Admin/Turma/alunos/matriculas/'.$fields['turmaAtual']);
    }


    //--------------------------------------------------------------------
    public function alunoTransferir()
    {

        $alunoId = $this->request->getVar('alunoId');
        $turmaId = $this->request->getVar('turmaId');
        $turmaAtual = $this->request->getVar('turmaAtual');

        if(empty($turmaId)){

            $alert = 'error';
            $message = 'Transferência não realizada! Selecione a turma para qual deseja transferir o aluno!';

            $this->session->setFlashdata($alert, $message);
            return redirect()->to('/Admin/Turma/alunos/'.$turmaAtual);

        }

        $alunoTurmaModel = new SI_AlunoTurmaModel();
        $nota = new SI_NotaModel();
        $falta  = new SI_FaltaModel();

        $turmaAluno = $alunoTurmaModel->where('fk_aluno', $alunoId)
                                      ->where('fk_turma', $turmaAtual)
                                      ->first();

        $fields = 	['fk_turma'  => $turmaId]; 


        $updateAlunoTurma = $alunoTurmaModel->update((int)$turmaAluno->id, $fields);

        if($updateAlunoTurma){

            $notaAluno = $nota->where('fk_aluno', $alunoId)
                               ->where('fk_turma', $turmaAtual)
                               ->findAll();

            foreach($notaAluno as $notaAlunoItem){

                $updateNota  = $nota->update($notaAlunoItem->id, $fields);

                if(!$updateNota){

                    $alert = 'success';
                    $message = 'O Aluno foi transferido somente a turma, porém não foi possível transferir as notas e faltas.';

                    $this->session->setFlashdata($alert, $message);
                    return redirect()->to('/Admin/Turma/alunos/'.$turmaAtual);

                }

                $faltaAluno = $falta->where('fk_aluno', $alunoId)
                                    ->where('fk_turma', $turmaAtual)
                                    ->findAll();

                foreach($faltaAluno as $faltaAlunoItem){
                    $updateFalta  = $falta->update($faltaAlunoItem->id, $fields);

                    if(!$updateFalta){

                        $alert = 'success';
                        $message = 'O Aluno foi transferido somente a turma e notas, porém não foi possível transferir as faltas.';
    
                        $this->session->setFlashdata($alert, $message);
                        return redirect()->to('/Admin/Turma/alunos/'.$turmaAtual);
    
                    }
                }                    

            }                   

            $alert = 'success';
            $message = 'Aluno transferido com sucesso! Verifique o aluno na turma correspondente a transferência!';

            $this->session->setFlashdata($alert, $message);
            return redirect()->to('/Admin/Turma/alunos/'.$turmaAtual);
        }else{

            $alert = 'error';
            $message = 'Transferência não realizada! Houve um problema no processamento, tente novamente ou entre em contato com o Administrador!';

            $this->session->setFlashdata($alert, $message);
            return redirect()->to('/Admin/Turma/alunos/'.$turmaAtual);
        }

        helper('form');

        if($this->request->getMethod() === 'post'){

            $search = $this->request->getVar('search');

            $this->data = 	[
                            'table'     => $this->alunosTurmaModel->select('si_aluno.id, si_aluno.matricula, si_aluno.nome')
                                                                  ->like('nome', $search)
                                                                  ->where('fk_turma', $idTurma)
                                                                  ->join('si_aluno', 'si_aluno.id = si_aluno_turma.fk_aluno') 
                                                                  ->paginate(10),
                            'pager'      => $this->alunosTurmaModel->pager                                                            
                            ];

        }else{

            $this->data = ['table' => $this->alunosTurmaModel->select('si_aluno.id, si_aluno.matricula, si_aluno.nome')
                                                            ->where('fk_turma', $idTurma)
                                                            ->join('si_aluno', 'si_aluno.id = si_aluno_turma.fk_aluno')   
                                                            ->paginate(10),
                            'pager'     => $this->alunosTurmaModel->pager
                        ];
        } 
        
        $this->data['idTurma'] = $idTurma;

        $turmaModel = new SI_TurmaModel();
        $anoAtual = date('Y');
        $this->data['turma'] = $turmaModel->where('ano > '.$anoAtual)
                                            ->where('status = 1')
                                            ->findAll();

        $this->data['turmaTransferir'] = $turmaModel->where('ano >= '.$anoAtual)
                                                    ->where('status = 1')
                                                    ->findAll();

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Turma/alunos.php', $this->data);
        echo view('admin/template/footer.php');

    }

    public function adicionarAluno($idTurma){

        helper('form');

        if($this->request->getMethod() === 'post'){

            $search = $this->request->getVar('search');

            $this->data = 	[
                            'table'     => $this->alunoModel->like('nome', $search)
                                                            ->where('status', 1)
                                                            ->paginate(10),
                            'pager'      => $this->alunoModel->pager                                                            
                            ];

        }else{
                $this->data =   [
                                'table'    => $this->alunoModel->where('status', 1)->paginate(10),
                                'pager'     => $this->alunoModel->pager
                                ];
        }

        $turmaModel = new SI_TurmaModel();
        $anoAtual = date('Y');
        

        

        $this->data['idTurma'] = $idTurma;                        

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Turma/adicionar-alunos.php', $this->data);
        echo view('admin/template/footer.php');                        

    }

    public function setAluno($idTurma, $idAluno){

        $checkAluno = $this->alunosTurmaModel->where('fk_turma', $idTurma)->where('fk_aluno', $idAluno)->first();

        if($checkAluno){
            $alert = 'error';
            $message = 'O Aluno já está vinculado a esta turma!';
        }else{

            $fields = [
                        'fk_turma' => $idTurma,
                        'fk_aluno'  =>$idAluno
                        ];

            if($this->alunosTurmaModel->insert($fields, false)){
                $alert = 'success';
                $message = 'O registro foi cadastrado com sucesso!';
            }else{
                $alert = 'error';
                $message = 'Não foi possível salvar o registro tente novamente!';
            }
        }


        $this->session->setFlashdata($alert, $message);
        return redirect()->to('/Admin/Turma/adicionar-alunos'.'/'.$idTurma);

    }

    public function delAluno($idTurma, $idAluno){

        $deleteAction = $this->alunosTurmaModel->where('fk_turma', $idTurma)->where('fk_aluno', $idAluno)->delete();

        if($deleteAction){
            $alert = 'success';
            $message = 'Registro excluído com sucesso!';

        }else{
            $alert = 'error';
            $message = 'Não foi possível excluir o registro!';

        }

        $this->session->setFlashdata($alert, $message);
        return redirect()->to('/Admin/Turma/alunos'.'/'.$idTurma);

        

    }


    public function visualizar($id){

        $this->data['visualizar'] = true;

        $this->edit($id);

    }

    public function edit($id)
    {

        helper('form');

        if($this->request->getMethod() === 'post'){

            $rules = $this->validation->setRules    ([
                                                        'nome'         => ['label' => 'Nome', 'rules' => 'required|min_length[3]|max_length[255]']
                                                    ]);                                                  

            if ($this->validation->withRequest($this->request)->run()){

                if($this->update($id)){
                    $alert = 'success';
                    $message = 'O registro foi atualizado com sucesso!';
                }else{
                    $alert = 'error';
                    $message = 'Não foi possível atualizar o registro!';
                }

                $this->session->setFlashdata($alert, $message);
                return redirect()->to('/Admin/Turma/editar/'.$id);

            }
        }

        $this->data += 	[
                        'field'     => $this->model->find($id)
                        ];


        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Turma/edit.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    private function update($id)
    {

        $fields = 	[
                    'nome'  => $this->request->getVar('nome'),
                    'usuario' => $this->request->getVar('usuario'),
                    'senha' => $this->request->getVar('senha'),
                    'permissao' => $this->request->getVar('permissao')
                    ];       
                    
                    
        if(!empty($this->request->getVar('turma_disciplina'))){

            $turmaDisciplina = $this->request->getVar('turma_disciplina');

            $fieldsPTD['fk_professor'] = $id;

            foreach($turmaDisciplina as $turmaDisciplinaItem){

                $arr =  explode("_", $turmaDisciplinaItem);
                $fieldsPTD['fk_turma'] = $arr[0];
                $fieldsPTD['id_disciplina'] = $arr[1];

                
                $this->ProfessorTurmaDisciplinamodel->insert($fieldsPTD);
        
            }
            
        }                    

        return $this->model->update($id, $fields);


    }

    //--------------------------------------------------------------------
    public function delete($id)
    {

        if($this->deleted($id)){
            $alert = 'success';
            $message = 'Registro excluído com sucesso!';

        }else{
            $alert = 'error';
            $message = 'Não foi possível excluir o registro!';

        }

        $this->session->setFlashdata($alert, $message);
        return redirect()->to('/Admin/Turma');

    }

    //--------------------------------------------------------------------
    private function deleted($id)
    {

        $fields['status'] = 2;

        return $this->model->update($id, $fields);

    }


    public function permissoesProfessor(){

        $idProfessor = session()->userId;

        $q = '';

        if((int)session()->userPermissao == 3){
            $q = ' AND u.id = '.$idProfessor;
        }

        $anoLetivo = (new SI_ParametroModel())->getAnoLetivo();


        $turmaDisciplina = $this->model->query('
        SELECT t.id AS turmaId, t.nome AS turmaNome, t.ano AS turmaAno, d.id AS disciplinaId, d.disciplina_id AS disciplinaSigla, d.nome AS disciplinaNome 
        FROM si_turma AS t
        RIGHT JOIN si_professor_turma_disciplina AS pfd ON pfd.fk_turma = t.id
        LEFT JOIN si_disciplina AS d ON pfd.id_disciplina = d.disciplina_id
        LEFT JOIN si_usuario AS u ON pfd.fk_professor = u.id WHERE t.ano = '.$anoLetivo.$q)->getResult();

        $this->data['table'] = $turmaDisciplina;


        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Turma/permissoes-professor.php', $this->data);
        echo view('admin/template/footer.php');


    }

    public function lancarNotas($turmaId, $disciplinaId, $periodoId){

        helper('form');

        $trimestre = $periodoId;
        $tabela['linhas'] =  [];
        $tabela['colunas'] =  [];

        switch ($periodoId) {
            case '1':
                $periodo =  [
                            'id'         => $periodoId,
                            'nome'  => '1º Trimestre'
                            ];
                break;
            case '2':
                $periodo =  [
                            'id'         => $periodoId,
                            'nome'  => '2º Trimestre'
                            ];
                break;
            case '3':
                $periodo =  [
                            'id'         => $periodoId,
                            'nome'  => '3º Trimestre'
                            ];
                break;
            case 'r':
                $periodo =  [
                            'id'         => $periodoId,
                            'nome'  => 'Recuperação'
                            ];
                break;
            
            default:
                break;
        }

        $r = $this->alunoModel->query('
                                            SELECT a.id aluno_id
                                            FROM si_aluno a
                                            
                                            JOIN si_aluno_turma at 
                                            ON a.id = at.fk_aluno
                                            
                                            JOIN si_turma t
                                            ON at.fk_turma = t.id
                                            
                                            
                                            WHERE 
                                                t.id = '.$turmaId.'
                                                AND t.status = 1 
                                                AND a.status = 1
                                                
                                            ORDER BY a.nome')->getResult();

        if($r != null) {


            if($periodoId == 'r'){

                $vetor_colunas = array('Matrícula', 'Aluno', 'Nota recuperação');
                $tabela['colunas'][] =  $vetor_colunas;

            }else{
                $vetor_colunas = array('Matrícula', 'Aluno');
            }



        

       

        $getDisciplina = (new SI_DisciplinaModel())->find($disciplinaId);
        $disciplina = $getDisciplina->disciplina_id;
        $turma = $turmaId;

        if($periodoId <> 'r'){
            $lista_provas = $this->get_lista_provas($disciplina, $turma);
    
            foreach ($lista_provas as $prova) {
                array_push($vetor_colunas, parametro::get_prova($prova));
            }
    
            array_push($vetor_colunas, 'Quantidade de Faltas');
    
            $tabela['colunas'][] =  $vetor_colunas;
        }

        foreach ($r as $id_aluno) {

            $aluno = $this->alunoModel->find($id_aluno->aluno_id); 

            $vetor_linha = array($aluno->matricula, $aluno->nome);
        
            if($periodoId == 'r'){

                $q = "
                SELECT nota 
                FROM si_recuperacao
                WHERE 
                
                fk_aluno = $id_aluno->aluno_id
                AND fk_turma = $turma
                AND id_disciplina = '$disciplina'";

                $r2 = (new SI_RecuperacaoModel())->query($q)->getResultArray();

                $nota = '';
                if($r2 != null) {
                    $nota = $r2[0]['nota'];
                    $nota = str_replace(".", ",", $nota);

                    
                }

                array_push($vetor_linha, '<input class="nota form-control" style="min-width:100px" type="text" name="nota_recuperacao_aluno_id_'.$aluno->id.'" maxlength="5" value="'.$nota.'" />');

            }else{

            

                foreach ($lista_provas as $prova) {


                $q = "
                SELECT nota 
                FROM si_nota 
                WHERE 

                fk_aluno = $id_aluno->aluno_id
                AND fk_turma = $turma
                AND trimestre = $trimestre
                AND id_disciplina = '$disciplina'
                AND id_prova = '$prova'";


                $r2 = (new SI_NotaModel())->query($q)->getResultArray();

                $nota = '';
                if($r2 != null) {
                // este aluno já possui nota lançada para esta turma / trimestre / disciplina / prova. vou pegar estas notas e exibir no formulário:
                $nota = $r2[0]['nota'];
                $nota = str_replace(".", ",", $nota);
                }else{

                    // é a primeira vez que este aluno receberá nota nesta turma / trimestre / disciplina / prova.

                }

                $nome_input_text = 'nota_prova_id_'.$prova.'_aluno_id_'.$aluno->id;
                $checkbox_zero = 'checkbox_zero_' . $nome_input_text;


                array_push($vetor_linha, '

                <input class="nota form-control" style="min-width:100px" type="text" name="'.$nome_input_text.'" id="'.$nome_input_text.'" maxlength="5" value="'.$nota.'" />

                ');

            }

            // coluna de faltas:


            $q = "
            SELECT faltas 
            FROM si_falta
            WHERE 

            fk_aluno = $id_aluno->aluno_id
            AND fk_turma = $turma
            AND trimestre = $trimestre
            AND id_disciplina = '$disciplina'";

            $r2 = (new SI_FaltaModel())->query($q)->getResultArray();

            $faltas = '';
            if($r2 != null) {
            $faltas = $r2[0]['faltas'];
            }

            //echo '<td><input class="faltas" type="text" name="faltas_aluno_id_'.$aluno->getId().'" maxlength="5" value="'.$faltas.'" /></td>';

            array_push($vetor_linha, '<input class="faltas form-control" style="min-width:100px" type="text" name="faltas_aluno_id_'.$aluno->id.'" id="faltas_aluno_id_'.$aluno->id.'" maxlength="10" value="'.$faltas.'" /> ');

            /*
            <span class="div_botoes_ad_diminuir">
            <a class="btn btn-sm btn-light" href="javascript:adicionar_faltas(\'faltas_aluno_id_'.$aluno->id.'\', -1);">-</a>  
            <a class="btn btn-sm btn-light" href="javascript:adicionar_faltas(\'faltas_aluno_id_'.$aluno->id.'\',  1);">+</a>
            </span>
            */
        }

            $tabela['linhas'][] = $vetor_linha;

        }



        }else{
        echo 'Nenhum aluno cadastrado nesta turma.';
        }
                    
       $this->data['tabela'] = $tabela;

       $this->data =    [
                        'turma'     => $this->model->find($turmaId),
                        'periodo'   => $periodo,
                        'tabela'    => $tabela,
                        'disciplina'=> (new SI_DisciplinaModel())->find($disciplinaId)
                        ];

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Turma/lancar-notas.php', $this->data);
        echo view('admin/template/footer.php');                                    

    }

    protected function get_lista_provas($id_disciplina, $id_turma) {
    	      

    	
    	$vetor_provas = array();
    	$vetor_provas_retornar = array();
		
		
		$q = "SELECT id_nivel, ano FROM si_turma WHERE id = $id_turma";
        $r = $this->model->query($q)->getResultArray();

		$nivel = $r[0]['id_nivel'];
		$ano = $r[0]['ano'];
		
		
		$q = "
		SELECT id_prova
		FROM si_nivel_disciplina_prova p
		
		JOIN si_turma t
		ON p.id_nivel = t.id_nivel
		
		WHERE
		t.id = $id_turma
		AND p.id_disciplina = '$id_disciplina'
		AND (
					(t.ano <= 2017 AND p.ano_vigencia = 2017)
				OR	(t.ano >= 2018 AND p.ano_vigencia = t.ano)
			)
		";

		
        $r = (new SI_NivelDisciplinaProvaModel())->query($q)->getResultArray();

		foreach ($r as $v) {
			array_push($vetor_provas, $v['id_prova']);
		}
	
		
			
			
			
		$provas = array(
			's1',
			's2',
			's3',
            's4',
			's',
			'f'
		);
			
    	foreach ($provas as $ordenado) {
			$achou_algum = false;

			foreach ($vetor_provas as $ordenar) {
				if($ordenar == $ordenado) {
					array_push($vetor_provas_retornar, $ordenar);
				}
			}
		}

		return $vetor_provas_retornar;
    }


    public function lancarNotaSave(){

        $fields = $this->request->getPost();
        
        return $this->salvar_notas($fields);

    }

    protected function salvar_notas($fields) {
        
    	
		//$c = EASYNC5__model_conn::get_conn();
			
    	$turma = $fields['turma'];
    	$trimestre = $fields['periodo'];
    	$disciplina = $fields['disciplina'];
    	
        if($trimestre <> 'r'):
    	// atualizando / inserindo as notas:
    	foreach($fields as $chave => $valor) {
    		$apagar_nota = false;
    		$apagar_falta = false;
    		
    		if(preg_match('/^nota_prova_id_(s1|s2|s3|s4|f|s)_aluno_id_([0-9]+)$/', $chave, $matches)) {
    			
    			
    			$prova = $matches[1];
    			$id_aluno = $matches[2];
    			
    			$campo_post = "nota_prova_id_{$prova}_aluno_id_{$id_aluno}";
    			$valor_nota = $fields[$campo_post];
    			$valor_nota = str_replace(",", ".", $valor_nota);
    			
    
    			/*
    			$checkbox_zero = $fields["checkbox_zero_" . $campo_post];
    			if($checkbox_zero != '') {
					// lançou zero.
    				$valor_nota = "0.0";
				}
    			*/
    			
    			if($valor_nota == '') {
    				// sem valor lançado.
    				// apagar do banco se tiver algo.
    				
    				$apagar_nota = true;
    			}
    			
    			$q = "
    			SELECT id 
    			FROM si_nota 
    			
    			WHERE
    			fk_aluno = $id_aluno
				AND fk_turma = $turma
				AND trimestre = $trimestre
				AND id_disciplina = '$disciplina'
				AND id_prova = '$prova'";

                //dd($q);
    			
    			$r = (new SI_NotaModel())->query($q)->getResultArray();
				
				
				//$nota = new EASYNC5__si_nota();
				if($r != null) {
                    //dd($r);
                    $r = $r[0]['id'];
					// já tem nota no banco.
					
                    $nota = (new SI_NotaModel())->find($r);
					//$nota = EASYNC5__si_nota::getByPK($r[0]);
					if($apagar_nota) {
						
						
						$log_aluno = $nota->fk_aluno;
						$log_turma = $nota->fk_turma;
						$log_disciplina = $nota->id_disciplina;
						$log_prova = $nota->id_prova;
						$log_trimestre = $nota->trimestre;
						$log_nota = $nota->nota;
						
						
						//$log = new EASYNC5__si_log();
						$log['data'] = date('Y-m-d H:i:s');
						$log['id_usuario'] = session()->userId;
						$log['tipo'] = 'DELETE';
						$log['texto'] = "Excluiu a nota >> aluno: $log_aluno; turma: $log_turma; disciplina: $log_disciplina; prova: $log_prova; trimestre: $log_trimestre; nota: $log_nota;";
						
                        (new SI_LogModel())->insert($log);

						(new SI_NotaModel())->delete($r);

						//$nota->remove();
					}
                    
					
				}
				
				if($apagar_nota == false) {

                    $nota = array();

					$nota['fk_aluno'] = $id_aluno;
					$nota['fk_turma'] = $turma;
					$nota['id_disciplina'] = $disciplina;
					$nota['id_prova'] = $prova;
					$nota['trimestre'] = $trimestre;
					$nota['nota'] = $valor_nota;

                    
					//$nota->save();
					
					
                    $log_aluno = $nota['fk_aluno'];
                    $log_turma = $nota['fk_turma'];
                    $log_disciplina = $nota['id_disciplina'];
                    $log_prova = $nota['id_prova'];
                    $log_trimestre = $nota['trimestre'];
                    $log_nota = $nota['nota'];
                    /*
					$log_aluno = $nota->getFk_aluno();
					$log_turma = $nota->getFk_turma();
					$log_disciplina = $nota->getId_disciplina();
					$log_prova = $nota->getId_prova();
					$log_trimestre = $nota->getTrimestre();
					$log_nota = $nota->getNota();
					*/
					
					if($r != null) {
                        $log['data'] = date('Y-m-d H:i:s');
						$log['id_usuario'] = session()->userId;
						$log['tipo'] = 'UPDATE';

						//$log = new EASYNC5__si_log();
						//$log->setData('NOW()', true);
						//$log->setId_usuario(modelo__sessao::get_usuario_logado_id());
						//$log->setTipo('UPDATE');
						$log['texto'] = "Atualizou a nota >> aluno: $log_aluno; turma: $log_turma; disciplina: $log_disciplina; prova: $log_prova; trimestre: $log_trimestre; nota: $log_nota;";

                        (new SI_LogModel())->insert($log);
						//$log->save();

                        (new SI_NotaModel())->update($r, $nota);

					}else{

                        $log['data'] = date('Y-m-d H:i:s');
						$log['id_usuario'] = session()->userId;
						$log['tipo'] = 'INSERT';

						//$log = new EASYNC5__si_log();
						//$log->setData('NOW()', true);
						//$log->setId_usuario(modelo__sessao::get_usuario_logado_id());
						//$log->setTipo('INSERT');
						$log['texto'] = "Incluiu a nota >> aluno: $log_aluno; turma: $log_turma; disciplina: $log_disciplina; prova: $log_prova; trimestre: $log_trimestre; nota: $log_nota;";
						
                        (new SI_LogModel())->insert($log);
                        //$log->save();

                        (new SI_NotaModel())->insert($nota);
					}
				}
    		}
    		
    		if(preg_match('/^faltas_aluno_id_([0-9]+)$/', $chave, $matches)) {
    			
    			$id_aluno = $matches[1];
    			
    			
    			
    			$campo_post = "faltas_aluno_id_{$id_aluno}";
    			$valor_faltas = $fields[$campo_post];
    			//echo "$campo_post = $valor_faltas; ";
    			
    			
    			if($valor_faltas === '') {
    				// sem valor lançado.
    				// apagar do banco se tiver algo.
    				
    				$apagar_falta = true;
    			}
    			$valor_faltas = (int)$valor_faltas;
    			
    			
    			$q = "
    			SELECT id 
    			FROM si_falta 
    			
    			WHERE
    			fk_aluno = $id_aluno
				AND fk_turma = $turma
				AND trimestre = $trimestre
				AND id_disciplina = '$disciplina'";
    			
    			$r = (new SI_FaltaModel())->query($q)->getResultArray();
                
				//$r = $c->qcv($q, "id");
				
				//$falta = new EASYNC5__si_falta();
				
				if($r != null) {
                    $r = $r[0]['id'];
					// já tem falta no banco.
					
                    $falta = (new SI_FaltaModel())->find($r);
					//$falta = EASYNC5__si_falta::getByPK($r[0]);
					if($apagar_falta) {
						
						
					
						$log_aluno = $falta->fk_aluno;
						$log_turma = $falta->fk_turma;
						$log_disciplina = $falta->id_disciplina;
						$log_trimestre = $falta->trimestre;
						$log_faltas = $falta->faltas;
						
						
						$log['data'] = date('Y-m-d H:i:s');
						$log['id_usuario'] = session()->userId;
						$log['tipo'] = 'DELETE';

						//$log = new EASYNC5__si_log();
						//$log->setData('NOW()', true);
						//$log->setId_usuario(modelo__sessao::get_usuario_logado_id());
						//$log->setTipo('DELETE');
						$log['texto'] = "Excluiu a falta do aluno >> aluno: $log_aluno; turma: $log_turma; disciplina: $log_disciplina; trimestre: $log_trimestre; faltas: $log_faltas;";

                        (new SI_LogModel())->insert($log);
						//$log->save();
						
                        (new SI_FaltaModel())->delete($r);
						//$falta->remove();
						
						
					}
					
				}else{
					// não tem falta.
					
				}
                
    			if($apagar_falta == false) {

                    $falta = array();

					$falta['fk_aluno'] = $id_aluno;
					$falta['fk_turma'] = $turma;
					$falta['id_disciplina'] = $disciplina;
					$falta['trimestre'] = $trimestre;
					$falta['faltas'] = $valor_faltas;

                    
					//$falta->save();
					
					
				
					$log_aluno = $falta['fk_aluno'];
					$log_turma = $falta['fk_turma'];
					$log_disciplina = $falta['id_disciplina'];
					$log_trimestre = $falta['trimestre'];
					$log_faltas = $falta['faltas'];
					
					
					if($r != null) { 

                        $log['data'] = date('Y-m-d H:i:s');
						$log['id_usuario'] = session()->userId;
						$log['tipo'] = 'UPDATE';

						//$log = new EASYNC5__si_log();
						//$log->setData('NOW()', true);
						//$log->setId_usuario(modelo__sessao::get_usuario_logado_id());
						//$log->setTipo('UPDATE');
						$log['texto'] = "Atualizou a falta do aluno >> aluno: $log_aluno; turma: $log_turma; disciplina: $log_disciplina; trimestre: $log_trimestre; faltas: $log_faltas;";
						
                        (new SI_LogModel())->insert($log);
                        (new SI_FaltaModel())->update($r, $falta);
                        //$log->save();
					}else{

                        $log['data'] = date('Y-m-d H:i:s');
						$log['id_usuario'] = session()->userId;
						$log['tipo'] = 'INSERT';

						//$log = new EASYNC5__si_log();
						//$log->setData('NOW()', true);
						//$log->setId_usuario(modelo__sessao::get_usuario_logado_id());
						//$log->setTipo('INSERT');
						$log['texto'] = "Incluiu a falta do aluno >> aluno: $log_aluno; turma: $log_turma; disciplina: $log_disciplina; trimestre: $log_trimestre; faltas: $log_faltas;";
						(new SI_LogModel())->insert($log);
                        (new SI_FaltaModel())->insert($falta);
					}
						
				}
    		}
    	}

        $alert = 'success';
        $message = 'Notas do trimestre lançadas com sucesso!';

    else:
        
        foreach($fields as $chave => $valor) {

    		$apagar_recuperacao = false;
    		
    		if(preg_match('/^nota_recuperacao_aluno_id_([0-9]+)$/', $chave, $matches)) {
    			
    			
    			$id_aluno = $matches[1];
    			
    			$campo_post = "nota_recuperacao_aluno_id_{$id_aluno}";
    			$valor_nota_rec = $fields[$campo_post];
    			$valor_nota_rec = str_replace(",", ".", $valor_nota_rec);
    			//echo "$campo_post = $valor_nota; ";
    			
    			if($valor_nota_rec == '') {
    				// sem valor lançado.
    				// apagar do banco se tiver algo.
    				
    				$apagar_recuperacao = true;
    			}
    			
    			$q = "
    			SELECT id 
    			FROM si_recuperacao
    			
    			WHERE
    			fk_aluno = $id_aluno
				AND fk_turma = $turma
				AND id_disciplina = '$disciplina'";
    			
    			
				$r = (new SI_RecuperacaoModel())->query($q)->getResultArray();
				
				//$rec = new EASYNC5__si_recuperacao();
				if($r != null) {
					// já tem nota no banco.
					$r = $r[0]['id'];
                    $rec = (new SI_RecuperacaoModel())->find($r);
					//$rec = EASYNC5__si_recuperacao::getByPK($r[0]);
					if($apagar_recuperacao) {
						
						
						
						$log_aluno = $rec->fk_aluno;
						$log_turma = $rec->fk_turma;
						$log_disciplina = $rec->id_disciplina;
						$log_nota = $rec->nota;
						
                        $log['data'] = date('Y-m-d H:i:s');
						$log['id_usuario'] = session()->userId;
						$log['tipo'] = 'DELETE';
						$log['texto'] = "Excluiu a NOTA DE RECUPERAÇÃO >> aluno: $log_aluno; turma: $log_turma; disciplina: $log_disciplina; nota: $log_nota;";
                        (new SI_LogModel())->insert($log);

                        (new SI_RecuperacaoModel())->delete($r);
						
						
						
						
					}
					
				}else{
					// não tem nota.
					
				}
				
				if($apagar_recuperacao == false) {


                    $rec = array();

					$rec['fk_aluno'] = $id_aluno;
					$rec['fk_turma'] = $turma;
					$rec['id_disciplina'] = $disciplina;
					$rec['nota'] = $valor_nota_rec;

                    
					//$falta->save();
					
					
				
					$log_aluno = $rec['fk_aluno'];
					$log_turma = $rec['fk_turma'];
					$log_disciplina = $rec['id_disciplina'];
					$log_nota = $rec['nota'];

					//$rec->setFk_aluno($id_aluno);
					//$rec->setFk_turma($turma);
					//$rec->setId_disciplina($disciplina);
					//$rec->setNota($valor_nota_rec);
					//$rec->save();
					
					
					
						//$log_aluno = $rec->getFk_aluno();
						//$log_turma = $rec->getFk_turma();
						//$log_disciplina = $rec->getId_disciplina();
						//$log_nota = $rec->getNota();
						
					
					
					if($r != null) {
						
                        $log['data'] = date('Y-m-d H:i:s');
						$log['id_usuario'] = session()->userId;
						$log['tipo'] = 'UPDATE';
						$log['texto'] = "Atualizou a NOTA DE RECUPERAÇÃO >> aluno: $log_aluno; turma: $log_turma; disciplina: $log_disciplina; nota: $log_nota;";
						(new SI_LogModel())->insert($log);
                        (new SI_RecuperacaoModel())->update($r, $rec);
						
					}else{
                        $log['data'] = date('Y-m-d H:i:s');
						$log['id_usuario'] = session()->userId;
						$log['tipo'] = 'INSERT';
						$log['texto'] = "Inseriu a NOTA DE RECUPERAÇÃO >> aluno: $log_aluno; turma: $log_turma; disciplina: $log_disciplina; nota: $log_nota;";
						(new SI_LogModel())->insert($log);
                        (new SI_RecuperacaoModel())->insert($rec);
						
					}
				}
    		}
    	}

        $alert = 'success';
        $message = 'Notas de recuperação lançadas com sucesso!';

    endif;

        
            

            $this->session->setFlashdata($alert, $message);
            return redirect()->to('/Admin/Turma/lancar-notas/'.$fields['turma'].'/'.$fields['disciplinaId'].'/'.$fields['periodo']);

    	//echo '<div class="alert alert-success" style="color:#1A311B; font-size:20px;">Notas do trimestre lançadas com sucesso.</div>';
    	//echo '<a class="btn" href="?acao=nota">Voltar</a>';
        
    }

    public function gerarContrato($idAluno, $idTurma){

        echo "entrou";die;

        $aluno = $this->alunoModel->find($idAluno);
        $turma = $this->model->find($idTurma);

        $this->data = [
                        'aluno' => $aluno,
                        'turma' => $turma
                      ];

        echo view('admin/SI_Turma/contrato.php', $this->data);


    }

    //--------------------------------------------------------------------
    public function contrato($id_turma, $id_aluno)
    {
        helper('form');

        $this->data['modo'] = 'cadastrar';
        $this->data['action'] = base_url('/Admin/Turma/alunos/contrato/create');

        $this->data += 	[
                        'turma'     => $this->model->find($id_turma),
                        'aluno'     => $this->alunoModel->find($id_aluno),
                        ];
        $this->data['responsavel'] =  $this->paisModel->find($this->data['aluno']->fk_pai);                

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Turma/contrato.php', $this->data);
        echo view('admin/template/footer.php');

    }

    public function contratoCreate()
    {
        helper('form');

        $alert = 'danger';
        $message = 'Não foi possível gerar o contrato!';

        if($this->request->getMethod() === 'post'){

            $rules = $this->validation->setRules    ([
                                                        'data_inicio'         => ['label' => 'Data Inicio', 'rules' => 'required'],
                                                        'data_fim'         => ['label' => 'Data Final', 'rules' => 'required'],
                                                        'parcelas'         => ['label' => 'Parcelas', 'rules' => 'required'],
                                                        'dia_vencimento'         => ['label' => 'Dia Vencimento', 'rules' => 'required'],
                                                        'valor_total'         => ['label' => 'Valor Total', 'rules' => 'required'],
                                                        'id_aluno'         => ['label' => 'Aluno', 'rules' => 'required'],
                                                        'id_responsavel'         => ['label' => 'Responsável Financeiro', 'rules' => 'required'],
                                                        'id_turma'         => ['label' => 'Turma', 'rules' => 'required']
                                                    ]);                                                  

            if ($this->validation->withRequest($this->request)->run()){

                $fields = 	$this->request->getVar();    

                $fields['valor_total'] = number_format((float)preg_replace('/[^\d]/', '', $fields['valor_total']) / 100, 2, '.', '');
                $fields['status'] = 1; // ativo 
                $fields['numero_contrato'] = $fields['id_aluno'] . $fields['id_responsavel'] . $fields['id_turma'] . date('Y');

                if($id_contrato = $this->contratoModel->insert($fields)){

                    $valorParcela = number_format((float)preg_replace('/[^\d]/', '', $fields['valor_parcela']) / 100, 2, '.', '');
                
                    for ($i = 1; $i <= $fields['parcelas']; $i++) {
                        $dataVencimento = date('Y-m-d', strtotime("+".($i-1)." month", strtotime($fields['data_inicio'])));
                        $parcela = [
                            'id_contrato' => $id_contrato,
                            'numero_parcela' => $i,
                            'valor_parcela' => $valorParcela,
                            'data_vencimento' => $dataVencimento,
                            'status' => 1
                        ];
                        $this->parcelasContratoModel->insert($parcela);
                    }

                    $alert = 'success';
                    $message = 'O contrato foi gerado com sucesso!';
                }


            }    
        }  

        $this->session->setFlashdata($alert, $message);
        
        return redirect()->back();
    }
}
