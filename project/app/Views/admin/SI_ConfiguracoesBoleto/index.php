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
          <h1><i class="fas fa-cog"></i> <?= esc($titulo) ?></h1>
        </div>
      </div>
    </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
            <div class="card">
                <?php if (session()->has('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                        <?= session('success') ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>

                <?php if (session()->has('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                        <?= session('error') ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('/Admin/Boleto-Config/salvar') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="card-body">
                        
                        <!-- Juros e Multa -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-primary">
                                        <h5 class="mb-0"><i class="fas fa-percentage"></i> Juros e Multa</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="juros_percentual">Juros ao Mês (%)</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="juros_percentual" 
                                                   name="juros_percentual" 
                                                   value="<?= esc($config['juros_percentual']) ?>" 
                                                   step="0.01" 
                                                   min="0" 
                                                   max="100" 
                                                   required>
                                            <small class="form-text text-muted">Ex: 1.00 para 1% ao mês</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="multa_percentual">Multa (%)</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="multa_percentual" 
                                                   name="multa_percentual" 
                                                   value="<?= esc($config['multa_percentual']) ?>" 
                                                   step="0.01" 
                                                   min="0" 
                                                   max="100" 
                                                   required>
                                            <small class="form-text text-muted">Ex: 2.00 para 2% de multa</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="multa_apos_dias">Aplicar Multa Após (dias)</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="multa_apos_dias" 
                                                   name="multa_apos_dias" 
                                                   value="<?= esc($config['multa_apos_dias']) ?>" 
                                                   min="0" 
                                                   required>
                                            <small class="form-text text-muted">Dias após o vencimento para aplicar multa</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="desconto_percentual">Desconto até o Vencimento (%)</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="desconto_percentual" 
                                                   name="desconto_percentual" 
                                                   value="<?= esc($config['desconto_percentual']) ?>" 
                                                   step="0.01" 
                                                   min="0" 
                                                   max="100" 
                                                   required>
                                            <small class="form-text text-muted">Desconto se pago antes do vencimento (0 = sem desconto)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-warning">
                                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Prazos e Restrições</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="nao_receber_apos_dias">Não Receber Após (dias)</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="nao_receber_apos_dias" 
                                                   name="nao_receber_apos_dias" 
                                                   value="<?= esc($config['nao_receber_apos_dias']) ?>" 
                                                   min="0" 
                                                   required>
                                            <small class="form-text text-muted">Dias após vencimento para não aceitar pagamento</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="protestar_apos_dias">Protestar Após (dias)</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="protestar_apos_dias" 
                                                   name="protestar_apos_dias" 
                                                   value="<?= esc($config['protestar_apos_dias']) ?>" 
                                                   min="0" 
                                                   required>
                                            <small class="form-text text-muted">0 = Não protestar</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="aceite">Aceite</label>
                                            <select class="form-control" id="aceite" name="aceite" required>
                                                <option value="N" <?= $config['aceite'] == 'N' ? 'selected' : '' ?>>NÃO</option>
                                                <option value="S" <?= $config['aceite'] == 'S' ? 'selected' : '' ?>>SIM</option>
                                            </select>
                                            <small class="form-text text-muted">Aceite do título pelo sacado</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="tipo_titulo">Tipo de Título</label>
                                            <select class="form-control" id="tipo_titulo" name="tipo_titulo" required>
                                                <option value="DM" <?= $config['tipo_titulo'] == 'DM' ? 'selected' : '' ?>>DM - Duplicata Mercantil</option>
                                                <option value="DS" <?= $config['tipo_titulo'] == 'DS' ? 'selected' : '' ?>>DS - Duplicata de Serviço</option>
                                                <option value="NP" <?= $config['tipo_titulo'] == 'NP' ? 'selected' : '' ?>>NP - Nota Promissória</option>
                                                <option value="RC" <?= $config['tipo_titulo'] == 'RC' ? 'selected' : '' ?>>RC - Recibo</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mensagens -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-info">
                                        <h5 class="mb-0"><i class="fas fa-comment"></i> Mensagens Personalizadas</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="mensagem_banco">Mensagem na Via do Banco (boletos e carnês)</label>
                                            <textarea class="form-control" 
                                                      id="mensagem_banco" 
                                                      name="mensagem_banco" 
                                                      rows="5"><?= esc($config['mensagem_banco']) ?></textarea>
                                            <small class="form-text text-muted">Instruções que aparecem no boleto (juros, multa, etc)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Salvar Configurações
                        </button>
                        <a href="<?= base_url('Admin/Boleto-Config/restaurar-padrao') ?>" 
                           class="btn btn-warning btn-lg"
                           onclick="return confirm('Deseja restaurar as configurações padrão? As configurações atuais serão perdidas.')">
                            <i class="fas fa-undo"></i> Restaurar Padrão
                        </a>
                        <a href="<?= base_url('Admin') ?>" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
