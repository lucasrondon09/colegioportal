<?php

namespace App\Controllers\Site;

use CodeIgniter\Controller;
use App\Models\Admin\NoticiaModel;
use App\Models\Admin\LeadsModel;
use App\Models\Admin\BannerModel;
use App\Models\Admin\ServicoModel;
use App\Models\Admin\GaleriaImagensModel;
use App\Models\Admin\GaleriaModel;
use App\Models\Admin\PaginasModel;
use App\Models\Admin\SITE_ArquivoModel;
use App\Models\Admin\SITE_PaginaArquivoModel;

class Home extends Controller
{

    public $modelBanner, $validation, $data, $modelGaleriaImagens, $modelPaginasModel, $modelServico, $modelGaleria;
    //--------------------------------------------------------------------
    public function __construct()
	{
        $this->modelBanner	= new BannerModel();
        $this->modelGaleriaImagens	= new GaleriaImagensModel();
        $this->modelGaleria	= new GaleriaModel();
        $this->modelPaginasModel	= new PaginasModel();
        $this->modelServico	        = new ServicoModel();
        $this->validation = \Config\Services::validation();
	}


    //--------------------------------------------------------------------
    public function index()
    {
        $img            = $this->modelGaleriaImagens->where('idGaleria', 1)->findAll();
        $modalidades    = $this->modelGaleriaImagens->where('idGaleria', 2)->findAll();
        $radar          = $this->modelPaginasModel->where('idCategoria', 2)->findAll();
        $educacao_infantil    = $this->modelPaginasModel->find(26);
        $fundamental_1    = $this->modelPaginasModel->find(27);
        $fundamental_2    = $this->modelPaginasModel->find(28);

        $banners        = $this->modelBanner->where('status', 1)
                                            ->orderBy('posicao asc')
                                            ->find();


        $this->data = 	[
                        'banner'            => $banners,
                        'img'               => $img,
                        'modalidades'       => $modalidades,
                        'educacao_infantil' => $educacao_infantil,
                        'fundamental_1'     => $fundamental_1,
                        'fundamental_2'     => $fundamental_2
                        ];


        echo view('site/header.php', $this->data);
        echo view('site/index.php', $this->data);
        echo view('site/footer.php', $this->data);

    }

    public function gridPortal(){

       return $this->modelServico->findAll();

    }

    //--------------------------------------------------------------------
    public function nossaProposta()
    {
        $page          = $this->modelPaginasModel->find(25);


        $this->data = 	[
                        'page'            => $page,
                        ];



        echo view('site/header.php', $this->data);
        echo view('site/nossa-proposta.php', $this->data);
        echo view('site/footer.php', $this->data);

    }

    //--------------------------------------------------------------------
    public function radar()
    {
        $radar          = $this->modelPaginasModel->where('idCategoria', 2)->findAll();


        $this->data = 	[
                        'radar'            => $radar,
                        ];


        echo view('site/header.php', $this->data);
        echo view('site/radar.php', $this->data);
        echo view('site/footer.php', $this->data);

    }

    

    //--------------------------------------------------------------------
    public function modalidades($modalidade)
    {

        switch ($modalidade){
            case 'educacao-infantil':
                $id = 26;
                break;
            case 'ensino-fundamental-1':
                $id = 27;
                break;
            case 'ensino-fundamental-2':
                $id = 28;
                break;
            default:
                $id = 26;
                break;
        }

        $page          = $this->modelPaginasModel->find($id);


        $this->data = 	[
                        'page'            => $page,
                        ];


        echo view('site/header.php', $this->data);
        echo view('site/modalidades.php', $this->data);
        echo view('site/footer.php', $this->data);

    }

    //--------------------------------------------------------------------
    public function materiais()
    {



        $this->data['materiais'] = (new SITE_ArquivoModel())->select('site_arquivo.descricao, site_arquivo.nome_arquivo')
                                                            ->join('site_pagina_arquivo', 'site_arquivo.id = site_pagina_arquivo.fk_arquivo')
                                                            ->where('site_pagina_arquivo.fk_pagina', 2)
                                                            ->orderBy('site_arquivo.ordem', 'asc')
                                                            ->findAll();                                                        
 
        echo view('site/header.php');
        echo view('site/materiais.php',$this->data);
        echo view('site/footer.php');

    }

