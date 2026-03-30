<?php 
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
            <h1>Banners</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/home')?>">Home</a></li>
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/Banners')?>">Banners</a></li>
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
                <a href="<?= base_url('Admin/Banners')?>" class="text-decoration-none text-dark">
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
              <?= form_open_multipart(base_url('Admin/Banners/editar'.'/'.$field->id)) ?>
              <?= csrf_field() ?>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-8">
                      <input type="text" class="form-control" id="idUsuario" name="idUsuario" value="<?= session()->userId; ?>" hidden>
                      <div class="form-group">
                        <input type="text" class="form-control" id="titulo" name="titulo" value="<?= $field->titulo; ?>" placeholder="Título">
                      </div>
                      <div class="form-group">
                        <input type="text" class="form-control" id="link" name="link" value="<?= $field->link; ?>" placeholder="Link">
                      </div>
                      <div class="form-group">
                        <textarea id="summernote" name="texto">
                          <?= $field->texto; ?>
                        </textarea>
                      </div>
                      
                    </div>
                    <div class="col-md-4">
                      <div class="card w-100">
                        <div class="card-header bg-dark">
                          Publicação
                        </div>
                        <div class="card-body">
                          <div class="form-group">
                            <label class="mr-3">Publicar:</label>
                            <input type="hidden" name="status" value='0'>
                            <input type="checkbox" name="status" <?= $field->status == '1' ? 'checked': ''; ?> data-bootstrap-switch value="1">
                          </div>
                        </div>
                      </div>
                      <div class="card w-100">
                        <div class="card-header bg-dark">
                          Posição
                        </div>
                        <div class="card-body">
                          <div class="form-group">
                            <label class="mr-3">posição do banner:</label>
                            <input type="text" class="form-control" id="posicao" name="posicao" value="<?= $field->posicao; ?>">
                          </div>
                        </div>
                      </div>
                      <div class="card w-100">
                        <div class="card-header bg-dark">
                          Capa
                        </div>
                        <div class="card-body">
                          <div class="form-group">
                            <label>Banner grande:</label>
                              <img src="<?= $field->imagem;?>" class="img-fluid w-100 mb-3" />
                              <input type="file"  id="imagem" name="imagem">
                          </div>
                          <div class="form-group mt-5">
                            <label>Banner para dispositivos móveis:</label>
                              <img src="<?= $field->imagem_responsiva;?>" class="img-fluid w-100 mb-3" />
                              <input type="file"  id="imagem_responsiva" name="imagem_responsiva">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary" id="submit">Salvar</button>
                </div>
                <?= form_close() ?>
            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>

