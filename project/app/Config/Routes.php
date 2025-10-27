<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
//$routes->get('/', 'Home::index');

//Site
//-------------------------------------------------------------------------
$routes->get('/', 'Site\Home::index');
$routes->get('/Radar', 'Site\Home::radar');
$routes->get('/Nossa-Proposta', 'Site\Home::nossaProposta');
$routes->get('/Estrutura', 'Site\Home::estrutura');
$routes->get('/Galeria-de-Fotos', 'Site\Home::galeriaFotos');
$routes->get('/Galeria-de-Fotos/(:any)', 'Site\Home::galeriaFotos/$1');
$routes->get('/Matriculas', 'Site\Home::matriculas');
$routes->get('/Materiais', 'Site\Home::materiais');
$routes->get('/Contatos', 'Site\Home::contatos');
$routes->get('/Dia-a-Dia/(:num)', 'Site\Home::diaPortal/$1');
$routes->get('/Modalidades/(:any)', 'Site\Home::modalidades/$1');

$routes->get('/', 'Site\Home::home');
$routes->get('/Home', 'Site\Home::home');
$routes->post('/Home/newsletter', 'Site\Home::newsletter');
$routes->get('/Sobre', 'Site\QuemSomos::sobre');
$routes->add('/Corpo-Clinico', 'Site\CorpoClinico::index');
$routes->get('/Corpo-Clinico/(:num)', 'Site\CorpoClinico::details/$1');
$routes->add('/Servicos', 'Site\Servicos::index');
$routes->get('/Servicos/(:any)', 'Site\Servicos::servicos/$1');
$routes->add('/Blog', 'Site\Blog::index');
$routes->get('/Blog/(:num)', 'Site\Blog::materia/$1');
$routes->get('/Blog/materia/(:num)', 'Site\Blog::materia/$1');
$routes->get('/Contatos', 'Site\Contatos::contatos');
$routes->post('/Contatos', 'Site\Contatos::sendMail');
$routes->get('/Agendamento-Consulta', 'Site\Contatos::agendamentoConsulta');
$routes->get('/Agendamento-Exames', 'Site\Contatos::agendamentoExame');
$routes->get('/Obrigado', 'Site\Home::obrigado');
$routes->get('/Email', 'Site\Home::email');

