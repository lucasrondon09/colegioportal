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
              <?php if(session()->sistema == 'sisaula'):?>
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/Relatorios/boletim')?>">Notas (Boletim)</a></li>
              <?php endif;?>
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
                  <div class="row">
                    <div class="col-12">
                      <?php  if(session()->sistema == 'sispai'):?>
                        <a href="/Admin/boletim" class="btn btn-sm btn-light">Voltar</a>
                      <?php else:?>  
                        <?= form_open(base_url('Admin/Relatorios/boletim-turma/'), ['method' => 'GET']) ?>
                        <input type="text" class="form-control" name="ano" hidden value="<?= $turma->ano?>">
                        <input type="text" class="form-control" name="periodo" hidden value="<?= $periodo?>">
                        <input type="text" name="turma" hidden value="<?= $turma->id?>">
                        <button type="submit" class="btn btn-sm btn-light">Voltar</button>
                      <?= form_close() ?> 
                      <?php endif;?>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="col-3">
                      <div class="form-group">
                        <label>Turma</label>
                        <input type="text" name="turma" hidden value="<?= $turma->id?>">
                        <input type="text" class="form-control" readonly name="turmaNome" value="<?= $turma->nome?>">
                      </div>
                    </div>
                    <div class="col-1">
                      <div class="form-group">
                        <label>Ano</label>
                        <input type="text" class="form-control" name="ano" readonly value="<?= $turma->ano?>">
                      </div>
                    </div>
                    <div class="col-2">
                      <div class="form-group">
                        <label>Período</label>
                        <input type="text" class="form-control" name="periodo" hidden value="<?= $periodo?>">
                        <input type="text" class="form-control" name="periodoNome" readonly value="<?= $periodoNome?>">
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="form-group">
                        <label>Aluno</label>
                        <input type="text" class="form-control" name="aluno" hidden value="<?= $aluno->id?>">
                        <input type="text" class="form-control" name="alunoNome" readonly value="<?= $aluno->nome?>">
                      </div>
                    </div>
                  </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">   
                <table id="registros" class="table table-bordered table-hover mb-3">
                  <thead>
                  <tr>
                    <?php foreach($tabela['colunas'][0] as $colunas):?>
                
                      <th><?= $colunas; ?></th>

                      <?php endforeach;?>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach($tabela['linhas'] as $linhas):?>
                    <tr>
                      <?php foreach($linhas as $linhasItem):?>
                        <td><?= $linhasItem;?></td>
                      <?php endforeach;?>  
                    </tr>
                  <?php endforeach;?>  
                  </tbody>
                </table> 
                <p>N.A. = Não se aplica <br>? = Sem dados suficientes para o cálculo</p>
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