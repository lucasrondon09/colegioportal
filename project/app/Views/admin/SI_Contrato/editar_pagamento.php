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
            <h1>Editar Pagamento</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/home')?>">Home</a></li>
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/Contrato')?>">Contratos</a></li>
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/Contrato/lancamentos/'.$contrato->id)?>">Lançamentos</a></li>
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/Contrato/detalhesParcela/'.$parcela->id)?>">Detalhes</a></li>
              <li class="breadcrumb-item active">Editar Pagamento</li>
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
            
            <!-- Card de Informações -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">
                  <a href="<?= base_url('Admin/Contrato/detalhesParcela/'.$parcela->id)?>" class="text-white">
                    <i class="fas fa-chevron-left"></i>
                  </a>
                  Informações do Pagamento
                </h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-4">
                    <p class="mb-2"><strong>Aluno:</strong> <?= $contrato->nome_aluno ?></p>
                    <p class="mb-2"><strong>Parcela:</strong> #<?= $parcela->numero_parcela ?> - <?= $parcela->tipo_lancamento ?></p>
                  </div>
                  <div class="col-md-4">
                    <p class="mb-2"><strong>Valor da Parcela:</strong> R$ <?= monetarioExibir($parcela->valor_parcela) ?></p>
                    <p class="mb-2"><strong>Vencimento:</strong> <?= dataBR($parcela->data_vencimento) ?></p>
                  </div>
                  <div class="col-md-4">
                    <p class="mb-2"><strong>ID do Pagamento:</strong> #<?= $pagamento->id ?></p>
                    <p class="mb-2"><strong>Registrado em:</strong> <?= date('d/m/Y H:i', strtotime($pagamento->created_at)) ?></p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Formulário de Edição -->
            <div class="card card-warning">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-edit"></i>
                  Editar Dados do Pagamento
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

              <?= form_open(base_url('Admin/Contrato/editarPagamento/'.$pagamento->id)) ?>
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
                              <option value="<?= $forma->id ?>" <?= $pagamento->id_forma_pagamento == $forma->id ? 'selected' : '' ?>>
                                <?= $forma->nome ?>
                                <?php if(isset($forma->taxa_percentual) && $forma->taxa_percentual > 0): ?>
                                  (Taxa: <?= number_format($forma->taxa_percentual, 2) ?>%)
                                <?php endif; ?>
                              </option>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </select>
                      </div>
                    </div>
                    
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="data_pagamento">Data do Pagamento <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control" 
                               id="data_pagamento" 
                               name="data_pagamento" 
                               value="<?= $pagamento->data_pagamento ?>"
                               max="<?= date('Y-m-d') ?>"
                               required>
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
                                 value="<?= monetarioExibir($pagamento->valor_pago) ?>"
                                 required>
                        </div>
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
                                 value="<?= monetarioExibir($pagamento->desconto_aplicado ?? 0) ?>">
                        </div>
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
                                 value="<?= monetarioExibir($pagamento->juros_aplicado ?? 0) ?>">
                        </div>
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
                                 value="<?= monetarioExibir($pagamento->multa_aplicada ?? 0) ?>">
                        </div>
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
                              <td class="text-right" id="resumo_valor_pago">R$ 0,00</td>
                            </tr>
                            <tr class="text-success">
                              <td><strong>(-) Desconto:</strong></td>
                              <td class="text-right" id="resumo_desconto">R$ 0,00</td>
                            </tr>
                            <tr class="text-danger">
                              <td><strong>(+) Juros:</strong></td>
                              <td class="text-right" id="resumo_juros">R$ 0,00</td>
                            </tr>
                            <tr class="text-danger">
                              <td><strong>(+) Multa:</strong></td>
                              <td class="text-right" id="resumo_multa">R$ 0,00</td>
                            </tr>
                            <tr class="bg-light">
                              <td><strong>VALOR LÍQUIDO:</strong></td>
                              <td class="text-right"><strong id="resumo_liquido">R$ 0,00</strong></td>
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
                                  placeholder="Informações adicionais sobre o pagamento (opcional)"><?= $pagamento->observacao ?? '' ?></textarea>
                      </div>
                    </div>
                  </div>

                </div>
                <!-- /.card-body -->
                
                <div class="card-footer">
                  <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save fa-fw"></i>
                    Salvar Alterações
                  </button>
                  <a href="<?= base_url('Admin/Contrato/detalhesParcela/'.$parcela->id)?>" class="btn btn-secondary">
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
<script src="<?= base_url('assets/admin/dist/js/funcoes.js')?>"></script>
<script>
$(document).ready(function(){
  
  
  // Validação antes de enviar
  $('form').on('submit', function(e){
    var valorPago = moneyToFloat($('#valor_pago').val());
    
    if(valorPago <= 0) {
      e.preventDefault();
      alert('O valor pago deve ser maior que zero!');
      return false;
    }
    
    return true;
  });
  
});
</script>

