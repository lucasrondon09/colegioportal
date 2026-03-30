<?php 
  $session = \Config\Services::session();
  
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><?= $pagina->descricao?></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/home')?>">Home</a></li>
              <li class="breadcrumb-item active"><?= $pagina->descricao?></li>
            </ol>
            
          </div>
        </div>
        <?php if(session()->sistema == 'sisaula'):?>
        <div class="row">
          <div class="col-12">
            <a href="<?= base_url('Admin/Paginas-Internas/cadastrar').'/'.$pagina->id?>" class="btn btn-primary">
              <i class="fas fa-plus fa-fw"></i>
              Cadastrar
            </a>
          </div>
        </div>
        <?php endif;?>
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
              </div>
              <!-- /.card-header -->
              <div class="card-body">
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
                <?php if(isset($arquivos)):?>
                <table id="registros" class="table table-bordered table-hover mb-3">
                  <thead>
                  <tr>
                    <th>Descrição</th>
                    <th>Arquivo</th>
                    <th>Data</th>
                    <?php if(session()->sistema == 'sisaula'):?>
                    <th></th>
                    <?php endif;?>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach($arquivos as $arquivosItem):?>
                    <tr>
                      <td><?= $arquivosItem->descricao?></td>
                      <td><a href="<?= base_url('/uploads/arquivo_download/'. $arquivosItem->nome_arquivo) ?>" target="_blank">Download</a></td>
                      <td><?= date('d/m/Y', strtotime($arquivosItem->data));?></td>
                      <?php if(session()->sistema == 'sisaula'):?>
                      <td class="text-center" width="10%">
                      
                      <a href="<?= base_url('Admin/Paginas-Internas/excluir/'.$pagina->id.'/'.$arquivosItem->id);?>" class="btn btn-sm btn-outline-danger" data-toggle="tooltip" data-placement="top" title="Excluir" onClick="return deletar()">
                        <i class="fas fa-trash fa-fw"></i>
                      </a>
                        
                      </td>
                      <?php endif;?>
                    </tr>
                  <?php endforeach;?>  
                  </tbody>
                </table> 
                <?php else:?> 
                  <div class="alert alert-danger" role="alert">
                    Não há dados cadastrados
                  </div>
                <?php endif;?> 
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