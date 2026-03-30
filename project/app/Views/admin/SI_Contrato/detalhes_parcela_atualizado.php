<?php 
$session = \Config\Services::session();
$validate = \Config\Services::validation();

// Funções helper inline (caso não estejam no helper)
if(!function_exists('badgeStatusParcela')){
  function badgeStatusParcela($status){
    $badges = [
       '1' => '<span class="badge badge-secondary">Aberto</span>',
       '2' => '<span class="badge badge-success">Pago</span>',
       '3' => '<span class="badge badge-warning">Pago Parcialmente</span>',
       '4' => '<span class="badge badge-danger">Atrasado</span>',
    ];
    return $badges[$status] ?? '<span class="badge badge-secondary">Desconhecido</span>';
  }
}

if(!function_exists('isVencido')){
  function isVencido($data_vencimento){
    $hoje = date('Y-m-d');
    return $data_vencimento < $hoje;
  }
}

if(!function_exists('statusParcela')){
  function statusParcela($status){
    $statuses = [
       '1' => 'Aberto',
       '2' => 'Pago',
       '3' => 'Pago Parcialmente',
       '4' => 'Atrasado',
    ];
    return $statuses[$status] ?? 'Desconhecido';
  }
}
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Detalhes da Parcela</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/home')?>">Home</a></li>
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/Contrato')?>">Contratos</a></li>
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/Contrato/lancamentos/'.$contrato->id)?>">Lançamentos</a></li>
              <li class="breadcrumb-item active">Detalhes</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            
            <!-- Alertas -->
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
              <div class="alert alert-<?= $classAlert;?> alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle"></i>
                <?= $message;?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>                    
            <?php endif;?>

            <!-- Card de Informações do Contrato -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <a href="<?= base_url('Admin/Contrato/lancamentos/'.$contrato->id)?>" class="text-decoration-none text-dark">
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
                  </div>
                  <div class="col-md-6">
                    <p class="mb-2"><strong>Contrato:</strong> #<?= $contrato->id ?></p>
                    <p class="mb-2"><strong>Status:</strong> <?= statusContrato($contrato->status) ?></p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card de Detalhes da Parcela -->
            <div class="card">
              <div class="card-header bg-primary text-white">
                <h3 class="card-title">
                  <i class="fas fa-file-invoice-dollar"></i>
                  Detalhes da Parcela #<?= $parcela->numero_parcela ?>
                </h3>
                <div class="card-tools">
                  <?= badgeStatusParcela($parcela->status) ?>
                </div>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-4">
                    <div class="info-box">
                      <span class="info-box-icon bg-info"><i class="fas fa-calendar-alt"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text">Data de Vencimento</span>
                        <span class="info-box-number"><?= dataBR($parcela->data_vencimento) ?></span>
                        <?php if(isVencido($parcela->data_vencimento) && $parcela->status != 2): ?>
                          <small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Vencida</small>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-md-4">
                    <div class="info-box">
                      <span class="info-box-icon bg-warning"><i class="fas fa-money-bill-wave"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text">Valor da Parcela</span>
                        <span class="info-box-number">R$ <?= monetarioExibir($parcela->valor_parcela) ?></span>
                        <small class="text-muted"><?= $parcela->tipo_lancamento ?></small>
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-md-4">
                    <div class="info-box">
                      <span class="info-box-icon bg-<?= $valor_restante > 0 ? 'danger' : 'success' ?>">
                        <i class="fas fa-<?= $valor_restante > 0 ? 'exclamation-circle' : 'check-circle' ?>"></i>
                      </span>
                      <div class="info-box-content">
                        <span class="info-box-text">Saldo Restante</span>
                        <span class="info-box-number">R$ <?= monetarioExibir($valor_restante) ?></span>
                        <?php if($valor_restante == 0): ?>
                          <small class="text-success"><i class="fas fa-check"></i> Pago integralmente</small>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Barra de Progresso do Pagamento -->
                <?php if($parcela->valor_parcela > 0): ?>
                <div class="row mt-3">
                  <div class="col-12">
                    <h6 class="mb-2">Progresso do Pagamento</h6>
                    <?php 
                      $percentual_pago = ($total_pago / $parcela->valor_parcela) * 100;
                      $percentual_pago = min(100, max(0, $percentual_pago));
                      
                      $cor_barra = 'bg-success';
                      if($percentual_pago < 30){
                        $cor_barra = 'bg-danger';
                      } elseif($percentual_pago < 100){
                        $cor_barra = 'bg-warning';
                      }
                    ?>
                    <div class="progress" style="height: 25px;">
                      <div class="progress-bar <?= $cor_barra ?>" role="progressbar" style="width: <?= $percentual_pago ?>%" aria-valuenow="<?= $percentual_pago ?>" aria-valuemin="0" aria-valuemax="100">
                        <?= number_format($percentual_pago, 1) ?>%
                      </div>
                    </div>
                    <small class="text-muted">
                      Pago: R$ <?= monetarioExibir($total_pago) ?> de R$ <?= monetarioExibir($parcela->valor_parcela) ?>
                    </small>
                  </div>
                </div>
                <?php endif; ?>

                <!-- Informações Adicionais -->
                <?php if(isset($parcela->valor_desconto) || isset($parcela->valor_juros) || isset($parcela->valor_multa)): ?>
                <div class="row mt-3">
                  <div class="col-12">
                    <h6>Valores Adicionais</h6>
                    <table class="table table-sm table-bordered">
                      <tbody>
                        <?php if(isset($parcela->valor_desconto) && $parcela->valor_desconto > 0): ?>
                        <tr>
                          <td width="30%"><strong>Desconto:</strong></td>
                          <td class="text-success">- R$ <?= monetarioExibir($parcela->valor_desconto) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if(isset($parcela->valor_juros) && $parcela->valor_juros > 0): ?>
                        <tr>
                          <td><strong>Juros:</strong></td>
                          <td class="text-danger">+ R$ <?= monetarioExibir($parcela->valor_juros) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if(isset($parcela->valor_multa) && $parcela->valor_multa > 0): ?>
                        <tr>
                          <td><strong>Multa:</strong></td>
                          <td class="text-danger">+ R$ <?= monetarioExibir($parcela->valor_multa) ?></td>
                        </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
                <?php endif; ?>

                <!-- Botões de Ação -->
                <div class="row mt-3">
                  <div class="col-12">
                    <?php if($valor_restante > 0): ?>
                      <a href="<?= base_url('Admin/Contrato/registrarPagamento/'.$parcela->id)?>" class="btn btn-success">
                        <i class="fas fa-dollar-sign"></i> Registrar Pagamento
                      </a>
                    <?php endif; ?>
                    
                    <?php if(empty($pagamentos)): ?>
                      <a href="<?= base_url('Admin/Contrato/excluirParcela/'.$parcela->id)?>" 
                         class="btn btn-danger"
                         onclick="return confirm('Tem certeza que deseja excluir esta parcela?')">
                        <i class="fas fa-trash"></i> Excluir Parcela
                      </a>
                    <?php endif; ?>
                    
                    <a href="<?= base_url('Admin/Contrato/lancamentos/'.$contrato->id)?>" class="btn btn-secondary">
                      <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card de Histórico de Pagamentos -->
            <div class="card">
              <div class="card-header bg-success text-white">
                <h3 class="card-title">
                  <i class="fas fa-history"></i>
                  Histórico de Pagamentos
                </h3>
              </div>
              <div class="card-body p-0">
                <?php if(!empty($pagamentos) && is_array($pagamentos)): ?>
                  <div class="table-responsive">
                    <table class="table table-striped mb-0">
                      <thead>
                        <tr>
                          <th width="5%">#</th>
                          <th width="10%">Data Pagamento</th>
                          <th>Forma de Pagamento</th>
                          <th width="10%">Valor Pago</th>
                          <th width="8%">Desconto</th>
                          <th width="8%">Juros</th>
                          <th width="8%">Multa</th>
                          <th width="10%">Valor Líquido</th>
                          <th width="8%">Status</th>
                          <th width="12%" class="text-center">Ações</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach($pagamentos as $pag): ?>
                          <tr>
                            <td><?= $pag->id ?></td>
                            <td><?= dataBR($pag->data_pagamento) ?></td>
                            <td>
                              <i class="fas fa-credit-card text-primary"></i>
                              <?= $pag->forma_pagamento ?? 'Não informado' ?>
                            </td>
                            <td><strong>R$ <?= monetarioExibir($pag->valor_pago) ?></strong></td>
                            <td>
                              <?php if(isset($pag->desconto_aplicado) && $pag->desconto_aplicado > 0): ?>
                                <span class="text-success">R$ <?= monetarioExibir($pag->desconto_aplicado) ?></span>
                              <?php else: ?>
                                <span class="text-muted">--</span>
                              <?php endif; ?>
                            </td>
                            <td>
                              <?php if(isset($pag->juros_aplicado) && $pag->juros_aplicado > 0): ?>
                                <span class="text-danger">R$ <?= monetarioExibir($pag->juros_aplicado) ?></span>
                              <?php else: ?>
                                <span class="text-muted">--</span>
                              <?php endif; ?>
                            </td>
                            <td>
                              <?php if(isset($pag->multa_aplicada) && $pag->multa_aplicada > 0): ?>
                                <span class="text-danger">R$ <?= monetarioExibir($pag->multa_aplicada) ?></span>
                              <?php else: ?>
                                <span class="text-muted">--</span>
                              <?php endif; ?>
                            </td>
                            <td><strong>R$ <?= monetarioExibir($pag->valor_liquido ?? $pag->valor_pago) ?></strong></td>
                            <td>
                              <?php if($pag->status == 'CONFIRMADO'): ?>
                                <span class="badge badge-success">Confirmado</span>
                              <?php elseif($pag->status == 'PENDENTE'): ?>
                                <span class="badge badge-warning">Pendente</span>
                              <?php elseif($pag->status == 'ESTORNADO'): ?>
                                <span class="badge badge-danger">Estornado</span>
                              <?php else: ?>
                                <span class="badge badge-secondary"><?= $pag->status ?></span>
                              <?php endif; ?>
                            </td>
                            <td class="text-center">
                              <div class="btn-group btn-group-sm" role="group">
                                <?php if($pag->status != 'ESTORNADO'): ?>
                                  <a href="<?= base_url('Admin/Contrato/editarPagamento/'.$pag->id)?>" 
                                     class="btn btn-warning btn-sm" 
                                     title="Editar"
                                     data-toggle="tooltip">
                                    <i class="fas fa-edit"></i>
                                  </a>
                                  <a href="<?= base_url('Admin/Contrato/excluirPagamento/'.$pag->id)?>" 
                                     class="btn btn-danger btn-sm" 
                                     title="Excluir"
                                     data-toggle="tooltip"
                                     onclick="return confirm('Tem certeza que deseja excluir este pagamento?')">
                                    <i class="fas fa-trash"></i>
                                  </a>
                                <?php else: ?>
                                  <span class="badge badge-secondary">Estornado</span>
                                <?php endif; ?>
                              </div>
                            </td>
                          </tr>
                          <?php if(!empty($pag->observacao)): ?>
                          <tr>
                            <td colspan="10" class="bg-light">
                              <small><strong>Observação:</strong> <?= esc($pag->observacao) ?></small>
                            </td>
                          </tr>
                          <?php endif; ?>
                          <?php if(!empty($pag->motivo_estorno)): ?>
                          <tr>
                            <td colspan="10" class="bg-danger text-white">
                              <small><strong>Motivo do Estorno:</strong> <?= esc($pag->motivo_estorno) ?></small>
                              <br><small>Estornado em: <?= date('d/m/Y H:i', strtotime($pag->data_estorno)) ?></small>
                            </td>
                          </tr>
                          <?php endif; ?>
                        <?php endforeach; ?>
                      </tbody>
                      <tfoot class="bg-light">
                        <tr>
                          <th colspan="3" class="text-right">TOTAL PAGO:</th>
                          <th>R$ <?= monetarioExibir($total_pago) ?></th>
                          <th colspan="6"></th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                <?php else: ?>
                  <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Nenhum pagamento registrado para esta parcela.</p>
                    <?php if($valor_restante > 0): ?>
                      <a href="<?= base_url('Admin/Contrato/registrarPagamento/'.$parcela->id)?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Registrar Primeiro Pagamento
                      </a>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <!-- Card de Informações do Sistema -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-info-circle"></i>
                  Informações do Sistema
                </h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <p class="mb-1"><small class="text-muted">Criado em:</small></p>
                    <p><?= isset($parcela->created_at) ? date('d/m/Y H:i:s', strtotime($parcela->created_at)) : '--' ?></p>
                  </div>
                  <div class="col-md-6">
                    <p class="mb-1"><small class="text-muted">Última atualização:</small></p>
                    <p><?= isset($parcela->updated_at) ? date('d/m/Y H:i:s', strtotime($parcela->updated_at)) : '--' ?></p>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
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

