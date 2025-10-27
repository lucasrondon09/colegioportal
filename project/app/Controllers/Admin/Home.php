<?php

namespace App\Controllers\Admin;

use App\Models\Admin\SI_AlunoModel;
use App\Models\Admin\SI_AlunoTurmaModel;
use App\Models\Admin\SI_ParametroModel;
use App\Models\Admin\SI_TurmaModel;
use CodeIgniter\Controller;


class Home extends Controller
{

    public $data;

    public function __construct()
	{
		
	}

    public function index()
    {
        helper('auth');
        permission();

        $alunoModel = new SI_AlunoModel();
        $turmaModel = new SI_TurmaModel();

        $this->data['aluno'] =[];
        
        if(session()->sistema == 'sispai'){
            $alunos = $alunoModel->where('fk_pai', session()->userId)->findAll();
            $anoLetivo = (new SI_ParametroModel())->getAnoLetivo();
            foreach($alunos as $alunosItem){
                $alunoId = $alunosItem->id;
                $alunoTurma = (new SI_AlunoTurmaModel())->where('fk_aluno', $alunoId)->findAll();

                foreach($alunoTurma as $alunoTurmaItem){

                    $turma = $turmaModel->find($alunoTurmaItem->fk_turma);

                    if($turma->ano === $anoLetivo){
                        
                        $this->data['aluno'][] = $alunoModel->select('si_aluno.id as alunoId, si_aluno.matricula as alunoMatricula, si_aluno.nome as alunoNome, si_turma.id as turmaId, si_turma.nome as turmaNome, si_turma.ano as turmaAno')
                                                          ->join('si_aluno_turma', 'si_aluno_turma.fk_aluno = si_aluno.id')
                                                          ->join('si_turma', 'si_turma.id = si_aluno_turma.fk_turma')
                                                          ->where('si_turma.id', $turma->id)
                                                          ->where('si_aluno.id', $alunoId)
                                                          ->first(); 
                                                          

                    }

                }
            }

        }else{
            $this->data['alunos'] = '';
        }

        
        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/home/index.php', $this->data);
        echo view('admin/template/footer.php');

    }

    public function sobre()
    {
        helper('auth');
        permission();                      

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/home/sobre.php');
        echo view('admin/template/footer.php');

    }
}
