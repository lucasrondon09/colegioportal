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
            <h1>Lançamentos</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/home')?>">Home</a></li>
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/Contrato')?>">Contrato</a></li>
              <li class="breadcrumb-item active">Lançamentos</li>
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
              <?= form_open() ?>
                  <fieldset>
              <?= csrf_field() ?>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="card">
                        <div class="card-header">
                          <h3 class="card-title">
                            <a href="javascript:history.back()" class="text-decoration-none text-dark">
                              <i class="fas fa-chevron-left"></i>
                            </a>
                            Informações do Contrato
                          </h3>
                        </div>
                        <div class="card-body">
                          <div class="row">
                            <div class="col-md-6">
                              <p class="mb-2"><strong>Aluno:</strong> <?= $contrato->aluno_nome ?></p>
                              <p class="mb-2"><strong>Responsável Financeiro:</strong> <?= $contrato->responsavel_nome ?></p>
                              <p class="mb-2"><strong>Tipo:</strong> <?= !empty($contrato->tipo_contrato) ? tipoContrato($contrato->tipo_contrato) : '--';?></p>
                            </div>
                            <div class="col-md-6">
                              <p class="mb-2"><strong>Contrato:</strong> #<?= $contrato->id ?></p>
                              <p class="mb-2"><strong>Status:</strong> <?= statusContrato($contrato->status) ?></p>
                              <p class="mb-2"><strong>Data:</strong> <?= !empty($contrato->data_inicio) ? dataBR($contrato->data_inicio) : '--';?> <?= !empty($contrato->data_fim) ? 'à '.dataBR($contrato->data_fim) : '--';?></p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Cards de Resumo Financeiro -->
                  <div class="row mt-3">
                    <div class="col-md-3">
                      <div class="card border-primary mb-3">     
                        <div class="card-body">
                          <p class="text-muted mb-1">
                            <i class="fas fa-file-contract text-primary"></i> VALOR DO CONTRATO
                          </p>
                          <h3 class="card-text mb-2"><strong>R$ <?= monetarioExibir($contrato->valor_total)?></strong></h3>
                          <p class="text-muted mb-0">
                            <small><?= !empty($contrato->parcelas) ? $contrato->parcelas .' parcelas cadastradas' : 'Nenhuma parcela lançada' ?></small>
                          </p>
                         </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="card border-info mb-3">     
                        <div class="card-body">
                          <p class="text-muted mb-1">
                            <i class="fas fa-clock text-info"></i> VALOR A RECEBER
                          </p>
                          <h3 class="card-text mb-2"><strong>R$ <?= monetarioExibir($valorTotalAReceber ?? 0)?></strong></h3>
                          <p class="text-muted mb-0">
                            <small><?= !empty($totalParcelas) ? $totalParcelas.' parcelas lançadas' : 'Nenhuma parcela lançada'?></small>
                          </p>
                         </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="card border-success mb-3">     
                        <div class="card-body">
                          <p class="text-muted mb-1">
                            <i class="fas fa-check-circle text-success"></i> VALOR RECEBIDO
                          </p>
                          <h3 class="card-text mb-2 text-success"><strong>R$ <?= monetarioExibir($valorTotalRecebido ?? 0)?></strong></h3>
                          <p class="text-muted mb-0">
                            <small><?= isset($totalParcelasPagas) ? $totalParcelasPagas.' parcela(s) paga(s)' : 'Nenhuma cobrança recebida'?></small>
                          </p>
                         </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="card border-danger mb-3">     
                        <div class="card-body">
                          <p class="text-muted mb-1">
                            <i class="fas fa-exclamation-triangle text-danger"></i> VALOR VENCIDO
                          </p>
                          <h3 class="card-text mb-2 text-danger"><strong>R$ <?= monetarioExibir($valorTotalVencido ?? 0)?></strong></h3>
                          <p class="text-muted mb-0">
                            <small><?= isset($totalParcelasAtrasadas) ? $totalParcelasAtrasadas.' parcela(s) atrasada(s)' : 'Nenhuma cobrança vencida'?></small>
                          </p>
                         </div>
                      </div>
                    </div>
                  </div>

                  <!-- Barra de Progresso -->
                  <?php if(isset($valorTotalParcelas) && $valorTotalParcelas > 0): ?>
                  <div class="row">
                    <div class="col-12">
                      <div class="card">
                        <div class="card-body">
                          <h6 class="mb-2">Progresso de Recebimento</h6>
                          <?php 
                            $percentual = percentualPago($valorTotalRecebido ?? 0, $valorTotalParcelas);
                          ?>
                          <?= barraProgresso($percentual) ?>
                          <small class="text-muted">
                            Recebido: R$ <?= monetarioExibir($valorTotalRecebido ?? 0)?> de R$ <?= monetarioExibir($valorTotalParcelas)?>
                          </small>
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php endif; ?>

                  <div class="row">
                    <div class="col-12">
                      <div class="card">
                        <div class="card-body">
                          <a href="<?= base_url('/Admin/Contrato/lancamentos/cadastrar/'.$idContrato)?>" class="btn btn-info">
                            <i class="fas fa-plus fa-fw"></i>
                            Novo Lançamento
                          </a>
                          <a href="<?= base_url('/Admin/Contrato/editar/'.$contrato->id_turma.'/'.$contrato->aluno_id)?>" class="btn btn-primary">
                            <i class="fas fa-edit fa-fw"></i>
                            Editar Contrato
                          </a>
                          <span class="float-right">
                            <a href="<?= base_url('/Admin/Contrato/editar/'.$contrato->id_turma.'/'.$contrato->aluno_id)?>" class="btn btn-danger">
                                <i class="fas fa-trash fa-fw"></i>
                              Excluir Contrato
                            </a>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Tabela de Lançamentos -->
                  <div class="row mt-4">
                    <div class="col-12">
                      <div class="card">
                        <div class="card-header">
                          <h5 class="card-title mb-0">
                            <i class="fas fa-list"></i> Lançamentos
                          </h5>
                        </div>
                        <div class="card-body p-0">
                          <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                              <thead class="thead-light">
                                <tr>
                                  <th width="5%">#</th>
                                  <th>Lançamento</th>
                                  <th width="10%">Nº Parcela</th>
                                  <th width="12%">Vencimento</th>
                                  <th width="12%">Valor</th>
                                  <th width="12%">Pago</th>
                                  <th width="10%">Status</th>
                                  <th width="15%" class="text-center">Ações</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php if (!empty($lancamentos) && is_array($lancamentos)): ?>
                                  <?php foreach ($lancamentos as $l): ?>
                                    <?php 
                                      $total_pago_parcela = $l->total_pago ?? 0;
                                      $valor_restante = $l->valor_parcela - $total_pago_parcela;
                                      $is_vencido = isVencido($l->data_vencimento);
                                      $row_class = '';
                                      if($l->status == 2){
                                        $row_class = 'table-success';
                                      } elseif($l->status == 4 || ($is_vencido && $l->status != 2)){
                                        $row_class = 'table-danger';
                                      } elseif($l->status == 3){
                                        $row_class = 'table-warning';
                                      }
                                    ?>
                                    <tr class="<?= $row_class ?>">
                                      <td><?= $l->id ?></td>
                                      <td>
                                        <strong><?= esc($l->tipo_lancamento) ?></strong>
                                        <?php if($is_vencido && $l->status != 2): ?>
                                          <br><small class="text-danger">
                                            <i class="fas fa-exclamation-circle"></i>
                                            <?= textoVencimento($l->data_vencimento) ?>
                                          </small>
                                        <?php endif; ?>
                                      </td>
                                      <td class="text-center">
                                        <span class="badge badge-secondary"><?= $l->numero_parcela ?></span>
                                      </td>
                                      <td><?= dataBR($l->data_vencimento) ?></td>
                                      <td><strong>R$ <?= monetarioExibir($l->valor_parcela) ?></strong></td>
                                      <td>
                                        <?php if($total_pago_parcela > 0): ?>
                                          <span class="text-success">R$ <?= monetarioExibir($total_pago_parcela) ?></span>
                                          <?php if($l->qtd_pagamentos > 1): ?>
                                            <br><small class="text-muted">(<?= $l->qtd_pagamentos ?> pagamentos)</small>
                                          <?php endif; ?>
                                        <?php else: ?>
                                          <span class="text-muted">--</span>
                                        <?php endif; ?>
                                      </td>
                                      <td><?= badgeStatusParcela($l->status) ?></td>
                                      <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                          <?php if($valor_restante > 0): ?>
                                            <a href="<?= base_url('/Admin/Contrato/registrarPagamento/'.$l->id)?>" 
                                               class="btn btn-success btn-sm" 
                                               title="Registrar Pagamento"
                                               data-toggle="tooltip">
                                              <i class="fas fa-dollar-sign"></i>
                                            </a>
                                          <?php endif; ?>
                                          <a href="<?= base_url('/Admin/Contrato/detalhesParcela/'.$l->id)?>" 
                                             class="btn btn-info btn-sm" 
                                             title="Ver Detalhes"
                                             data-toggle="tooltip">
                                            <i class="fas fa-eye"></i>
                                          </a>
                                          <?php if($total_pago_parcela == 0): ?>
                                            <a href="<?= base_url('/Admin/Contrato/excluirParcela/'.$l->id)?>" 
                                               class="btn btn-danger btn-sm" 
                                               title="Excluir"
                                               data-toggle="tooltip"
                                               onclick="return confirm('Tem certeza que deseja excluir esta parcela?')">
                                              <i class="fas fa-trash"></i>
                                            </a>
                                          <?php endif; ?>
                                        </div>
                                      </td>
                                    </tr>
                                  <?php endforeach; ?>
                                <?php else: ?>
                                  <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                      <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                      Nenhum lançamento encontrado.
                                      <br>
                                      <a href="<?= base_url('/Admin/Contrato/lancamentos/cadastrar/'.$idContrato)?>" class="btn btn-primary btn-sm mt-2">
                                        <i class="fas fa-plus"></i> Criar Primeiro Lançamento
                                      </a>
                                    </td>
                                  </tr>
                                <?php endif; ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

        
                
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

<script>
// Ativar tooltips
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>

