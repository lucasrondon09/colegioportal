<?php 
$session = \Config\Services::session();
$validate = \Config\Services::validation();
?>
<style>

.image_area {
  position: relative;
}

img {
    display: block;
    max-width: 100%;
}

.preview {
    overflow: hidden;
    width: 160px; 
    height: 160px;
    margin: 10px;
    border: 1px solid red;
}

.modal-lg{
    max-width: 1000px !important;
}

.overlay {
  position: absolute;
  bottom: 10px;
  left: 0;
  right: 0;
  background-color: rgba(255, 255, 255, 0.5);
  overflow: hidden;
  height: 0;
  transition: .5s ease;
  width: 100%;
}

.image_area:hover .overlay {
  height: 100%;
  cursor: pointer;
}

.text {
  color: #333;
  font-size: 20px;
  position: absolute;
  top: 50%;
  left: 50%;
  -webkit-transform: translate(-50%, -50%);
  -ms-transform: translate(-50%, -50%);
  transform: translate(-50%, -50%);
  text-align: center;
}

</style>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Notícias</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/home')?>">Home</a></li>
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/Noticias')?>">Notícias</a></li>
              <li class="breadcrumb-item active">Cadastrar</li>
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
                <a href="<?= base_url('Admin/Noticias')?>" class="text-decoration-none text-dark">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                  </svg>
                </a>               
                Novo Registro
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
              <?= form_open(base_url('Admin/Noticias/cadastrar')) ?>
              <?= csrf_field() ?>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-8">
                      <input type="text" class="form-control" id="idUsuario" name="idUsuario" value="<?= session()->userId; ?>" hidden>
                      <div class="form-group">
                        <input type="text" class="form-control" id="titulo" name="titulo" value="<?= set_value('titulo') ?>" placeholder="Título">
                      </div>
                      <div class="form-group">
                        <input type="text" class="form-control" id="subtitulo" name="subtitulo" value="<?= set_value('subtitulo') ?>" placeholder="Subtítulo">
                      </div>
                      <div class="form-group">
                        <textarea id="summernote" name="texto">
                          
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
                            <label class="mr-3">Publicar notícia:</label>
                            <input type="checkbox" name="status" value="1" checked data-bootstrap-switch>
                          </div>
                          <div class="form-group">
                            <label>Data da publicação:</label>
                            <input type="date" class="form-control" value="<?= date('Y-m-d');?>" name="dataNoticia">
                          </div>
                            
                        </div>
                      </div>
                      <div class="card w-100">
                        <div class="card-header bg-dark">
                          Categoria
                        </div>
                        <div class="card-body">
                          <div class="form-group">
                            <label>Selecione uma categoria:</label>
                            <div class="input-group mb-3">
                              
                              <select class="custom-select" id="idCategoria" name="idCategoria">
                                <option selected>Selecione</option>
                                <?php foreach($categoria as $categoriaItem):?>
                                <option value="<?= $categoriaItem->id?>"><?= $categoriaItem->titulo;?></option>
                                <?php endforeach;?>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="card w-100">
                        <div class="card-header bg-dark">
                          Capa
                        </div>
                        <div class="card-body">
                          <div class="form-group">
                            <label>Selecione uma imagem:</label>
                            <div class="image_area">
                                <label for="upload_image">
                                  <img src="<?= base_url('assets/dist/img/imagem-1.jpg')?>" id="uploaded_image" class="img-fluid w-100" />
                                  <div class="overlay">
                                      <div class="text">Selecione uma imagem</div>
                                  </div>
                                    <input type="file" class="image" id="upload_image" hidden>
                                    <input type="text" name="capa" id="capa" hidden>
                                  </label>
                              </div>
                          </div>
                        </div>
                      </div>
                        <!--Modal Crop-->
                        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="modalLabel">Upload de imagem</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">×</span>
                                  </button>
                                </div>
                                <div class="modal-body">
                                  <div class="img-container">
                                      <div class="row">
                                          <div class="col-md-8">
                                              <img src="" id="sample_image" />
                                          </div>
                                          <div class="col-md-4">
                                              <div class="preview"></div>
                                          </div>
                                      </div>
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                  <button type="button" class="btn btn-primary" id="crop">Salvar</button>
                                </div>
                            </div>
                          </div>
                        </div>
                        <!--Modal Crop-->
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

