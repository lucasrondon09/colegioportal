<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Colégio Portal</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url()?>/assets/portal/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= base_url()?>/assets/portal/css/style.css">
	<!-- jQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

	<!-- Slick Carousel -->
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

	<!-- Lightbox jQuery -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

</head>
<body>
	
	<!--<div class="container d-none d-xl-block largura1300">-->
	<div class="container d-none d-lg-block">
		<div class="row">
			<div class="col-lg-12">
				<nav class="navbar navbar-expand-lg navbar-light bg-light">
					<a class="navbar-brand" href="<?= base_url()?>"><img src="<?= base_url()?>/assets/portal/img/logo.png" alt="" height="50"></a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>
			
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
						<ul class="navbar-nav me-auto mb-2 mb-lg-0 w-100">
						<div class="d-flex justify-content-around w-100">	
						<li class="nav-item">
							<a class="nav-link" href="<?= base_url('/Nossa-Proposta')?>">NOSSA PROPOSTA</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?= base_url('/Estrutura')?>">ESTRUTURA</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?= base_url('/Matriculas')?>">MATRÍCULAS</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?= base_url('/Contatos')?>">CONTATO</a>
						</li>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
							ACESSO PORTAL
							</a>
							<div class="dropdown-menu">
							<a class="dropdown-item" href="<?= base_url('Admin/Autenticacao/login/sisaula')?>">PROFESSOR</a>
							<a class="dropdown-item" href="<?= base_url('Admin/Autenticacao/login/sispai')?>">PAI/ALUNO</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="<?= base_url('/Materiais')?>">MATERIAL</a>
							<a class="dropdown-item" href="<?= base_url('/Galeria-de-Fotos')?>">GALERIA DE FOTOS</a>
							</div>
						</li>
						</div>
						</ul>
					</div>
				</nav>
			</div>
		</div>
    </div> 
	
	<!-- Visão para dispositivos móveis -->
	<div class="d-lg-none">
		
		<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<a class="navbar-brand" href="<?= base_url()?>"><img src="<?= base_url()?>/assets/portal/img/logo.png" alt="" height="50"></a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarNav">
			<ul class="navbar-nav">
			<li class="nav-item">
				<a class="nav-link" href="<?= base_url('/Nossa-Proposta')?>">NOSSA PROPOSTA</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="<?= base_url('/Estrutura')?>">ESTRUTURA</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="<?= base_url('/Matriculas')?>">MATRÍCULAS</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="<?= base_url('/Contatos')?>">CONTATO</a>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
				ACESSO PORTAL
				</a>
				<div class="dropdown-menu">
				<a class="dropdown-item" href="<?= base_url('Admin/Autenticacao/login/sisaula')?>">PROFESSOR</a>
				<a class="dropdown-item" href="<?= base_url('Admin/Autenticacao/login/sispai')?>">PAI/ALUNO</a>
				<div class="dropdown-divider"></div>
				<a class="dropdown-item" href="<?= base_url('/Materiais')?>">MATERIAL</a>
				<a class="dropdown-item" href="<?= base_url('/Galeria-de-Fotos')?>">GALERIA DE FOTOS</a>
				</div>
			</li>
			</ul>
		</div>
		</nav>
	</div>