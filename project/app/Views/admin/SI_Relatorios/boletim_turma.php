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
            <h1>Notas (Boletim)</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/home')?>">Home</a></li>
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/Relatorios/boletim')?>">Notas (Boletim)</a></li>
              <li class="breadcrumb-item active">Turma</li>
            </ol>
            
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
              <div class="card-header">
                
                <div class="">
                <?= form_open(base_url('Admin/Relatorios/boletim-turma/'), ['method' => 'GET']) ?>
                  
                  <div class="row">
                    <div class="col-12">
                      <h3 class="card-title">Registros</h3>
                    </div>
                  </div>
                    <div class="form-row">
                      <div class="col-4">
                        <div class="form-group">
                          <label>Turma</label>
                          <input type="text" name="turma" hidden value="<?= $turma->id?>">
                          <input type="text" class="form-control" readonly name="turmaNome" value="<?= $turma->nome?>">
                        </div>
                      </div>
                      <div class="col-2">
                        <div class="form-group">
                          <label>Ano</label>
                          <input type="text" class="form-control" name="ano" readonly value="<?= $ano?>">
                        </div>
                      </div>
                      <div class="col-4">
                        <div class="form-group">
                          <label>Período</label>
                          <input type="text" class="form-control" name="periodo" hidden value="<?= $periodo?>">
                          <input type="text" class="form-control" name="periodoNome" readonly value="<?= $periodoNome?>">
                        </div>
                      </div>
                      <div class="col-2">
                      <div class="form-group">
                        <label for="">Boletim da Turma</label>
                        <a href="<?= base_url('Admin/Relatorios/boletim-turma/gerar-boletim-turma').'/'.$turma->id?>" class="btn btn-danger w-100"><i class="fas fa-file-pdf mr-2"></i>Gerar Boletim</a>
                        </div>
                      </div>
                    </div>
                    <hr>
                    <div class="">
                    <div class="row">
                      <div class="col-12">
                        <div class="input-group">
                          <input class="form-control form-control-sidebar" type="search" placeholder="Procurar" name="search">
                          <div class="input-group-append">
                            <button class="btn btn-sidebar bg-secondary">
                              <i class="fas fa-search fa-fw"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                <?= form_close() ?> 
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">   
                <table id="registros" class="table table-bordered table-hover mb-3">
                  <thead>
                  <tr>
                    <th>Matricula</th>
                    <th>Nome</th>
                    <th></th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach($alunos as $alunosItem):?>
                    <tr>
                      <td><?= $alunosItem->matricula?></td>
                      <td><?= $alunosItem->nome?></td>
                      <td class="text-center" >
                      <?php if($periodo == 'a'):?> 
                      <a href="<?= base_url('Admin/Relatorios/boletim-turma/gerar-boletim-turma').'/'.$turma->id.'/'.$alunosItem->id?>" class="btn btn-sm btn-outline-danger" data-toggle="tooltip" data-placement="top" title="Gerar Boletim Individual">
                      <i class="fas fa-file-pdf mr-2"></i>Gerar Boletim
                      </a>
                      <a href="<?= base_url('Admin/Relatorios/boletim-turma/gerar-boletim-turma-ficha').'/'.$turma->id.'/'.$alunosItem->id?>" class="btn btn-sm btn-outline-danger" data-toggle="tooltip" data-placement="top" title="Gerar Ficha Individual">
                      <i class="fas fa-file-pdf mr-2"></i>Gerar Ficha
                      </a>
                      <?php else:?>
                      <a href="<?= base_url('Admin/Relatorios/boletim-turma/notas-trimestrais').'/'.$turma->id.'/'.$alunosItem->id.'/'.$periodo?>" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="top" title="Notas Trimestrais">
                      Notas Trimestrais
                      </a>
                      <?php endif;?>
                      </td>
                    </tr>
                  <?php endforeach;?>  
                  </tbody>
                </table>    
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