//Admin - Start Site
//-------------------------------------------------------------------------
$routes->add('/Admin/home', 'Admin\Home::index');
$routes->add('/Admin/boletim', 'Admin\Home::index');
$routes->get('/Admin/sobre', 'Admin\Home::sobre');
$routes->add('/Admin', 'Admin\Auth::index');
//-------------------------------------------------------------------------
$routes->add('/Admin/Usuarios', 'Admin\SI_Usuarios::index');
$routes->add('/Admin/Usuarios/cadastrar', 'Admin\SI_Usuarios::create');
$routes->add('/Admin/Usuarios/visualizar/(:num)', 'Admin\SI_Usuarios::visualizar/$1');
$routes->add('/Admin/Usuarios/editar/(:num)', 'Admin\SI_Usuarios::edit/$1');
$routes->add('/Admin/Usuarios/excluir/(:num)', 'Admin\SI_Usuarios::delete/$1');
//-------------------------------------------------------------------------
$routes->add('/Admin/Leads', 'Admin\Leads::index');
$routes->add('/Admin/Leads/cadastrar', 'Admin\Leads::create');
$routes->add('/Admin/Leads/editar/(:num)', 'Admin\Leads::edit/$1');
$routes->add('/Admin/Leads/excluir/(:num)', 'Admin\Leads::delete/$1');
//-------------------------------------------------------------------------
$routes->add('/Admin/Autenticacao/login/(:any)', 'Admin\Auth::index/$1');
$routes->add('/Admin/Autenticacao/login/sisaula', 'Admin\Auth::index');
$routes->add('/Admin/Autenticacao/login/sispai', 'Admin\Auth::index');
$routes->post('/Admin/Autenticacao/login', 'Admin\Auth::login');
$routes->add('/Admin/Autenticacao/logout', 'Admin\Auth::logout');
//-------------------------------------------------------------------------
$routes->add('/Admin/Categorias', 'Admin\Categorias::index');
$routes->add('/Admin/Categorias/cadastrar', 'Admin\Categorias::create');
$routes->add('/Admin/Categorias/editar/(:num)', 'Admin\Categorias::edit/$1');
$routes->add('/Admin/Categorias/excluir/(:num)', 'Admin\Categorias::delete/$1');
//-------------------------------------------------------------------------
$routes->add('/Admin/Noticias', 'Admin\Noticias::index');
$routes->add('/Admin/Noticias/cadastrar', 'Admin\Noticias::create');
$routes->add('/Admin/Noticias/editar/(:num)', 'Admin\Noticias::edit/$1');
$routes->add('/Admin/Noticias/excluir/(:num)', 'Admin\Noticias::delete/$1');
$routes->add('/Admin/Noticias/galeria/(:num)', 'Admin\Noticias::galery/$1');
$routes->post('/Admin/Noticias/uploadImagens/(:num)', 'Admin\Noticias::uploadImages/$1');
$routes->get('/Admin/Noticias/deleteImage/(:num)/(:num)', 'Admin\Noticias::deleteImage/$1/$2');
//-------------------------------------------------------------------------
$routes->post('/UploadImage/upload', 'Admin\UploadImage::upload');
//-------------------------------------------------------------------------
$routes->add('/Admin/Servicos', 'Admin\Servicos::index');
$routes->add('/Admin/Servicos/cadastrar', 'Admin\Servicos::create');
$routes->add('/Admin/Servicos/editar/(:num)', 'Admin\Servicos::edit/$1');
$routes->add('/Admin/Servicos/excluir/(:num)', 'Admin\Servicos::delete/$1');
$routes->post('/Admin/Servicos/upload', 'Admin\Servicos::upload');
//-------------------------------------------------------------------------
$routes->add('/Admin/Categorias-galeria', 'Admin\CategoriasGaleria::index');
$routes->add('/Admin/Categorias-galeria/cadastrar', 'Admin\CategoriasGaleria::create');
$routes->add('/Admin/Categorias-galeria/editar/(:num)', 'Admin\CategoriasGaleria::edit/$1');
$routes->add('/Admin/Categorias-galeria/excluir/(:num)', 'Admin\CategoriasGaleria::delete/$1');
//-------------------------------------------------------------------------
$routes->add('/Admin/Galerias', 'Admin\Galeria::index');
$routes->add('/Admin/Galerias/cadastrar', 'Admin\Galeria::create');
$routes->add('/Admin/Galerias/editar/(:num)', 'Admin\Galeria::edit/$1');
$routes->add('/Admin/Galerias/excluir/(:num)', 'Admin\Galeria::delete/$1');
$routes->add('/Admin/Galerias/imagens/(:num)', 'Admin\Galeria::images/$1');
$routes->post('/Admin/Galerias/upload', 'Admin\Galeria::upload');
$routes->post('/Admin/Galerias/uploadImagens/(:num)', 'Admin\Galeria::uploadImages/$1');
$routes->get('/Admin/Galerias/deleteImage/(:num)/(:num)', 'Admin\Galeria::deleteImage/$1/$2');
//-------------------------------------------------------------------------
$routes->add('/Admin/Banners', 'Admin\Banners::index');
$routes->add('/Admin/Banners/cadastrar', 'Admin\Banners::create');
$routes->post('/Admin/Banners/upload', 'Admin\Banners::upload');
$routes->add('/Admin/Banners/editar/(:num)', 'Admin\Banners::edit/$1');
$routes->add('/Admin/Banners/excluir/(:num)', 'Admin\Banners::delete/$1');
//-------------------------------------------------------------------------
$routes->add('/Admin/GoogleAnalytics', 'Admin\Configuracoes::googleAnalytics');
$routes->add('/Admin/MetaTags', 'Admin\Configuracoes::metaTags');
$routes->add('/Admin/RedesSociais', 'Admin\Configuracoes::redesSociais');
$routes->add('/Admin/Email', 'Admin\Configuracoes::email');
$routes->add('/Admin/Email/cadastrar', 'Admin\Configuracoes::emailCreate');
$routes->add('/Admin/Email/editar/(:num)', 'Admin\Configuracoes::emailEdit/$1');
$routes->add('/Admin/Email/excluir/(:num)', 'Admin\Configuracoes::emailDelete/$1');
//-------------------------------------------------------------------------
$routes->add('/Admin/PaginasCategorias', 'Admin\PaginasCategorias::index');
$routes->add('/Admin/PaginasCategorias/cadastrar', 'Admin\PaginasCategorias::create');
$routes->add('/Admin/PaginasCategorias/editar/(:num)', 'Admin\PaginasCategorias::edit/$1');
$routes->add('/Admin/PaginasCategorias/excluir/(:num)', 'Admin\PaginasCategorias::delete/$1');
//-------------------------------------------------------------------------
$routes->add('/Admin/Paginas', 'Admin\Paginas::index');
$routes->add('/Admin/Paginas/cadastrar', 'Admin\Paginas::create');
$routes->add('/Admin/Paginas/editar/(:num)', 'Admin\Paginas::edit/$1');
$routes->add('/Admin/Paginas/excluir/(:num)', 'Admin\Paginas::delete/$1');

