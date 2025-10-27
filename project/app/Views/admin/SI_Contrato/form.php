<?php

use App\Models\Admin\SI_DisciplinaModel;
use App\Models\Admin\SI_ParametroModel;
use App\Models\Admin\SI_ProfessorTurmaModel;
use App\Models\Admin\SI_TurmaModel;

  $session = \Config\Services::session();
  $validate = \Config\Services::validation();
?>
  
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Usuários</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/home')?>">Home</a></li>
              <li class="breadcrumb-item">Turmas</li>
              <li class="breadcrumb-item ">Alunos</li>
              <li class="breadcrumb-item active">Contrato</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card">
              <h3 class="card-title mt-3">
                <a href="<?= base_url('/Admin/Usuarios')?>" class="text-decoration-none text-dark">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                  </svg>
                </a>               
                Dados do Registro
              </h3>
              <!-- form start -->
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
                <div class="row mt-4 px-3">
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
              <div class="row">
                <div class="col-12">
                  <span class="text-danger"><?= $validate->listErrors(); ?></span>
                </div>
              </div>
              <?= form_open() ?>
                <form>
                  <fieldset disabled>
              <?= csrf_field() ?>
                <div class="card-body">
                  <div class="form-group">
                    <label for="Aluno">Aluno</label>
                    <input type="text" class="form-control" id="aluno" name="aluno" value="">
                    <input type="text" class="form-control" id="id_aluno" name="id_aluno" value="" hidden>
                  </div>
                  <div class="form-group">
                    <label for="Responsavel">Responsável</label>
                    <input type="text" class="form-control" id="responsavel" name="responsavel" value="">
                    <input type="text" class="form-control" id="id_responavel" name="id_responsavel" value="" hidden>
                  </div>
                  <div class="form-group">
                    <label for="Turma">Turma</label>
                    <input type="text" class="form-control" id="turma" name="turma" value="">
                    <input type="text" class="form-control" id="id_turma" name="id_turma" value="" hidden>
                  </div>
                  <div class="form-group row">
                    <div class="col-md-6">
                      <label for="Data_Inicio">Data Início</label>
                      <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="">
                    </div>
                    <div class="col-md-6">
                      <label for="Data_Final">Data Final</label>
                      <input type="date" class="form-control" id="data_final" name="data_final" value="">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-md-6">
                      <label for="Parcelas">Parcelas</label>
                      <input type="date" class="form-control" id="parcelas" name="parcelas" value="">
                    </div>
                    <div class="col-md-6">
                      <label for="Dia_vencimento">Dia Vencimento</label>
                      <input type="date" class="form-control" id="dia_vencimento" name="dia_vencimento" value="">
                    </div>
                    <div class="col-md-6">
                      <label for="Valor_Total">Valor Total</label>
                      <input type="date" class="form-control" id="valor_total" name="valor_total" value="">
                    </div>
                  </div>
                  
                </div>
                  <div class="card-footer">
                    <button type="submit" class="btn btn-primary" id="submit">Salvar</button>
                  </div>
                
                <?= form_close() ?>
            </div>
            <!-- /.card -->
            </div>
          <!--/.col (left) -->
          <!-- right column -->
          <div class="col-md-6">

          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>

