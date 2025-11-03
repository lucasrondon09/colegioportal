<?php 
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
            <h1>Registrar Pagamento</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/home')?>">Home</a></li>
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/Contrato')?>">Contratos</a></li>
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/Contrato/lancamentos/'.$contrato->id)?>">Lançamentos</a></li>
              <li class="breadcrumb-item active">Registrar Pagamento</li>
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
            
            <!-- Card de Informações da Parcela -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">
                  <a href="<?= base_url('Admin/Contrato/lancamentos/'.$contrato->id)?>" class="text-white">
                    <i class="fas fa-chevron-left"></i>
                  </a>
                  Informações da Parcela
                </h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <p class="mb-2"><strong>Aluno:</strong> <?= $contrato->aluno_nome ?></p>
                    <p class="mb-2"><strong>Parcela:</strong> #<?= $parcela->numero_parcela ?> - <?= $parcela->tipo_lancamento ?></p>
                    <p class="mb-2"><strong>Vencimento:</strong> <?= dataBR($parcela->data_vencimento) ?></p>
                  </div>
                  <div class="col-md-6">
                    <div class="info-box bg-light">
                      <span class="info-box-icon bg-info"><i class="fas fa-money-bill-wave"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text">Valor da Parcela</span>
                        <span class="info-box-number">R$ <?= monetarioExibir($parcela->valor_parcela) ?></span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Resumo de Pagamentos Anteriores -->
                <?php if(!empty($pagamentos_parcela)): ?>
                <div class="alert alert-info mt-3">
                  <h6><i class="fas fa-info-circle"></i> Pagamentos Anteriores</h6>
                  <table class="table table-sm table-bordered mb-0">
                    <thead>
                      <tr>
                        <th>Data</th>
                        <th>Forma</th>
                        <th>Valor</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach($pagamentos_parcela as $pag): ?>
                      <tr>
                        <td><?= dataBR($pag->data_pagamento) ?></td>
                        <td><?= $pag->forma_pagamento_nome ?? 'Não informado' ?></td>
                        <td>R$ <?= monetarioExibir($pag->valor_pago) ?></td>
                        <td><span class="badge badge-success"><?= $pag->status ?></span></td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                      <tr class="bg-light">
                        <th colspan="2">Total Pago:</th>
                        <th>R$ <?= monetarioExibir($total_pago) ?></th>
                        <th></th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
                <?php endif; ?>

                <!-- Resumo Financeiro -->
                <div class="row mt-3">
                  <div class="col-md-4">
                    <div class="small-box bg-info">
                      <div class="inner">
                        <h4>R$ <?= monetarioExibir($parcela->valor_parcela) ?></h4>
                        <p>Valor Original</p>
                      </div>
                      <div class="icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="small-box bg-success">
                      <div class="inner">
                        <h4>R$ <?= monetarioExibir($total_pago) ?></h4>
                        <p>Total Pago</p>
                      </div>
                      <div class="icon">
                        <i class="fas fa-check-circle"></i>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="small-box bg-<?= $valor_restante > 0 ? 'warning' : 'success' ?>">
                      <div class="inner">
                        <h4>R$ <?= monetarioExibir($valor_restante) ?></h4>
                        <p>Saldo Restante</p>
                      </div>
                      <div class="icon">
                        <i class="fas fa-<?= $valor_restante > 0 ? 'exclamation-triangle' : 'check-double' ?>"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Formulário de Pagamento -->
            <div class="card card-success">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-dollar-sign"></i>
                  Novo Pagamento
                </h3>
              </div>
              
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
                <div class="alert alert-<?= $classAlert;?> alert-dismissible fade show m-3" role="alert">
                  <i class="fas fa-info-circle"></i>
                  <?= $message;?>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>                    
              <?php endif;?>
              
              <div class="row mx-3">
                <div class="col-12">
                  <span class="text-danger"><?= $validate->listErrors(); ?></span>
                </div>
              </div>

              <?= form_open(base_url('Admin/Contrato/registrarPagamento/'.$parcela->id)) ?>
              <?= csrf_field() ?>
                <div class="card-body">
                  
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="id_forma_pagamento">Forma de Pagamento <span class="text-danger">*</span></label>
                        <select class="form-control" id="id_forma_pagamento" name="id_forma_pagamento" required>
                          <option value="">Selecione...</option>
                          <?php if(!empty($formas_pagamento)): ?>
                            <?php foreach($formas_pagamento as $forma): ?>
                              <option value="<?= $forma->id ?>" <?= set_select('id_forma_pagamento', $forma->id) ?>>
                                <?= $forma->nome ?>
                                <?php if(isset($forma->taxa_percentual) && $forma->taxa_percentual > 0): ?>
                                  (Taxa: <?= number_format($forma->taxa_percentual, 2) ?>%)
                                <?php endif; ?>
                              </option>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </select>
                        <small class="form-text text-muted">Selecione como o pagamento foi realizado</small>
                      </div>
                    </div>
                    
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="data_pagamento">Data do Pagamento <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control" 
                               id="data_pagamento" 
                               name="data_pagamento" 
                               value="<?= set_value('data_pagamento', date('Y-m-d')) ?>"
                               max="<?= date('Y-m-d') ?>"
                               required>
                        <small class="form-text text-muted">Data em que o pagamento foi recebido</small>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="valor_pago">Valor Pago <span class="text-danger">*</span></label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">R$</span>
                          </div>
                          <input type="text" 
                                 class="form-control money" 
                                 id="valor_pago" 
                                 name="valor_pago" 
                                 value="<?= set_value('valor_pago', monetarioExibir($valor_restante)) ?>"
                                 required>
                        </div>
                        <small class="form-text text-muted">
                          Valor máximo sugerido: R$ <?= monetarioExibir($valor_restante) ?>
                        </small>
                      </div>
                    </div>
                    
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="desconto_aplicado">Desconto</label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">R$</span>
                          </div>
                          <input type="text" 
                                 class="form-control money" 
                                 id="desconto_aplicado" 
                                 name="desconto_aplicado" 
                                 value="<?= set_value('desconto_aplicado', '0,00') ?>">
                        </div>
                        <small class="form-text text-muted">Desconto concedido (se houver)</small>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="juros_aplicado">Juros</label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">R$</span>
                          </div>
                          <input type="text" 
                                 class="form-control money" 
                                 id="juros_aplicado" 
                                 name="juros_aplicado" 
                                 value="<?= set_value('juros_aplicado', '0,00') ?>">
                        </div>
                        <small class="form-text text-muted">Juros por atraso (se houver)</small>
                      </div>
                    </div>
                    
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="multa_aplicada">Multa</label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">R$</span>
                          </div>
                          <input type="text" 
                                 class="form-control money" 
                                 id="multa_aplicada" 
                                 name="multa_aplicada" 
                                 value="<?= set_value('multa_aplicada', '0,00') ?>">
                        </div>
                        <small class="form-text text-muted">Multa por atraso (se houver)</small>
                      </div>
                    </div>
                  </div>

                  <!-- Cálculo Automático do Valor Líquido -->
                  <div class="row">
                    <div class="col-12">
                        <div class="alert alert-light border">
                        <h6 class="mb-2">Resumo do Pagamento</h6>
                        <table class="table table-sm mb-0">
                          <tbody>
                          <tr>
                            <td width="70%"><strong>Valor Pago:</strong></td>
                            <td class="text-right" id="resumo_valor_pago"></td>
                          </tr>
                          <tr class="text-success">
                            <td><strong>(-) Desconto:</strong></td>
                            <td class="text-right" id="resumo_desconto"></td>
                          </tr>
                          <tr class="text-danger">
                            <td><strong>(+) Juros:</strong></td>
                            <td class="text-right" id="resumo_juros"></td>
                          </tr>
                          <tr class="text-danger">
                            <td><strong>(+) Multa:</strong></td>
                            <td class="text-right" id="resumo_multa"></td>
                          </tr>
                          <tr class="bg-light">
                            <td><strong>VALOR LÍQUIDO:</strong></td>
                            <td class="text-right"><strong id="resumo_liquido"></strong></td>
                          </tr>
                          </tbody>
                        </table>
                        </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-12">
                      <div class="form-group">
                        <label for="observacao">Observações</label>
                        <textarea class="form-control" 
                                  id="observacao" 
                                  name="observacao" 
                                  rows="3"
                                  placeholder="Informações adicionais sobre o pagamento (opcional)"><?= set_value('observacao') ?></textarea>
                        <small class="form-text text-muted">Ex: Número do comprovante, observações sobre negociação, etc.</small>
                      </div>
                    </div>
                  </div>

                </div>
                <!-- /.card-body -->
                
                <div class="card-footer">
                  <button type="submit" class="btn btn-success" id="submit">
                    <i class="fas fa-check fa-fw"></i>
                    Confirmar Pagamento
                  </button>
                  <a href="<?= base_url('Admin/Contrato/lancamentos/'.$contrato->id)?>" class="btn btn-secondary">
                    <i class="fas fa-times fa-fw"></i>
                    Cancelar
                  </a>
                </div>
              <?= form_close() ?>
            </div>
            <!-- /.card -->

          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="<?= base_url('assets/admin/dist/js/funcoes.js')?>"></script>
<script>
$(document).ready(function(){
  
  // Validação antes de enviar
  $('form').on('submit', function(e){
    var valorPago = moneyToFloat($('#valor_pago').val());
    var valorRestante = <?= $valor_restante ?>;
    
    if(valorPago <= 0) {
      e.preventDefault();
      alert('O valor pago deve ser maior que zero!');
      return false;
    }
    
    if(valorPago > valorRestante) {
      if(!confirm('O valor pago (R$ ' + formatMoney(valorPago) + ') é maior que o saldo restante (R$ ' + formatMoney(valorRestante) + '). Deseja continuar?')) {
        e.preventDefault();
        return false;
      }
    }
    
    return true;
  });
  
});
</script>

