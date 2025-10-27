<?php

use App\Models\Admin\SI_DisciplinaModel;
use App\Models\Admin\SI_ParametroModel;
use App\Models\Admin\SI_ProfessorTurmaModel;
use App\Models\Admin\SI_TurmaModel;

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
            <h1>Contrato</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/home')?>">Home</a></li>
              <li class="breadcrumb-item">Turmas</li>
              <li class="breadcrumb-item ">Alunos</li>
              <li class="breadcrumb-item active">Contrato</li>
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
                <a href="javascript:history.back()" class="text-decoration-none text-dark">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                  </svg>
                </a>
                Dados do Registro
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
              <?= form_open($action) ?>
                  <fieldset>
              <?= csrf_field() ?>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-9">
                      <h3><?= $aluno->nome?></h3>
                      <h6 class="text-muted"><?= $turma->nome?> <?= getPeriodo($turma->id_periodo)?></h6>
                      <h6 class="text-muted">Responsável Financeiro: <?= $responsavel->rm_resp_financeiro_nome?></h6>
                      <h6 class="text-muted">Situação: <?= !empty($fields->status) ? statusContrato($fields->status) : '--';?></h6>
                      
                    </div>
                    <div class="col-md-3">
                      <div class="card border-info mb-3">     
                        <div class="card-body">
                          <p class="text-muted">PENDÊNCIA FINANCEIRA</p>
                          <h3 class="card-text mb-1">R$ 26.101,20</h3>
                         </div>
                      </div>
                    </div>
                  </div>
                    
                    
                  <input type="text" class="form-control" id="id_aluno" name="id_aluno" value="<?= $aluno->id?>" hidden>
                  <input type="text" class="form-control" id="id_turma" name="id_turma" value="<?= $turma->id?>" hidden>
                  <input type="text" class="form-control" id="id_responavel" name="id_responsavel" value="<?= $responsavel->id?>" hidden>
                  
                  <div class="form-group row">
                    <div class="col-md-6">
                      <label for="tipo_contrato">Tipo do Contrato</label>
                      <select class="form-control" id="tipo_contrato" name="tipo_contrato">
                        <?php foreach(tipoContrato() as $key => $value): ?>
                          <option value="<?= $key ?>"><?= $value ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label for="status">Status do Contrato</label>
                      <select class="form-control" id="status" name="status">
                        <?php foreach (statusContrato() as $key => $value): ?>
                          <option value="<?= $key ?>" <?= (!empty($fields->status) && $fields->status == $key) ? 'selected' : '' ?>>
                            <?= $value ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-md-6">
                      <label for="Data_Inicio">Data Início</label>
                      <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?= !empty($fields->data_inicio) ? $fields->data_inicio : ''?>">
                    </div>
                    <div class="col-md-6">
                      <label for="Data_Final">Data Final</label>
                      <input type="date" class="form-control" id="data_final" name="data_fim" value="<?= !empty($fields->data_fim) ? $fields->data_fim : ''?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-md-4">
                      <label for="Parcelas">Parcelas</label>
                      <input type="number" class="form-control" id="parcelas" name="parcelas" value="<?= !empty($fields->parcelas) ? $fields->parcelas : '';?>">
                    </div>
                    <div class="col-md-4">
                      <label for="Dia_vencimento">Dia Vencimento</label>
                      <input type="number" class="form-control" id="dia_vencimento" name="dia_vencimento" value="<?= !empty($fields->dia_vencimento) ? $fields->dia_vencimento : '10';?>">
                    </div>
                    <div class="col-md-4">
                      <label for="Valor_Total">Valor Total</label>
                      <input type="text" class="form-control" id="valor_total" name="valor_total" value="<?= !empty($fields->valor_total) ? $fields->valor_total : '';?>">
                        <script>
                        $(document).ready(function() {
                          // Salva o valor digitado manualmente
                          let valorManual = '';

                          // Quando o usuário digitar manualmente, salva o valor
                          $('#valor_total').on('input', function() {
                            valorManual = $(this).val();
                          });

                          function calcularValorTotal() {
                            var parcelas = parseInt($('#parcelas').val());
                            var valorBase = 26101.20;

                            // Se o usuário digitou manualmente, não sobrescreve
                            if (valorManual !== '') {
                              $('#valor_total').val(valorManual);
                              return;
                            }

                            if (parcelas && parcelas > 0) {
                              let valorTotal = ((valorBase / 12) * parcelas).toFixed(2);
                              // Formata para moeda brasileira
                              valorTotal = valorTotal.replace('.', ',');
                              valorTotal = valorTotal.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                              $('#valor_total').val(valorTotal);
                            } else {
                              $('#valor_total').val('');
                            }
                          }

                          $('#parcelas').on('input change', function() {
                            valorManual = ''; // Limpa o valor manual ao alterar parcelas
                            calcularValorTotal();
                          });
                        });
                        </script>

                      </div>
                    
                  
                </div>
                  <div class="card-footer">
                    <button type="submit" class="btn btn-primary" id="submit">Salvar</button>
                  </div>

                  <script>
                  $(function() {
                    function calcularParcelas() {
                      var inicio = $('#data_inicio').val();
                      var fim = $('#data_final').val();

                      if (inicio && fim) {
                        var dataInicio = new Date(inicio);
                        var dataFinal = new Date(fim);

                        var anos = dataFinal.getFullYear() - dataInicio.getFullYear();
                        var meses = dataFinal.getMonth() - dataInicio.getMonth();
                        var totalMeses = anos * 12 + meses + (dataFinal.getDate() >= dataInicio.getDate() ? 1 : 0);

                        if (totalMeses > 0) {
                          $('#parcelas').val(totalMeses).trigger('input');
                        } else {
                          $('#parcelas').val('').trigger('input');
                        }
                      }
                    }

                    $('#data_inicio, #data_final').on('change', calcularParcelas);
                  });
                  </script>
                
                <?= form_close() ?>
            </div>
            <!-- /.card -->
            </div>
          <!--/.col (left) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>

