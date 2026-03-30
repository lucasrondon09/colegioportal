<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use App\Models\Admin\GoogleAnalyticsModel;
use App\Models\Admin\MetaTagsModel;
use App\Models\Admin\RedesSociaisModel;
use App\Models\Admin\EmailModel;

class Configuracoes extends Controller
{
    protected $modelGoogleAnalytics;
    protected $modelMetaTags;
    protected $modelRedesSociais;
    protected $modelEmail;
    protected $session;
    protected $validation;
    protected $data;

    //--------------------------------------------------------------------
    public function __construct()
	{
        helper('auth');
        permission();

		$this->modelGoogleAnalytics	= new GoogleAnalyticsModel();
		$this->modelMetaTags	    = new MetaTagsModel();
		$this->modelRedesSociais	= new RedesSociaisModel();
		$this->modelEmail	        = new EmailModel();
        $this->session              = session();
        $this->validation           = \Config\Services::validation();
	}

    //--------------------------------------------------------------------
    public function googleAnalytics()
    {
        helper('form');

        if($this->request->getMethod() === 'post'){

            if(csrf_hash() === $this->request->getVar('csrf_test_name'))
            {
                $rules = $this->validation->setRules    ([
                                                            'script'         => ['label' => 'Script', 'rules' => 'required']
                                                        ]);

                if ($this->validation->withRequest($this->request)->run()){

                    //var_dump($this->request->getVar());exit;

                    if($this->modelGoogleAnalytics->update(1,$this->request->getVar())){
                        $alert = 'success';
                        $message = 'O registro foi cadastrado com sucesso!';
                    }else{
                        $alert = 'error';
                        $message = 'Não foi possível salvar o registro, tente novamente!';
                    }

                    $this->session->setFlashdata($alert, $message);
                    return redirect()->to('/Admin/GoogleAnalytics');
                }

            }else{
                $alert = 'error';
                $message = 'Não foi possível salvar o registro, tente novamente!';

                $this->session->setFlashdata($alert, $message);
                return redirect()->to('/Admin/GoogleAnalytics');
            }

        }

        $this->data =   [
                            'fields'    => $this->modelGoogleAnalytics->get(1)
                        ];
        

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/configuracoes/google-analytics.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    public function metaTags()
    {
        helper('form');

        if($this->request->getMethod() === 'post'){

            if(csrf_hash() === $this->request->getVar('csrf_test_name'))
            {
                $rules = $this->validation->setRules    ([
                                                            'descricao'         => ['label' => 'Descrição', 'rules' => 'required|min_length[3]|max_length[999]'],
                                                            'palavras_chave'            => ['label' => 'Palavras-Chave', 'rules' => 'required|min_length[3]|max_length[999]']
                                                        ]);

                if ($this->validation->withRequest($this->request)->run()){

                    if($this->modelMetaTags->update(1,$this->request->getVar())){
                        $alert = 'success';
                        $message = 'O registro foi cadastrado com sucesso!';
                    }else{
                        $alert = 'error';
                        $message = 'Não foi possível salvar o registro, tente novamente!';
                    }

                    $this->session->setFlashdata($alert, $message);
                    return redirect()->to('/Admin/MetaTags');
                }

            }else{
                $alert = 'error';
                $message = 'Não foi possível salvar o registro, tente novamente!';

                $this->session->setFlashdata($alert, $message);
                return redirect()->to('/Admin/MetaTags');
            }

        }

        $this->data =   [
                            'fields'    => $this->modelMetaTags->get(1)
                        ];
        

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/configuracoes/meta-tags.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    public function redesSociais()
    {

        helper('form');

        if($this->request->getMethod() === 'post'){

            if(csrf_hash() === $this->request->getVar('csrf_test_name'))
            {

                if($this->modelRedesSociais->update(1,$this->request->getVar())){
                    $alert = 'success';
                    $message = 'O registro foi cadastrado com sucesso!';
                }else{
                    $alert = 'error';
                    $message = 'Não foi possível salvar o registro, tente novamente!';
                }

                $this->session->setFlashdata($alert, $message);
                return redirect()->to('/Admin/RedesSociais');

            }else{
                $alert = 'error';
                $message = 'Não foi possível salvar o registro, tente novamente!';

                $this->session->setFlashdata($alert, $message);
                return redirect()->to('/Admin/RedesSociais');
            }

        }

        $this->data =   [
                            'fields'    => $this->modelRedesSociais->get(1)
                        ];
        

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/configuracoes/redes-sociais.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    public function email()
    {
        helper('form');

        if($this->request->getMethod() === 'post'){
            $search = $this->request->getVar('search');

            $this->data = 	[
                            'table'     => $this->modelEmail->like('email', $search)
                                                        ->paginate(10),
                            'pager'      => $this->modelEmail->pager                                                            
                            ];

        }else{

            $this->data = 	[
                            'table'     => $this->modelEmail->paginate(10),
                            'pager'     => $this->modelEmail->pager
                            ];
        }

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/configuracoes/email.php', $this->data);
        echo view('admin/template/footer.php');

    }

    

    //--------------------------------------------------------------------
    public function emailCreate()
    {
        helper('form');
        
        if($this->request->getMethod() === 'post'){

            if(csrf_hash() === $this->request->getVar('csrf_test_name'))
            {
                $rules = $this->validation->setRules    ([
                                                            'email'         => ['label' => 'Email', 'rules' => 'required|min_length[3]|max_length[255]']
                                                        ]);

                if ($this->validation->withRequest($this->request)->run()){

                    if($this->emailSave()){
                    $alert = 'success';
                    $message = 'O registro foi cadastrado com sucesso!';
                    }else{
                    $alert = 'error';
                    $message = 'Não foi possível salvar o registro tente novamente!';
                    }

                    $this->session->setFlashdata($alert, $message);
                    return redirect()->to('/Admin/Email/cadastrar');
                }

            }else{
                $alert = 'error';
                $message = 'Não foi possível salvar o registro, tente novamente!';

                $this->session->setFlashdata($alert, $message);
                return redirect()->to('/Admin/Email/cadastrar');
            }

           

        }

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/configuracoes/email-create.php');
        echo view('admin/template/footer.php');

    }


    //--------------------------------------------------------------------
    private function emailSave()
    {

        $fields = 	$this->request->getPost();

        if($this->modelEmail->insert($fields)){

            return true;

        }

        return false;

    }

    public function emailEdit($id)
    {

        helper('form');

        if($this->request->getMethod() === 'post'){


            $rules = $this->validation->setRules    ([
                                                        'email'         => ['label' => 'Email', 'rules' => 'required|min_length[3]|max_length[255]']
                                                    
                                                    ]);                                                  

            if ($this->validation->withRequest($this->request)->run()){

                if($this->emailUpdate($id)){
                    $alert = 'success';
                    $message = 'O registro foi atualizado com sucesso!';
                }else{
                    $alert = 'error';
                    $message = 'Não foi possível atualizar o registro!';
                }

                $this->session->setFlashdata($alert, $message);
                return redirect()->to('/Admin/Email/editar/'.$id);

            }
        }

        $this->data = 	[
                        'field'     => $this->modelEmail->get($id)
                        ];

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/configuracoes/email-edit.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    private function emailUpdate($id)
    {

        $fields = 	$this->request->getPost();


        if($this->modelEmail->update($id, $fields)){

            return true;

        }

        return false;

    }

    //--------------------------------------------------------------------
    public function emailDelete($id)
    {

        if($this->emailDeleted($id)){
            $alert = 'success';
            $message = 'Registro excluído com sucesso!';

        }else{
            $alert = 'error';
            $message = 'Não foi possível excluir o registro!';

        }

        $this->session->setFlashdata($alert, $message);
        return redirect()->to('/Admin/Email');

    }

    //--------------------------------------------------------------------
    private function emailDeleted($id)
    {

        $delete = $this->modelEmail->delete($id);

        if($delete){

            return true;

        }
    
        return false;

    }


}