// SisAula - Professor
//-------------------------------------------------------------------------
$routes->add('/Admin/Pais', 'Admin\SI_Pai::index');
$routes->add('/Admin/Pais/cadastrar', 'Admin\SI_Pai::create');
$routes->add('/Admin/Pais/editar/(:num)', 'Admin\SI_Pai::edit/$1');
$routes->add('/Admin/Pais/detalhes/(:num)', 'Admin\SI_Pai::details/$1');
$routes->add('/Admin/Pais/excluir/(:num)', 'Admin\SI_Pai::delete/$1');

// SI_Alunos
//-------------------------------------------------------------------------
$routes->add('/Admin/Alunos', 'Admin\SI_Alunos::index');
$routes->add('/Admin/Alunos/(:num)', 'Admin\SI_Alunos::index/$1');
$routes->add('/Admin/Alunos/cadastrar/(:num)', 'Admin\SI_Alunos::create/$1');
$routes->add('/Admin/Alunos/editar/(:num)', 'Admin\SI_Alunos::edit/$1');
$routes->add('/Admin/Alunos/detalhes/(:num)', 'Admin\SI_Alunos::details/$1');
$routes->add('/Admin/Alunos/excluir/(:num)', 'Admin\SI_Alunos::delete/$1');

//SI_AnoLetivo
//-------------------------------------------------------------------------
$routes->get('/Admin/Ano-Letivo', 'Admin\SI_AnoLetivo::index');
$routes->post('/Admin/Ano-Letivo', 'Admin\SI_AnoLetivo::save');

//SI_Turma
//-------------------------------------------------------------------------
$routes->add('/Admin/Turma', 'Admin\SI_Turma::index');
$routes->add('/Admin/Turma/editar/(:num)', 'Admin\SI_Turma::edit/$1');
$routes->add('/Admin/Turma/visualizar/(:num)', 'Admin\SI_Turma::visualizar/$1');
$routes->add('/Admin/Turma/cadastrar', 'Admin\SI_Turma::create');
$routes->add('/Admin/Turma/excluir/(:num)', 'Admin\SI_Turma::delete/$1');
$routes->add('/Admin/Turma/alunos/(:num)', 'Admin\SI_Turma::alunos/$1');
$routes->add('/Admin/Turma/alunos/matriculas/(:num)', 'Admin\SI_Turma::matriculas/$1');
$routes->add('/Admin/Turma/Alunos/matriculas/send', 'Admin\SI_Turma::matriculasSend');
$routes->add('/Admin/Turma/alunos/transferir', 'Admin\SI_Turma::alunoTransferir');
$routes->add('/Admin/Turma/alunos/excluir/(:num)/(:num)', 'Admin\SI_Turma::delAluno/$1/$2');
$routes->add('/Admin/Turma/adicionar-alunos/(:num)', 'Admin\SI_Turma::adicionarAluno/$1');
$routes->add('/Admin/Turma/setAluno/(:num)/(:num)', 'Admin\SI_Turma::setAluno/$1/$2');
$routes->add('/Admin/Turma/permissao-professor', 'Admin\SI_Turma::permissoesProfessor');
$routes->add('/Admin/Turma/lancar-notas/(:num)/(:num)/(:any)', 'Admin\SI_Turma::lancarNotas/$1/$2/$3');
$routes->add('/Admin/Turma/lancar-notas/save', 'Admin\SI_Turma::lancarNotaSave');
$routes->add('/Admin/Turma/alunos/gerar-contrato/(:num)/(:num)', 'Admin\SI_Turma::gerarContrato/$1/$2');
$routes->add('/Admin/Turma/alunos/pdf', 'Admin\SI_Turma::teste_pdf');
$routes->add('/Admin/Turma/alunos/contrato/(:num)/(:num)', 'Admin\SI_Contrato::create/$1/$2');
$routes->add('/Admin/Turma/alunos/contrato/save', 'Admin\SI_Contrato::save');
//$routes->add('/Admin/Turma/alunos/gerar-contrato/pdf/(:num)/(:num)', 'Admin\SI_Relatorios::gerar_contrato_aluno_pdf/$1/$2');


//SI_Contrato
//-------------------------------------------------------------------------
$routes->add('/Admin/Contrato', 'Admin\SI_Contrato::index');
$routes->add('/Admin/Contrato/save', 'Admin\SI_Contrato::save');
$routes->add('/Admin/Contrato/update/(:num)', 'Admin\SI_Contrato::update/$1');
$routes->add('/Admin/Contrato/lancamentos/(:num)', 'Admin\SI_Contrato::lancamentos/$1');
$routes->add('/Admin/Contrato/lancamentos/cadastrar/(:num)', 'Admin\SI_Contrato::lancamentosCadastrar/$1');
$routes->add('/Admin/Contrato/lancamentos/salvar/(:num)', 'Admin\SI_Contrato::lancamentosSalvar/$1');

