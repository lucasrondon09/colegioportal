<?php 
$session = \Config\Services::session();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Start Site</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url('assets/admin/plugins/fontawesome-free/css/all.min.css')?>">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="<?= base_url('assets/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css')?>">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url('assets/admin/dist/css/adminlte.min.css')?>">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
    <div class="login-logo mb-0">
    <a href="<?= base_url('Autenticacao/login')?>">
    <img src="<?= base_url('assets/admin/dist/img/logo_portal.png')?>" alt="" width="150"><br>
    <?= $titulo?>
    </a>
    </div>
      <p class="login-box-msg">Faça login para iniciar sua sessão</p>
      <?php
        if(!empty($session->getFlashdata())){
          $alert = $session->getFlashdata();
          
          if(key($alert) == 'success'){
            
            $classAlert = 'success';
            $message    = $session->getFlashdata('success');
          }else{

            $classAlert = 'danger';
            $message    = $session->getFlashdata('error');
          }
        }

        if(isset($alert)):

        ?>    
          <div class="row mt-4">
            <div class="col-12">
              <div class="alert alert-<?= $classAlert;?> alert-dismissible fade show" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                  <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                </svg>
                <?= $message;?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
              </div>
            </div>
          </div>                    
        <?php endif;?>
        <?= form_open(base_url('Admin/Autenticacao/login')) ?>
        <?= csrf_field() ?>
        <div class="input-group mb-3">
          <input type="text" name="sistema" hidden value="<?= $sistema?>">
          <input type="text" class="form-control" placeholder="Login" name="login">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Senha" name="senha">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <!-- /.col -->
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Acessar</button>
          </div>
          <!-- /.col -->
        </div>
        <hr>
        <div class="row ">
          <div class="col-6">
            <?= $link?>
          </div>
          <div class="col-6 text-right">
            <a href="<?= base_url()?>">ir para <b>SITE</b></a>
          </div>
        </div>
        
      <?= form_close() ?>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="<?= base_url('assets/admin/plugins/jquery/jquery.min.js')?>"></script>
<!-- Bootstrap 4 -->
<script src="<?= base_url('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js')?>"></script>
<!-- AdminLTE App -->
<script src="<?= base_url('assets/admin/dist/js/adminlte.min.js')?>"></script>
</body>
</html>
