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
            <h1>Turmas do Professor</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/home')?>">Home</a></li>
              <li class="breadcrumb-item active">Turmas</li>
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
              <div class="card-header d-flex align-items-center">
                <h3 class="card-title">Registros</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">         
                <table id="registros" class="table table-bordered table-hover mb-3">
                  <thead>
                  <tr>
                    <th>Turma</th>
                    <th>Disciplina</th>
                    <th>Ano</th>
                    <th>Lançar Notas</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach($table as $tableItem):?>
                    <tr>
                      <td><?= $tableItem->turmaNome?></td>
                      <td><?= $tableItem->disciplinaNome?></td>
                      <td><?= $tableItem->turmaAno?></td>
                      <td class="text-center" width="30%">
                      <a href="<?= base_url('Admin/Turma/lancar-notas/'.$tableItem->turmaId.'/'.$tableItem->disciplinaId.'/1')?>" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="top" title="1º Trimestre">
                        1º Trim
                      </a>  
                      <a href="<?= base_url('Admin/Turma/lancar-notas/'.$tableItem->turmaId.'/'.$tableItem->disciplinaId.'/2')?>" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="top" title="2º Trimestre">
                        2º Trim
                      </a>  
                      <a href="<?= base_url('Admin/Turma/lancar-notas/'.$tableItem->turmaId.'/'.$tableItem->disciplinaId.'/3')?>" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="top" title="3º Trimestre">
                        3º Trim
                      </a>  
                      <a href="<?= base_url('Admin/Turma/lancar-notas/'.$tableItem->turmaId.'/'.$tableItem->disciplinaId.'/r')?>" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="top" title="Notas de Recuperação">
                        Rec
                      </a>  
                        
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