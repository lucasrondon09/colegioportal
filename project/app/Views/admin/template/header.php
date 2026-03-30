
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= constant('CMSNOME');?></title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback'">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url('assets/admin/plugins/fontawesome-free/css/all.min.css');?>">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="<?= base_url('assets/admin/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')?>">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?= base_url('assets/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css')?>">
  <!-- JQVMap -->
  <link rel="stylesheet" href="<?= base_url('assets/admin/plugins/jqvmap/jqvmap.min.css')?>">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url('assets/admin/dist/css/adminlte.min.css')?>">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?= base_url('assets/admin/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')?>">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?= base_url('assets/admin/plugins/daterangepicker/daterangepicker.css')?>">
  <!-- DataTable -->
  <link rel="stylesheet" href="<?= base_url('assets/admin/plugins/datatables-bs5/datatables.min.css')?>">
  <!-- summernote -->
  <link rel="stylesheet" href="<?= base_url('assets/admin/plugins/summernote/summernote-bs4.min.css')?>">
  <link rel="stylesheet" href="<?= base_url('https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.css')?>">

  <script src="<?= base_url('assets/admin/dist/js/jquery-3.6.0.min.js')?>"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>


 


  <link rel="stylesheet" href="https://unpkg.com/dropzone/dist/dropzone.css" />
  <link href="https://unpkg.com/cropperjs/dist/cropper.css" rel="stylesheet"/>
	<script src="https://unpkg.com/dropzone"></script>

  <style>
    a, p, li{
      font-size: 0.9rem;
    }

    [class*=sidebar-dark-] {
    background-color: #091e42 !important;
    }

    .bg-dark, .btn-primary{
      background-color: #0052a5 !important;
    }

    .btn-primary{
      border: none !important;
    }
  </style>

  

</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <!--
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="<?= base_url(constant('CMSLOGO'));?>" alt="Admin Site" height="60" width="60">
  </div>
-->
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="<?= base_url('Admin/home')?>" class="nav-link">Home</a>
      </li>
      <!--
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Suporte</a>
      </li>-->
      <li class="nav-item d-none d-sm-inline-block">
        <a href="<?= base_url('Admin/sobre')?>" class="nav-link">Sobre</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!--
      <li class="nav-item">
        <a class="nav-link" href="#" role="button" data-toggle="tooltip" data-placement="top" title="Acessar o site">
          <i class="fas fa-external-link-alt"></i>
        </a>
      </li>
      -->
      <li class="nav-item">
        <a class="nav-link px-2" href="<?= base_url('Admin/home');?>" role="button" data-toggle="tooltip" data-placement="top" title="Página inicial">
        <i class="fas fa-home"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link px-2" href="<?= base_url();?>" role="button" data-toggle="tooltip" data-placement="top" title="Acessar site">
        <i class="fas fa-external-link-square-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link px-2" href="<?= current_url();?>" role="button" data-toggle="tooltip" data-placement="top" title="Atualizar">
        <i class="fas fa-redo-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link px-2" data-widget="fullscreen" href="#" role="button" data-toggle="tooltip" data-placement="top" title="Expandir tela">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link px-2" href="<?= base_url('Admin/Autenticacao/logout')?>" role="button" data-toggle="tooltip" data-placement="top" title="Sair do sistema">
        <i class="fas fa-sign-out-alt"></i>
        </a>
      </li>
      <li class="border-left nav-item ml-2">
        <a class="nav-link" href="" role="button">
        <i class="fas fa-user-circle pr-2"></i><?= session()->userName;?>
        </a>
      </li>
      
    </ul>
  </nav>
  <!-- /.navbar -->
  