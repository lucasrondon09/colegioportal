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
                <a href="<?= base_url('/Admin/Usuarios')?>" class="text-decoration-none text-dark">
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
                      <label for="Data_Inicio">Data Início</label>
                      <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="">
                    </div>
                    <div class="col-md-6">
                      <label for="Data_Final">Data Final</label>
                      <input type="date" class="form-control" id="data_final" name="data_fim" value="">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-md-6">
                      <label for="Parcelas">Parcelas</label>
                      <input type="number" class="form-control" id="parcelas" name="parcelas" value="">
                    </div>
                    <div class="col-md-6">
                      <label for="Dia_vencimento">Dia Vencimento</label>
                      <input type="number" class="form-control" id="dia_vencimento" name="dia_vencimento" value="10">
                    </div>
                    
                  </div>

                  <div class="form-group row">
                    <div class="col-md-6">
                      <label for="Valor_Total">Valor Total</label>
                      <input type="text" class="form-control" id="valor_total" name="valor_total" value="">
                        <script>
                        function calcularValorTotal() {
                        var parcelas = parseInt($('#parcelas').val());
                        var valorBase = 26101.20;
                        var valorTotal = '';
                        if (parcelas && parcelas > 0) {
                          valorTotal = ((valorBase / 12) * parcelas).toFixed(2);
                          // Formata para moeda brasileira
                          valorTotal = valorTotal.replace('.', ',');
                          valorTotal = valorTotal.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                          $('#valor_total').val(valorTotal);
                          document.getElementById('valor_total').value = valorTotal;
                        } else {
                          $('#valor_total').val('');
                          document.getElementById('valor_total').value = '';
                        }
                        }

                        // Atualiza valor total quando parcelas mudam ou datas mudam
                        $('#parcelas').on('input', calcularValorTotal);
                        $('#data_inicio, #data_final').on('change', function() {
                        setTimeout(calcularValorTotal, 100);
                        });
                        $(document).ready(calcularValorTotal);
                        </script>
                      </div>
                    <div class="col-md-6">
                        <label for="valor_parcela">Valor da Parcela</label>
                        <input type="text" class="form-control" id="valor_parcela" name="valor_parcela" value="">

                        <script>
                        function calcularValorParcela() {
                          var valorTotal = $('#valor_total').val().replace(/\./g, '').replace(',', '.');
                          var parcelas = parseInt($('#parcelas').val());
                          var valorParcela = '';
                          if (valorTotal && parcelas && parcelas > 0) {
                            valorParcela = (parseFloat(valorTotal) / parcelas).toFixed(2);
                            // Formata para moeda brasileira: 2.175,10
                            valorParcela = valorParcela.replace('.', ',');
                            // Adiciona separador de milhar
                            var partes = valorParcela.split(',');
                            partes[0] = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            valorParcela = partes.join(',');
                            $('#valor_parcela').val(valorParcela);
                            // Também atribui ao atributo value do input para recuperar via GET
                            document.getElementById('valor_parcela').value = valorParcela;
                          } else {
                            $('#valor_parcela').val('');
                            document.getElementById('valor_parcela').value = '';
                          }
                        }

                        $('#valor_total, #parcelas').on('input', calcularValorParcela);
                        $(document).ready(calcularValorParcela);

                        // Executa ao alterar datas, pois parcelas pode ser alterado dinamicamente
                        $('#data_inicio, #data_final').on('change', function() {
                          setTimeout(calcularValorParcela, 100);
                        });
                        </script>
                    </div>
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
                          $('#parcelas').val(totalMeses);
                        } else {
                          $('#parcelas').val('');
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

