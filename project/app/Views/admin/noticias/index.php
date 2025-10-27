
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
              <li class="breadcrumb-item active">Notícias</li>
            </ol>
            
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <a href="<?= base_url('Admin/Noticias/cadastrar')?>" class="btn btn-primary">
              <i class="fas fa-plus fa-fw"></i>
              Cadastrar
            </a>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header d-flex align-items-center">
                <h3 class="card-title">Registros</h3>
                <div class="ml-auto">
                <?= form_open(base_url('Admin/Noticias')) ?>
                <?= csrf_field() ?>
                  <div class="form-inline">
                    <div class="input-group">
                      <input class="form-control form-control-sidebar" type="search" placeholder="Procurar" name="search">
                      <div class="input-group-append">
                        <button class="btn btn-sidebar bg-secondary">
                          <i class="fas fa-search fa-fw"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                <?= form_close() ?> 
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
              <?php

                if(!empty(session()->getFlashdata())){
                  $alert = session()->getFlashdata();
                  
                  if(key($alert) == 'success'){
                    
                    $classAlert = 'success';
                    $message    = session()->getFlashdata('success');
                  }else{

                    $classAlert = 'danger';
                    $message    = session()->getFlashdata('error');
                  }
                }

                if(isset($alert)):

                ?>    
                  <div class="mt-4">
                    <div>
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
                <table id="registros" class="table table-bordered table-hover mb-3">
                  <thead>
                  <tr>
                    <th>capa</th>
                    <th>Título</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th>Visualizações</th>
                    <th></th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach($table as $tableItem):?>
                    <tr>
                      <td class="col-md-1 align-middle"><img src="<?= $tableItem->capa;?>" alt="capa" class="img-fluid w-100"></td>
                      <td class="col-md-4 align-middle"><?= $tableItem->titulo?></td>
                      <td class="col-md-2 align-middle"><?= $tableItem->dataNoticia?></td>
                      <td class="col-md-2 align-middle"><?= $tableItem->status == '1' ?'Publicado': 'Não publicado' ?></td>
                      <td class="col-md-1 align-middle"><?= $tableItem->visualizacoes?></td>
                      <td class="col-md-2 align-middle text-center" width="10%">
                      <a href="<?= base_url('Admin/Noticias/galeria').'/'.$tableItem->id;?>" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="top" title="Galeria">
                        <i class="fas fa-images"></i>
                      </a>

                      <a href="<?= base_url('Admin/Noticias/editar').'/'.$tableItem->id;?>" class="btn btn-sm btn-outline-info" data-toggle="tooltip" data-placement="top" title="Editar">
                        <i class="fas fa-pen fa-fw"></i>
                      </a>
                      
                      <a href="<?= base_url('Admin/Noticias/excluir').'/'.$tableItem->id;?>" class="btn btn-sm btn-outline-danger" data-toggle="tooltip" data-placement="top" title="Excluir" onClick="return deletar()">
                        <i class="fas fa-trash fa-fw"></i>
                      </a>
                        
                      </td>
                    </tr>
                  <?php endforeach;?>  
                  </tbody>
                </table>
                <?= $pager->links('default', 'default_page') ?>    
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<script>
  function deletar(){

    return confirm('Tem certeza que deseja excluir o registro?');

  }
</script>