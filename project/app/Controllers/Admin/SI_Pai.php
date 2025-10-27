<?php

namespace App\Controllers\Admin;

use App\Models\Admin\SI_PaiModel;
use CodeIgniter\Controller;
use App\Models\Admin\SI_PaiModelModel;


class SI_Pai extends Controller
{
    public $modelSI_Pai, $data, $validation, $session;
    protected $helpers = ['form',  'auth'];
    //--------------------------------------------------------------------
    public function __construct()
	{
        helper($this->helpers);
        permission();
        $this->modelSI_Pai = new SI_PaiModel();
        $this->validation = \Config\Services::validation();
        $this->session  = session();
	}

    //--------------------------------------------------------------------
    public function index()
    {

        helper('system');
        authSystem();

        $pais = $this->modelSI_Pai->findAll();

        if($search = $this->request->getVar('search')){

            $this->data = 	[
                            'table'     => $this->modelSI_Pai->where('status', 1)
                                                            ->like('nome_pai', $search)
                                                            ->orLike('nome_mae', $search)
                                                            ->paginate(10),

                            'pager'      => $this->modelSI_Pai->pager                                                            
                            ];

        }else{

            $this->data = 	[
                            'table'     => $this->modelSI_Pai->paginate(10),
                            'pager'     => $this->modelSI_Pai->pager
                            ];
        }

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Pais/index.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    public function create()
    {

        helper('system');
        authSystem();
        
        if($this->request->getMethod() === 'post'){

            if(csrf_hash() === $this->request->getVar('csrf_test_name'))
            {
                $rules = $this->validation->setRules    ([
                                                            
                                                            'senha_pai'        => ['label' => 'Login', 'rules' => 'required|min_length[3]|max_length[255]']
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
                    return redirect()->to('/Admin/Pais/cadastrar');
                }

            }else{
                $alert = 'error';
                $message = 'Não foi possível salvar o registro, tente novamente!';

                $this->session->setFlashdata($alert, $message);
                return redirect()->to('/Admin/Pais/cadastrar');
            }

           

        }

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Pais/create.php');
        echo view('admin/template/footer.php');

    }


    //--------------------------------------------------------------------
    private function save()
    {
        

            $ultimo_registro = $this->modelSI_Pai->select('id')->orderBy('id', 'DESC')->first();

            $ultimo_registro = (int)$ultimo_registro->id+1;
            //dd($ultimo_registro.date('Y'));

        $fields = 	$this->request->getVar();   


        $nasc_pai = $this->request->getVar('nasc_pai');
        $nasc_mae = $this->request->getVar('nasc_mae');
        $nasc_resp = $this->request->getVar('nasc_resp');

        // Converte a data para o formato 'dd/mm/YYYY'
        //$datePai = Time::createFromFormat('Y-m-d', $nasc_pai);
        $datePai = strtotime($nasc_pai);
        $formattedDatePai = $datePai ? date('d/m/Y', $datePai) : ''; // Converte para 'dd/mm/YYYY'

        //$dateMae = Time::createFromFormat('Y-m-d', $nasc_mae);
        $dateMae = strtotime($nasc_mae);
        $formattedDateMae = $dateMae ? date('d/m/Y', $dateMae) : ''; // Converte para 'dd/mm/YYYY'

        //$dateResp = Time::createFromFormat('Y-m-d', $nasc_resp);
        $dateResp = strtotime($nasc_resp);
        $formattedDateResp = $dateResp ? date('d/m/Y', $dateResp) : ''; // Converte para 'dd/mm/YYYY'


        $fields += 	[
                    'mat_pai'  => $ultimo_registro.date('Y'),
                    'status'  => 1
                    ];

        $fields['nasc_pai'] = $formattedDatePai;
        $fields['nasc_mae'] = $formattedDateMae;
        $fields['nasc_resp'] = $formattedDateResp;

                    

        if($this->modelSI_Pai->insert($fields)){

            return true;

        }

        return false;

    }

    //--------------------------------------------------------------------
    public function details($id)
    {


        if(session()->sistema == 'sispai'){

            if($id <> session()->userId){

                return redirect()->back();

            }
        }

        $this->data = 	[
                        'field'     => $this->modelSI_Pai->find($id)
                        ];

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Pais/details.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    public function edit($id)
    {

        if(session()->sistema == 'sispai'){

            if($id <> session()->userId){

                return redirect()->back();

            }
        }

        if($this->request->getMethod() === 'post'){

            $rules = $this->validation->setRules    ([
                                                        'senha_pai'        => ['label' => 'Login', 'rules' => 'required|min_length[3]|max_length[255]']
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
                return redirect()->to('/Admin/Pais/editar/'.$id);

            }
        }

        $this->data = 	[
                        'field'     => $this->modelSI_Pai->find($id)
                        ];

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/SI_Pais/edit.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    private function update($id)
    {

        $fields = 	$this->request->getVar();        


        return $this->modelSI_Pai->update($id, $fields);


    }

    //--------------------------------------------------------------------
    public function delete($id)
    {
        helper('system');
        authSystem();

        if($this->deleted($id)){
            $alert = 'success';
            $message = 'Registro excluído com sucesso!';

        }else{
            $alert = 'error';
            $message = 'Não foi possível excluir o registro!';

        }

        $this->session->setFlashdata($alert, $message);
        return redirect()->to('/Admin/Pais');

    }

    //--------------------------------------------------------------------
    private function deleted($id)
    {
        $fields['status'] = 2;

        return $this->modelSI_Pai->update($id, $fields);

    }


}
