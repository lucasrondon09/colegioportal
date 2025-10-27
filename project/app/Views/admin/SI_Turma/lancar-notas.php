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
            <h1>Lançar Notas</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/home')?>">Home</a></li>
              <li class="breadcrumb-item active">Lançar Notas</li>
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
                      <a href="<?= base_url('Admin/Turma/permissao-professor')?>" class="btn btn-sm btn-secondary mb-3">Voltar</a>
                    </div>
                  </div>
                  <div class="row">
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
                    <div class="col-4">
                      <div class="form-group">
                        <label>Período</label>
                        <input type="text" class="form-control" name="periodo" hidden value="<?= $periodo['id']?>">
                        <input type="text" class="form-control" name="periodoNome" readonly value="<?= $periodo['nome']?>">
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="form-group">
                        <label>Disciplina</label>
                        <input type="text" class="form-control" name="periodo" hidden value="<?= $disciplina->disciplina_id?>">
                        <input type="text" class="form-control" name="periodoNome" readonly value="<?= $disciplina->nome?>">
                      </div>
                    </div>
                  </div>
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
                ?>
                <?php if(isset($alert)):?>    
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

                <?= form_open(base_url('Admin/Turma/lancar-notas/save')) ?>
                <?= csrf_field() ?>
                <input type="text" name="turma" hidden value="<?= $turma->id?>">
                <input type="text" name="ano" hidden value="<?= $turma->ano?>">
                <input type="text" name="periodo" hidden value="<?= $periodo['id']?>">
                <input type="text" name="disciplina" hidden value="<?= $disciplina->disciplina_id?>">
                <input type="text" name="disciplinaId" hidden value="<?= $disciplina->id?>">
<div class="table-responsive">
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
                </div>
                <br /><h4>Instruções para lançamento de notas</h4>
                <p>Para valores decimais use vírgula , exemplo:&nbsp;&nbsp; <b style="font-size:15px;">8,5</b> &nbsp;&nbsp; (o limite é 1 número após a vírgula)<br>
                Para valores inteiros, use somente o valor inteiro: &nbsp;&nbsp;<b style="font-size:15px;">8</b> &nbsp;&nbsp; ou o valor inteiro com vírgula: &nbsp;&nbsp; <b style="font-size:15px;">8,0</b><br /><br /></p>
                <?php if($periodo['id'] == 'r'):?>
                  <b>Observação:</b><br>As notas de recuperação somente serão consideradas caso a média parcial final (MPA) seja abaixo da média do colégio (6,0).
                  <br /><br />
                <?php else:?>  
                <h4>Legenda</h4>
							 <p>
                S1 = Prova 1<br />
                S2 = Prova 2<br />
                S3 = Prova 3<br />
                S4 = Prova 4<br />
                SIM = Simulado<br />
                FOR = Formativa<br />
                <br /><br /></p>
                <?php endif?>
              </div>
              <!-- /.card-body -->
              
              <div class="card-footer">
                <button type="submit" class="btn btn-primary" id="submit">
                  Salvar
                </button>
              </div>
              <?= form_close() ?>
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