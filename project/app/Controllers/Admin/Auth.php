<?php

namespace App\Controllers\Admin;

use App\Models\Admin\SI_PaiModel;
use CodeIgniter\Controller;
use App\Models\Admin\SI_UsuarioModel;

class Auth extends Controller
{

    public $data, $model, $session, $modelPai;
    //--------------------------------------------------------------------
    public function __construct()
	{

		$this->model	    = new SI_UsuarioModel();
        $this->modelPai     = new SI_PaiModel();
        $this->session     = session();
    
	}

    public function index($sistema)
    {

        helper('form');

        $this->data['sistema'] = $sistema;

        if($sistema == 'sisaula'){

            $titulo = '<b>SIS</b>AULA';
            $link = "<a href=".base_url('/Admin/Autenticacao/login/sispai').">ir para <b>SISPAI</b></a>";

        }else{
            $titulo = '<b>SIS</b>PAI';
            $link = "<a href=".base_url('/Admin/Autenticacao/login/sisaula').">ir para <b>SISAULA</b></a>";
        }

        $this->data +=   [
                        'titulo' => $titulo,
                        'link'   => $link    
                        ];

        return view('admin/auth/login.php', $this->data);

    }

    public function login()
    {
        helper('form');
        helper('text');
        $alert = NULL;
        $message = NULL;

        if($this->request->getMethod() === 'post'){

            if(csrf_hash() === $this->request->getVar('csrf_test_name'))
            {
                $login = $this->request->getPost('login');
                $senha = $this->request->getPost('senha');
                $sistema = $this->request->getPost('sistema');

                if($sistema == 'sisaula'){
                    $user = $this->model->where('usuario', $login)->where('status', 1)->first();

                    if($user){

                        if($senha == $user->senha){
                            $this->session->userId 	        = $user->id;
                            $this->session->userName	    = $user->nome;
                            $this->session->userLogin	    = $user->usuario;
                            $this->session->userPermissao	= $user->permissao;
                            $this->session->sistema         = $sistema;
                            return redirect()->to(base_url('Admin/home'));  
                        }else{
                            $alert = 'error';
                            $message = 'Usuário ou senha incorretos!';
                        }

                    }else{
                        $alert = 'error';
                        $message = 'Usuário não localizado!';
                    }
                }else{
                    $user = $this->modelPai->where('mat_pai', $login)->first();

                    if($user){

                        if($senha == $user->senha_pai){
                            $this->session->userId 	    = $user->id;
                            $this->session->userName	= !empty($user->nome_pai) ? $user->nome_pai : $user->nome_mae;
                            $this->session->userLogin	= $user->mat_pai;
                            $this->session->sistema     = $sistema;

                            return redirect()->to(base_url('Admin/home'));  
                        }else{
                            $alert = 'error';
                            $message = 'Usuário ou senha incorretos!';
                        }

                    }else{
                        $alert = 'error';
                        $message = 'Usuário não localizado!';
                    }
                }
            }else{
                $alert = 'error';
                $message = 'Falha na autenticação!';
            }

            

            $this->session->setFlashdata($alert, $message);

            return redirect()->to(base_url('Admin/Autenticacao/login/'.$sistema));

        }

    }

    public function logout()
    {

        $this->session->destroy();
        $rota = $this->session->sistema == 'sisaula' ? 'Admin/Autenticacao/login/sisaula' : 'Admin/Autenticacao/login/sispai';
        return redirect()->to(base_url($rota));

    }

}
