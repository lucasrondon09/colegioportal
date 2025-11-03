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
              <?= form_open() ?>
                  <fieldset>
              <?= csrf_field() ?>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-12">
                      <h3><?= $contrato->aluno_nome?>
                      <span class="float-right">
                        <a href="<?= base_url('/Admin/Contrato/lancamentos/cadastrar/'.$idContrato)?>" class="btn btn-primary">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square mr-1" viewBox="0 0 16 16" style="vertical-align:middle">
                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                          </svg>
                          Editar Contrato
                        </a>
                        <a href="<?= base_url('/Admin/Contrato/lancamentos/cadastrar/'.$idContrato)?>" class="btn btn-info">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg mr-1" viewBox="0 0 16 16" style="vertical-align:middle">
                            <path d="M8 1a.5.5 0 0 1 .5.5V7.5H14a.5.5 0 0 1 0 1H8.5V14a.5.5 0 0 1-1 0V8.5H2a.5.5 0 0 1 0-1h5.5V1.5A.5.5 0 0 1 8 1z"/>
                          </svg>
                          Novo Lançamento
                        </a>
                      </span>
                      </h3>
                      <h6 class="text-muted"><span class="font-weight-bold"><?= $contrato->turma_nome?> <?= getPeriodo($contrato->id_periodo)?></span></h6>
                        <h6 class="text-muted"><span class="font-weight-bold"> Responsável Financeiro: </span><?= $contrato->responsavel_nome?></h6>
                      <h6 class="text-muted"><span class="font-weight-bold"> Situação: </span><?= !empty($contrato->status) ? statusContrato($contrato->status) : '--';?></h6>
                      <h6 class="text-muted"><span class="font-weight-bold"> Tipo Matricula: </span><?= !empty($contrato->tipo_contrato) ? tipoContrato($contrato->tipo_contrato) : '--';?></h6>
                      <h6 class="text-muted"><span class="font-weight-bold"> Data: </span><?= !empty($contrato->data_inicio) ? dataBR($contrato->data_inicio) : '--';?> <?= !empty($contrato->data_fim) ? 'à '.dataBR($contrato->data_fim) : '--';?></h6>
                    </div>
                  </div>
                  <div class="row mt-3">
                    <div class="col-md-3">
                      <div class="card border-info mb-3">     
                        <div class="card-body">
                          <p class="text-muted">VALOR DO CONTRATO <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle p-0 float-right" title="Ajuda" style="width:17px;height:17px;padding:0;display:inline-flex;align-items:center;justify-content:center;">?</a></p>
                          <h3 class="card-text mb-3"><strong>R$ <?= monetarioExibir($contrato->valor_total)?></strong></h3>
                          <p class="text-muted"><?= !empty($contrato->parcelas) ? $contrato->parcelas .' parcelas cadastradas' : 'Nenhuma parcela lançada' ?></p>
                         </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="card border-info mb-3">     
                        <div class="card-body">
                          <p class="text-muted">VALOR A RECEBER <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle p-0 float-right" title="Ajuda" style="width:17px;height:17px;padding:0;display:inline-flex;align-items:center;justify-content:center;">?</a></p>
                          <h3 class="card-text mb-3"><strong>R$ <?= !empty($valorTotalParcelas) ? monetarioExibir($valorTotalParcelas) : '--';?></strong></h3>
                          <p class="text-muted"><?= !empty($totalParcelas) ? $totalParcelas.' parcelas lançadas' : 'Nenhuma parcela lançada'?></p>
                         </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="card border-info mb-3">     
                        <div class="card-body">
                          <p class="text-muted">VALOR RECEBIDO <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle p-0 float-right" title="Ajuda" style="width:17px;height:17px;padding:0;display:inline-flex;align-items:center;justify-content:center;">?</a></p>
                          <h3 class="card-text mb-3"><strong>R$ 26.101,20</strong></h3>
                          <p class="text-muted">Nenhuma cobrança recebida</p>
                         </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="card border-info mb-3">     
                        <div class="card-body">
                          <p class="text-muted">VALOR VENCIDO <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle p-0 float-right" title="Ajuda" style="width:17px;height:17px;padding:0;display:inline-flex;align-items:center;justify-content:center;">?</a></p>
                          <h3 class="card-text mb-3"><strong>R$ 26.101,20</strong></h3>
                          <p class="text-muted">Nenhuma cobrança vencida</p>
                         </div>
                      </div>
                    </div>
                  </div>

                  <div class="row mt-4">
                    <div class="col-12">
                      <div class="card">
                        <div class="card-header">
                          <h5 class="card-title mb-0">Lançamentos</h5>
                        </div>
                        <div class="card-body p-0">
                          <div class="table-responsive">
                            <table class="table table-striped mb-0">
                              <thead>
                                <tr>
                                  <th>Lançamento</th>
                                  <th>Nº Parcela</th>
                                  <th>Data Vencimento</th>
                                  <th>Valor Parcela</th>
                                  <th>Data Pagamento</th>
                                  <th>Forma Pagamento</th>
                                  <th>Valor Pago</th>
                                  <th>Status</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php if (!empty($lancamentos) && is_array($lancamentos)): ?>
                                  <?php foreach ($lancamentos as $l): ?>
                                    <tr>
                                      
                                      <td><?= esc($l->tipo_lancamento) ?></td>
                                      <td><?= esc($l->numero_parcela) ?></td>
                                      <td><?= !empty($l->data_vencimento) ? date('d/m/Y', strtotime($l->data_vencimento)) : '--' ?></td>
                                      <td><?= isset($l->valor_parcela) ? 'R$ ' . number_format((float)$l->valor_parcela, 2, ',', '.') : '--' ?></td>
                                      <td><?= !empty($l->data_pagamento) ? date('d/m/Y', strtotime($l->data_pagamento)) : '--' ?></td>
                                      <td><?= esc($l->id_forma_pagamento) ?></td>
                                      <td><?= isset($l->valor_pago) ? 'R$ ' . number_format((float)$l->valor_pago, 2, ',', '.') : '--' ?></td>
                                      <td><?= statusContrato(esc($l->status))  ?></td>
                                    </tr>
                                  <?php endforeach; ?>
                                <?php else: ?>
                                  <tr>
                                    <td colspan="7" class="text-center text-muted">Nenhum lançamento encontrado.</td>
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