//SI_Relatorios
//-------------------------------------------------------------------------
$routes->add('/Admin/Relatorios/pais', 'Admin\SI_Relatorios::pais');
$routes->add('/Admin/Relatorios/gerar-contrato/(:num)/(:num)', 'Admin\SI_Relatorios::gerar_contrato_aluno_pdf/$1/$2');
$routes->add('/Admin/Turma/alunos/gerar-contrato', 'Relatorios::index');
$routes->add('/Admin/Relatorios/alunos', 'Admin\SI_Relatorios::relatorio_aluno_montar');
$routes->add('/Admin/Relatorios/livro-matricula', 'Admin\SI_Relatorios::relatorio_aluno_matricula_montar');
$routes->add('/Admin/Relatorios/livro-matricula', 'Admin\SI_Relatorios::relatorio_aluno_matricula_montar');
$routes->add('/Admin/Relatorios/media-turma', 'Admin\SI_Relatorios::media_turma');
$routes->add('/Admin/Relatorios/media-turma/send', 'Admin\SI_Relatorios::relatorio_media_materia');
$routes->add('/Admin/Relatorios/media-alunos', 'Admin\SI_Relatorios::media_alunos');
$routes->add('/Admin/Relatorios/media-alunos/send', 'Admin\SI_Relatorios::relatorio_media_alunos');
$routes->add('/Admin/Relatorios/media-individual-nucleo-comum', 'Admin\SI_Relatorios::media_individual_nucleo_comum');
$routes->add('/Admin/Relatorios/media-individual-nucleo-comum/send', 'Admin\SI_Relatorios::media_individual_nucleo_comum_send');
$routes->add('/Admin/Relatorios/media-individual-todas-disciplinas', 'Admin\SI_Relatorios::media_individual_todas_disciplinas');
$routes->add('/Admin/Relatorios/media-individual-todas-disciplinas/send', 'Admin\SI_Relatorios::media_individual_todas_disciplinas_send');
$routes->add('/Admin/Relatorios/boletim', 'Admin\SI_Relatorios::boletim');
$routes->add('/Admin/Relatorios/boletim-turma', 'Admin\SI_Relatorios::boletim_turma');
$routes->get('/Admin/Relatorios/boletim-turma/gerar-boletim-turma/(:num)', 'Admin\SI_Relatorios::gerar_boletim_turma/$1');
$routes->get('/Admin/Relatorios/boletim-turma/gerar-boletim-turma/(:num)/(:num)', 'Admin\SI_Relatorios::gerar_boletim_turma/$1/$2');
$routes->get('/Admin/Relatorios/boletim-turma/gerar-boletim-turma-ficha/(:num)/(:num)', 'Admin\SI_Relatorios::gerar_boletim_turma_ficha/$1/$2');
$routes->get('/Admin/Relatorios/boletim-turma/notas-trimestrais/(:num)/(:num)/(:num)', 'Admin\SI_Relatorios::notas_trimestrais/$1/$2/$3');
$routes->add('/Admin/Relatorios/listas', 'Admin\SI_Relatorios::listas');
$routes->add('/Admin/Relatorios/listas/send', 'Admin\SI_Relatorios::listas_send');
$routes->add('/Admin/Relatorios/buscaTurmas', 'Admin\SI_Relatorios::buscaTurmas');
//$routes->add('/Admin/Relatorios/requerimento-matricula/(:num)/(:num)', 'Admin\SI_RequerimentoMatricula::adicionar_vetor/$1/$2');
$routes->add('/Admin/Relatorios/requerimento-matricula', 'Admin\SI_RequerimentoMatricula::adicionar_vetor');
$routes->add('/Admin/Relatorios/requerimento-matricula-turma', 'Admin\SI_RequerimentoMatricula::adicionar_vetor_turma');

$routes->add('/Admin/Paginas-Internas/(:num)', 'Admin\SI_PaginasInternas::index/$1');
$routes->add('/Admin/Paginas-Internas/cadastrar/(:num)', 'Admin\SI_PaginasInternas::create/$1');
$routes->add('/Admin/Paginas-Internas/save', 'Admin\SI_PaginasInternas::save');
$routes->add('/Admin/Paginas-Internas/excluir/(:num)/(:num)', 'Admin\SI_PaginasInternas::delete/$1/$2');
/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
