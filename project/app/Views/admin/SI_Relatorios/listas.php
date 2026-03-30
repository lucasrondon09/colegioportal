<?php

use App\Controllers\Admin\parametro;
use App\Models\Admin\SI_ParametroModel;

  $session = \Config\Services::session();
  
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Relatório - Listas</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/home')?>">Home</a></li>
              <li class="breadcrumb-item active">Relatório - Listas</li>
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
                <h3 class="card-title">Relatório</h3>
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
                <div class="row">
                  <div class="col-md-12">
                    <?= form_open(base_url('Admin/Relatorios/listas/send')) ?>
                    <div class="form-group">
                      <label for="ano">Ano Letivo Corrente</label>
                      <?php $anosLetivos = (new parametro)->anos_letivos();?>
                      <select class="custom-select" id="ano" name="ano">
                          <option value="-1" selected="">Selecione</option>
                          <?php foreach ($anosLetivos as $anosLetivosItem):?>
                            <option value="<?= $anosLetivosItem;?>"><?= $anosLetivosItem;?></option>
                          <?php endforeach;?>
                      </select>
                      <!--<input type="text" readonly  class="form-control" name="ano" value="<?= $ano?>">-->
                      <div id="resultado"></div>
                    </div>
                    <div class="form-group">
                      <label>Turmas:</label>
                      <div class="input-group mb-3">
                        <select class="custom-select" id="turma" name="turma">
                          <option value="-1" selected="">Todas</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label>Relatórios:</label>
                      <div class="input-group mb-3">
                        <select class="custom-select" id="relatorios" name="relatorios">
                          <option disabled selected="">Selecione</option>
                          <option value="telefone" id="rel_telefone">Nascimento/ Telefone</option>
                          <option value="carometro" id="rel_carometro">Carômetro</option>
                          <option value="chamada" id="rel_chamada">Lista de Chamada</option>
                          <option value="tarefa" id="rel_tarefa">Registro de Tarefas</option>
                          <option value="classe" id="rel_classe">Diário de Classe</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group" id="etapa" style="display: none;">
                      <label>Etapa:</label>
                      <small class="form-text text-muted">Selecione uma turma (acima) ou uma etapa (abaixo)</small>
                      <div class="input-group mb-3">
                        <select class="custom-select" name="etapa">
                          <option value="-1" selected="">Selecione</option>
                          <option value="1">Educação Infantil</option>
                          <option value="2">Ensino Fundamental 1</option>
                          <option value="3" >Ensino Fundamental 2</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group" id="disciplina" style="display: none;">
                      <label>Disciplina:</label>
                      <small class="form-text text-muted">Selecione uma turma (acima) ou uma disciplina (abaixo)</small>
                      <div class="input-group mb-3">
                        <select class="custom-select" name="disciplina">
                          <option selected="" value="-1">Todas</option>
                          <?php $disciplina = parametro::disciplinas()?>
                          <?php foreach($disciplina as $key => $disciplinaItem):?>
                          <option value="<?= $key?>"><?= $disciplinaItem?></option>
                          <?php endforeach;?>
                          
                        </select>
                      </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                      Gerar Relatório
                    </button>
                    <?= form_close() ?>
                  </div>
                </div>   
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
    $(document).ready(function() {
        $('#relatorios').change(function() {
            if ($(this).val() === 'carometro') {
                $('#etapa').show();
            } else {
                $('#etapa').hide();
            }

            if ($(this).val() === 'classe') {
                $('#disciplina').show();
            } else {
                $('#disciplina').hide();
            }
        });

        $('#ano').on('change', function() {
          var ano = $(this).val();
        
          if(ano) { // Verifica se uma opção foi selecionada

              $.ajax({
                  url: '<?= base_url('/Admin/Relatorios/buscaTurmas')?>',
                  type: 'POST',
                  data: { ano: ano },
                  success: function(response) {
                      $('#turma').html(response);
                  },
                  error: function() {
                      $('#turma').html('Erro ao processar a requisição.');
                  }
              });
          }
        }); 
        
    });
  </script>