<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use App\Models\Admin\BannerModel;
use CodeIgniter\Files\File;

class Banners extends Controller
{
    protected $model;
    protected $session;
    protected $validation;
    protected $data;
    
    //--------------------------------------------------------------------
    public function __construct()
	{
        helper('auth');
        permission();

		$this->model	= new BannerModel();
        $this->session  = session();
        $this->validation = \Config\Services::validation();
	}

    //--------------------------------------------------------------------
    public function index()
    {
        helper('form');

        if($this->request->getMethod() === 'post'){
            $search = $this->request->getVar('search');

            $this->data = 	[
                            'table'     => $this->model->like('titulo', $search)
                                                        ->paginate(10),
                            'pager'      => $this->model->pager                                                            
                            ];

        }else{

            $this->data = 	[
                            'table'     => $this->model->paginate(10),
                            'pager'     => $this->model->pager
                            ];
        }

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/banners/index.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    public function create()
    {
        helper('form');

       
        
        if($this->request->getMethod() === 'post'){


            if(csrf_hash() === $this->request->getVar('csrf_test_name'))
            {
                
                
                $rules = $this->validation->setRules([     'titulo'         => ['label' => 'Título', 'rules' => 'required|min_length[3]|max_length[255]'],
                                                            'imagem'        => [
                                                                                'label' => 'Banner grande',
                                                                                'rules' => [    
                                                                                                'uploaded[imagem]',
                                                                                                'is_image[imagem]',
                                                                                                'mime_in[imagem,image/jpg,image/jpeg,image/gif,image/png,image/webp]',
                                                                                                'max_size[imagem,16000]',
                                                                                            ],
                                                                                ],
                                                            'imagem_responsiva'        => [
                                                                                'label' => 'Banner para dispositivos móveis',
                                                                                'rules' => [    
                                                                                                'uploaded[imagem]',
                                                                                                'is_image[imagem]',
                                                                                                'mime_in[imagem,image/jpg,image/jpeg,image/gif,image/png,image/webp]',
                                                                                                'max_size[imagem,16000]',
                                                                                            ],
                                                                                ],
                                                    ]);

                if ($this->validation->withRequest($this->request)->run()){

                    if($this->save()){
                        $alert = 'success';
                        $message = 'O registro foi cadastrado com sucesso!';
                    }else{
                        $alert = 'error';
                        $message = 'Não foi possível salvar o registro, tente novamente!';
                    }

                    $this->session->setFlashdata($alert, $message);
                    return redirect()->to('/Admin/Banners/cadastrar');
                }

            }else{
                $alert = 'error';
                $message = 'Não foi possível salvar o registro, tente novamente!';

                $this->session->setFlashdata($alert, $message);
                return redirect()->to('/Admin/Banners/cadastrar');
            }

           

        }

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/banners/create.php');
        echo view('admin/template/footer.php');

    }


    //--------------------------------------------------------------------
    private function save()
    {

        $fields      = 	$this->request->getPost();
        $img         = $this->request->getFile('imagem');
        $imgSmall    = $this->request->getFile('imagem_responsiva');

        if ($img->isValid() && !$img->hasMoved() && $imgSmall->isValid() && !$imgSmall->hasMoved()) {
            
            $newName        = $img->getRandomName();
            $newNameSmall   = $imgSmall->getRandomName();

            $img->move(FCPATH . 'uploads/img/', $newName);
            $imgSmall->move(FCPATH . 'uploads/img/', $newNameSmall);

            $fields =   [
                        'titulo'                => $this->request->getVar('titulo'),
                        'link'                  => $this->request->getVar('link'),
                        'texto'                 => $this->request->getVar('texto'),
                        'posicao'               => $this->request->getVar('posicao'),
                        'status'                => $this->request->getVar('status'),
                        'imagem'                => base_url().'/uploads/img/'.$newName,
                        'imagem_responsiva'     => base_url().'/uploads/img/'.$newNameSmall
                        ];                   

            if($this->model->insert($fields)){

                return true;
    
            }
        }    

        return false;

    }

    public function edit($id)
    {

        helper('form');

        if($this->request->getMethod() === 'post'){

            $img    = $this->request->getFile('imagem');

            if($img->getSize() != 0){

                $rules = $this->validation->setRules([     'titulo'         => ['label' => 'Título', 'rules' => 'required|min_length[3]|max_length[255]'],
                                                            'imagem'         => [
                                                                                'label' => 'Banner grande',
                                                                                'rules' => [
                                                                                                'is_image[imagem]',
                                                                                                'mime_in[imagem,image/jpg,image/jpeg,image/gif,image/png,image/webp]',
                                                                                                'max_size[imagem,16000]',
                                                                                            ],
                                                                                ],
                                                            'imagem_responsiva'         => [
                                                                                'label' => 'Banner para dispositivos móveis',
                                                                                'rules' => [
                                                                                                'is_image[imagem]',
                                                                                                'mime_in[imagem,image/jpg,image/jpeg,image/gif,image/png,image/webp]',
                                                                                                'max_size[imagem,16000]',
                                                                                            ],
                                                                                ]
                                                    ]);

                
            }else{
                $rules = $this->validation->setRules([     'titulo'         => ['label' => 'Título', 'rules' => 'required|min_length[3]|max_length[255]']]);
            }                                                 

            if ($this->validation->withRequest($this->request)->run()){

                if($this->update($id)){
                    $alert = 'success';
                    $message = 'O registro foi atualizado com sucesso!';
                }else{
                    $alert = 'error';
                    $message = 'Não foi possível atualizar o registro!';
                }

                $this->session->setFlashdata($alert, $message);
                return redirect()->to('/Admin/Banners/editar/'.$id);

            }
        }

        $this->data = 	[
                        'field'     => $this->model->get($id)
                        ];

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/banners/edit.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    private function update($id)
    {

        


        $fields =   [
                    'titulo'        => $this->request->getVar('titulo'),
                    'link'          => $this->request->getVar('link'),
                    'texto'         => $this->request->getVar('texto'),
                    'posicao'       => $this->request->getVar('posicao'),
                    'status'        => $this->request->getVar('status')
                    ];
        
        $img    = $this->request->getFile('imagem');
        $imgSmall    = $this->request->getFile('imagem_responsiva');

        if($img->getSize() != 0){


            if ($img->isValid() && ! $img->hasMoved()) {

                $newName = $img->getRandomName();
    
                $img->move(FCPATH . 'uploads/img/', $newName);
    
                $fields +=     [
                                'imagem'        => base_url().'/uploads/img/'.$newName
                                ];

            }
        }

        if($imgSmall->getSize() != 0){


            if ($imgSmall->isValid() && !$imgSmall->hasMoved()) {

                $newNameSmall = $imgSmall->getRandomName();
    
                $imgSmall->move(FCPATH . 'uploads/img/', $newNameSmall);
    
                $fields +=     [
                                'imagem_responsiva'        => base_url().'/uploads/img/'.$newNameSmall
                                ];

            }
        }

        if($this->model->update($id, $fields)){
    
            return true;

        }

        return false;


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
        return redirect()->to('/Admin/Banners');

    }

    //--------------------------------------------------------------------
    private function deleted($id)
    {

        $delete = $this->model->delete($id);

        if($delete){

            return true;

        }
    
        return false;

    }


}
