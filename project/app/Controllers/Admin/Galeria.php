<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use App\Models\Admin\GaleriaModel;
use App\Models\Admin\CategoriaGaleriaModel;
use App\Models\Admin\GaleriaImagensModel;
use App\Models\Admin\SI_ParametroModel;
use CodeIgniter\Files\File;


class Galeria extends Controller
{
    protected $model;
    protected $modelCategoria;
    protected $modelImagens;
    protected $session;
    protected $validation;
    protected $data;
    
    //--------------------------------------------------------------------
    public function __construct()
	{
        helper('auth');
        permission();

		$this->model	= new galeriaModel();
        $this->modelCategoria = new CategoriaGaleriaModel();
        $this->modelImagens = new GaleriaImagensModel();
        $this->session  = session();
        $this->validation = \Config\Services::validation();
	}

    //--------------------------------------------------------------------
    public function index()
    {
        helper('form');

        if($search = $this->request->getVar('search')){

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
        echo view('admin/galerias/index.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    public function create()
    {
        helper('form');

       
        
        if($this->request->getMethod() === 'post'){

            if(csrf_hash() === $this->request->getVar('csrf_test_name'))
            {
                $rules = $this->validation->setRules    ([
                                                            'titulo'         => ['label' => 'Título', 'rules' => 'required|min_length[3]|max_length[255]']
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
                    return redirect()->to('/Admin/Galerias/cadastrar');
                }

            }else{
                $alert = 'error';
                $message = 'Não foi possível salvar o registro, tente novamente!';

                $this->session->setFlashdata($alert, $message);
                return redirect()->to('/Admin/Galerias/cadastrar');
            }

           

        }

        $this->data =   [
                        'categoria' => $this->modelCategoria->get()
                        ];

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/galerias/create.php', $this->data);
        echo view('admin/template/footer.php');

    }


    //--------------------------------------------------------------------
    private function save()
    {

        $fields = 	$this->request->getPost();
        $capa = explode('/', $fields['capa']);
        $fields['capa'] = end($capa);
        $fields['ano'] = (new SI_ParametroModel())->getAnoLetivo();

        if($this->model->insert($fields)){

            return true;

        }

        return false;

    }

    public function edit($id)
    {

        helper('form');

        if($this->request->getMethod() === 'post'){


            $rules = $this->validation->setRules    ([
                                                        'titulo'         => ['label' => 'Título', 'rules' => 'required|min_length[3]|max_length[255]']
                                                        
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
                return redirect()->to('/Admin/Galerias/editar/'.$id);

            }
        }

        $this->data = 	[
                        'field'     => $this->model->get($id),
                        'categoria' => $this->modelCategoria->get()
                        ];

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/galerias/edit.php', $this->data);
        echo view('admin/template/footer.php');

    }

    //--------------------------------------------------------------------
    private function update($id)
    {

        $fields = 	$this->request->getPost();
        $capa = explode('/', $fields['capa']);
        $fields['capa'] = end($capa);

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
        return redirect()->to('/Admin/Galerias');

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


    //--------------------------------------------------------------------
    public function images($id)
    {

        helper('form');

        $this->data = 	[
                        'field'     => $this->model->get($id),
                        'images'    => $this->modelImagens->where('idGaleria', $id)->find()
                        ];

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('admin/galerias/images.php', $this->data);
        echo view('admin/template/footer.php');
       
        
    }

    public function uploadImages($id)
    {

        $validationRule = [
        'imagem' => [
            'label' => 'Imagem',
            'rules' => [
                'uploaded[imagem]',
                'is_image[imagem]',
                'mime_in[imagem,image/jpg,image/jpeg,image/gif,image/png,image/webp]'
            ],
        ],
        ];                    

        if ($imagefile = $this->request->getFiles()) {
            foreach ($imagefile['imagem'] as $img) {
                if ($img->isValid() && ! $img->hasMoved()) {
                    $newName = $img->getRandomName();
                    $img->move(FCPATH . 'uploads/img/', $newName);
                    $this->saveImage($newName, $id);
                    $alert = 'success';
                    $message = 'Upload realizado com sucesso';
                }else{
                    $alert = 'error';
                    $message = 'Não foi possível realizar o upload das imagens';
                }

            }
        }


        $this->session->setFlashdata($alert, $message);
        return redirect()->to(base_url('Admin/Galerias/imagens/'.$id));

    }

    //--------------------------------------------------------------------
    private function saveImage($img, $id)
    {

        $fields = 	[
                    'idGaleria' =>$id,
                    'imagem'    => $img
                    ];

        if($this->modelImagens->insert($fields)){

            return true;

        }

        return false;

    }

    //--------------------------------------------------------------------
    public function deleteImage($idImage, $idGaleria)
    {

        if($this->modelImagens->delete($idImage)){
            $this->session->setFlashdata('success', 'Imagem excluída com sucesso!');
            return redirect()->to(base_url('Admin/Galerias/imagens/'.$idGaleria));
        }

        $this->session->setFlashdata('error', 'Não foi possível excluir a imagem!');
        return redirect()->to(base_url('Admin/Galerias/imagens/'.$idGaleria));

    }


}
