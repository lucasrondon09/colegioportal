<?php

use App\Models\Admin\SI_PaiModel;

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
            <h1>Alunos</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/home')?>">Home</a></li>
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/Alunos')?>">Alunos</a></li>
              <li class="breadcrumb-item active">Editar</li>
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
                <a href="<?= base_url('/Admin/Alunos')?>" class="text-decoration-none text-dark">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                  </svg>
                </a>               
                Editar Registro
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
              <form>
                <fieldset disabled>
                  <div class="card-body">
                    <div class="form-group row">
                      <label class="col-form-label col-sm-1">Matricula</label>
                      <input type="text" class="form-control-plaintext col-sm-1" name="matricula" value="<?= $field->matricula;?>">
                      <label class="col-form-label col-sm-1">Data Matricula</label>
                      <input type="text" class="form-control-plaintext col-sm-2" name="data_matricula" value="<?= date('d/m/Y H:i:s', strtotime($field->data_matricula)) ;?>">
                    </div>
                    <div class="form-group row">
                      <label class="col-form-label col-sm-1">Senha</label>
                      <input type="text" class="form-control col-sm-1" name="senha" value="<?= $field->senha;?>">
                    </div>
                    <div class="form-group row">
                      <?php
                        $paiModel = new SI_PaiModel();
                        $pais = $paiModel->getPai($field->fk_pai);
                      ?>
                      <label class="col-form-label col-sm-1">Pai</label>
                      <input type="text" class="form-control-plaintext col-sm-5" name="senha" value="<?= $pais->nome_pai;?>">
                      <label class="col-form-label col-sm-1">Mãe</label>
                      <input type="text" class="form-control-plaintext col-sm-5" name="fk_pai" value="<?= $pais->nome_mae;?>">
                    </div>
                    <hr>
                    <div class="form-group">
                      <label>Nome</label>
                      <input type="text" class="form-control" name="nome" value="<?= $field->nome;?>">
                    </div>
                    <div class="form-group">
                      <label>RG</label>
                      <input type="text" class="form-control" name="rg" value="<?= $field->rg;?>">
                    </div>
                    <div class="form-group">
                      <label>Data de Nascimento</label>
                      <input type="text" class="form-control" name="nasc" value="<?= $field->nasc;?>">
                    </div>
                    <div class="form-group">
                      <label>Cidade Nascimento</label>
                      <input type="text" class="form-control" name="cid_nasc" value="<?= $field->cid_nasc;?>">
                    </div>
                    <div class="form-group">
                      <label>UF Nascimento</label>
                      <input type="text" class="form-control" name="uf_nasc" value="<?= $field->uf_nasc;?>">
                    </div>
                    <div class="form-group">
                      <label>Endereço</label>
                      <input type="text" class="form-control" name="end" value="<?= $field->end;?>">
                    </div>
                    <div class="form-group">
                      <label>UF</label>
                      <input type="text" class="form-control" name="uf" value="<?= $field->uf;?>">
                    </div>
                    <div class="form-group">
                      <label>Bairro</label>
                      <input type="text" class="form-control" name="bairro" value="<?= $field->bairro;?>">
                    </div>
                    <div class="form-group">
                      <label>Cidade</label>
                      <input type="text" class="form-control" name="cidade" value="<?= $field->cidade;?>">
                    </div>
                    <div class="form-group">
                      <label>Fone</label>
                      <input type="text" class="form-control" name="fone" value="<?= $field->fone;?>">
                    </div>
                    <div class="form-group">
                      <label>Email</label>
                      <input type="text" class="form-control" name="email" value="<?= $field->email;?>">
                    </div>
                    <div class="form-group">
                      <label>Escola Proveniente</label>
                      <input type="text" class="form-control" name="prov_aluno" value="<?= $field->prov_aluno;?>">
                    </div>
                  </div>
                </fieldset>
              </form>
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

