<?php

namespace App\Controllers\Site;

use CodeIgniter\Controller;
use App\Models\Admin\NoticiaModel;

class Blog extends Controller
{
    //--------------------------------------------------------------------
    public function __construct()
	{
        $this->model	= new NoticiaModel();
	}

    //--------------------------------------------------------------------
    public function index()
    {

        $this->data =   [
                        'fields'    =>$this->model  ->select('noticias.id as id, noticias.titulo as tituloNoticia, noticias.texto as texto, noticias_categorias.titulo as tituloCategoria, noticias.capa as capa')
                                                    ->join('noticias_categorias', 'noticias.idCategoria = noticias_categorias.id')
                                                    ->where('status', 1)
                                                    ->orderBy('noticias.dataNoticia desc')
                                                    ->paginate(12),
                        'pager'     =>$this->model->pager, 
                        ];
        

        echo view('site/header.php');
        echo view('site/blog.php', $this->data);
        echo view('site/footer.php');

    }

    //--------------------------------------------------------------------
    public function materia($id)
    {

        $this->data = 	[
                        'materia'     => $this->model->select('noticias.id as id, noticias.titulo as titulo, noticias.subtitulo as subtitulo, noticias.texto as texto, noticias_categorias.titulo as tituloCategoria, usuarios.nome as nome, noticias.dataNoticia as dataNoticia')
                                                     ->where('noticias.id', $id)
                                                     ->join('noticias_categorias', 'noticias.idCategoria = noticias_categorias.id')
                                                     ->join('usuarios', 'noticias.idUsuario = usuarios.id')
                                                     ->find(),      
                                                      
                        'noticia'     => $this->model->select('noticias.id as id, noticias.titulo as tituloNoticia, noticias.texto as texto, noticias_categorias.titulo as tituloCategoria, noticias.capa as capa')
                                                     ->join('noticias_categorias', 'noticias.idCategoria = noticias_categorias.id')
                                                     ->limit(3)
                                                     ->orderBy('noticias.dataNoticia desc')
                                                     ->find()
                        ];

        echo view('site/header.php');
        echo view('site/materia.php', $this->data);
        echo view('site/footer.php');

    }




}