    //--------------------------------------------------------------------
    public function estrutura()
    {

        $galeria = $this->modelGaleria->where('idCategoria', 9)->where('status', 1)->findAll();
    
        $this->data['galeria'] = $galeria;
 
        echo view('site/header.php');
        echo view('site/estrutura.php',$this->data);
        echo view('site/footer.php');

    }

    //--------------------------------------------------------------------
    public function galeriaFotos($anoGaleria = null)
    {

        

        $ano = $this->modelGaleria->select('ano')->distinct()->where('idCategoria', 10)->where('status', 1)->orderBy('ano', 'ASC')->findAll();
        
        if(empty($anoGaleria)){

            $getAno = end($ano);
            $anoGaleria = $getAno->ano;
        }

        $galeria = $this->modelGaleria->where('idCategoria', 10)->where('status', 1)->where('ano', $anoGaleria)->findAll();
    
        $this->data['galeria'] = $galeria;
        $this->data['ano'] = $ano;
 
        echo view('site/header.php');
        echo view('site/galeria-fotos.php',$this->data);
        echo view('site/footer.php');

    }

    //--------------------------------------------------------------------
    public function imagensGaleria($idGaleria)
    {

        //dd($idGaleria);
        $img = $this->modelGaleriaImagens->where('idGaleria', $idGaleria)->findAll();
    
        return $img;

    }

    //--------------------------------------------------------------------
    public function contatos()
    {
        $page          = $this->modelPaginasModel->find(24);


        $this->data = 	[
                        'page'            => $page,
                        ];


        echo view('site/header.php', $this->data);
        echo view('site/contatos.php', $this->data);
        echo view('site/footer.php', $this->data);

    }

    //--------------------------------------------------------------------
    public function matriculas()
    {
        $page          = $this->modelPaginasModel->find(22);


        $this->data = 	[
                        'page'            => $page,
                        ];


        echo view('site/header.php', $this->data);
        echo view('site/matriculas.php', $this->data);
        echo view('site/footer.php', $this->data);

    }

    //--------------------------------------------------------------------
    public function diaPortal($params)
    {
        $page          = $this->modelServico->find($params);


        $this->data = 	[
                        'page'            => $page,
                        ];


        echo view('site/header.php', $this->data);
        echo view('site/dia-a-dia.php', $this->data);
        echo view('site/footer.php', $this->data);

    }

    //--------------------------------------------------------------------
    public function home()
    {

        helper('form');

        $this->model	= new NoticiaModel();
        $this->modelBanner	= new BannerModel();
        $this->modelServico	= new ServicoModel();

        $this->data = 	[
                        'noticia'           => $this->model->select('noticias.id as id, noticias.titulo as tituloNoticia, noticias.texto as texto, noticias_categorias.titulo as tituloCategoria, noticias.capa as capa')
                                                     ->join('noticias_categorias', 'noticias.idCategoria = noticias_categorias.id')
                                                     ->where('status', 1)
                                                     ->limit(3)
                                                     ->orderBy('noticias.dataNoticia desc')
                                                     ->find(),
                        'banner'            =>$this->modelBanner->where('status', 1)
                                                            ->orderBy('posicao asc')
                                                            ->find(),
                        'servicos'          =>$this->modelServico->where('status', 1)->findAll()
                                                                                    

                        ];

        echo view('site/header.php', $this->data);
        echo view('site/home.php');
        echo view('site/footer.php');

    }

    //--------------------------------------------------------------------
    public function newsletter()
    {
        helper('leads');
       

        if($this->request->getMethod() === 'post'){

            
                $fields = $this->request->getVar();

                $leads = leadsInsert($fields);

                if(!$leads){

                    echo view('site/header.php');
                    echo view('site/obrigado.php');
                    echo view('site/footer.php');
                    
                }

            echo view('site/header.php');
            echo view('site/obrigado.php');
            echo view('site/footer.php');

        }
    }

    //--------------------------------------------------------------------
    public function obrigado()
    {

        echo view('site/header.php');
        echo view('site/obrigado.php');
        echo view('site/footer.php');

    }

    //--------------------------------------------------------------------
    public function email()
    {

        return view('site/template-email.php');

    }



}
