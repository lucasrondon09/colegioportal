<?php

namespace App\Controllers\Site;

use CodeIgniter\Controller;
use App\Models\Admin\EmailModel;
use App\Models\Admin\ServicoModel;
use App\Models\Admin\PaginasModel;

class Contatos extends Controller
{

    //--------------------------------------------------------------------
    public function contatos()
    {

        helper('form');

        echo view('site/header');
        echo view('site/contatos');
        echo view('site/footer');

    }

    //--------------------------------------------------------------------
    public function sendMail()
    {

        helper('leads');

        $email = \Config\Services::email();

        $this->modelEmail = new EmailModel();

        $config =       [
            'protocol' => 'smtp',
            'SMTPHost' => 'email-ssl.com.br',
            'SMTPPort' => 465,
            'SMTPUser' => 'site@axonsneurologia.com.br',
            'SMTPPass' => 'Axons@2023',
            'wordWrap' => true,
            'mailType' => 'html', 
            'SMTPCrypto'=> 'ssl'
            ];                

            $assunto = $this->request->getPost('assunto');
            $emailSetTo = $this->modelEmail->where('assunto', $assunto)->find();
          

          

          foreach($emailSetTo as $emailSetToItem){

            $email->initialize($config);

            $email->setFrom('site@axonsneurologia.com.br', 'MENSAGEM DO SITE');
            
            $email->setTo($emailSetToItem->email);

            $template = view('site/template-email', $this->request->getPost());
            
            $email->setSubject($assunto);
            $email->setMessage($template);
            
            $sent = $email->send();


          }

          if(!$sent){
                var_dump($email->printDebugger());exit;


          }else{

            $fields =   [
                        'nome' => $this->request->getPost('nome'),
                        'telefone' => $this->request->getPost('telefone'),
                        'email' => $this->request->getPost('email')
                        ];

            $leads = leadsInsert($fields);

            return redirect()->to('/Obrigado');
          }

    }

    //--------------------------------------------------------------------
    public function agendamentoConsulta()
    {

        helper('form');

        $this->modelPaginas	= new PaginasModel();

        $this->data =   [
                            'medico'   => $this->modelPaginas->where('idCategoria', 1)->find()
                        ];

        echo view('site/header');
        echo view('site/agendamento-consulta', $this->data);
        echo view('site/footer');

    }

    //--------------------------------------------------------------------
    public function agendamentoExame()
    {

        helper('form');

        $this->modelServico = new ServicoModel();

        $this->data = ['servico' => $this->modelServico->get()];

        echo view('site/header');
        echo view('site/agendamento-exame', $this->data);
        echo view('site/footer');

    }




}
