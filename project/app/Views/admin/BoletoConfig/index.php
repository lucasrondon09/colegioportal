<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Configurações de Boleto</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Configurações de Boleto</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/boleto-config/salvar') ?>" method="post">
            <?= csrf_field() ?>

            <div class="row">
                <!-- Coluna Esquerda -->
                <div class="col-md-6">
                    
                    <!-- Card de Juros e Multa -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Juros e Multa</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="juros_percentual">Juros ao Mês (%)</label>
                                <input type="number" step="0.01" class="form-control" id="juros_percentual" 
                                       name="juros_percentual" value="<?= esc($config['juros_percentual']) ?>" 
                                       placeholder="Ex: 1.00" required>
                                <small class="form-text text-muted">Percentual de juros ao mês (dias corridos)</small>
                            </div>

                            <div class="form-group">
                                <label for="multa_percentual">Multa (%)</label>
                                <input type="number" step="0.01" class="form-control" id="multa_percentual" 
                                       name="multa_percentual" value="<?= esc($config['multa_percentual']) ?>" 
                                       placeholder="Ex: 2.00" required>
                                <small class="form-text text-muted">Percentual de multa sobre o valor do título</small>
                            </div>

                            <div class="form-group">
                                <label for="multa_apos_dias">Aplicar Multa Após (dias)</label>
                                <input type="number" class="form-control" id="multa_apos_dias" 
                                       name="multa_apos_dias" value="<?= esc($config['multa_apos_dias']) ?>" 
                                       placeholder="Ex: 1" required>
                                <small class="form-text text-muted">Número de dias após o vencimento para aplicar multa</small>
                            </div>

                            <div class="form-group">
                                <label for="desconto_percentual">Desconto até o Vencimento (%)</label>
                                <input type="number" step="0.01" class="form-control" id="desconto_percentual" 
                                       name="desconto_percentual" value="<?= esc($config['desconto_percentual']) ?>" 
                                       placeholder="Ex: 0.00">
                                <small class="form-text text-muted">Desconto concedido se pago até o vencimento (opcional)</small>
                            </div>
                        </div>
                    </div>

                    <!-- Card de Instruções Bancárias -->
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Instruções Bancárias</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="nao_receber_apos_dias">Não Receber Após (dias)</label>
                                <input type="number" class="form-control" id="nao_receber_apos_dias" 
                                       name="nao_receber_apos_dias" value="<?= esc($config['nao_receber_apos_dias']) ?>" 
                                       placeholder="Ex: 29" required>
                                <small class="form-text text-muted">Número de dias após vencimento para não aceitar pagamento</small>
                            </div>

                            <div class="form-group">
                                <label for="protestar_apos_dias">Protestar Após (dias)</label>
                                <input type="number" class="form-control" id="protestar_apos_dias" 
                                       name="protestar_apos_dias" value="<?= esc($config['protestar_apos_dias']) ?>" 
                                       placeholder="Ex: 0" required>
                                <small class="form-text text-muted">Número de dias para protestar (0 = não protestar)</small>
                            </div>

                            <div class="form-group">
                                <label for="aceite">Aceite</label>
                                <select class="form-control" id="aceite" name="aceite" required>
                                    <option value="N" <?= $config['aceite'] == 'N' ? 'selected' : '' ?>>NÃO</option>
                                    <option value="S" <?= $config['aceite'] == 'S' ? 'selected' : '' ?>>SIM</option>
                                </select>
                                <small class="form-text text-muted">Indica se o título foi aceito pelo sacado</small>
                            </div>

                            <div class="form-group">
                                <label for="tipo_titulo">Tipo de Título</label>
                                <select class="form-control" id="tipo_titulo" name="tipo_titulo" required>
                                    <option value="DM" <?= $config['tipo_titulo'] == 'DM' ? 'selected' : '' ?>>DM - Duplicata Mercantil</option>
                                    <option value="DS" <?= $config['tipo_titulo'] == 'DS' ? 'selected' : '' ?>>DS - Duplicata de Serviço</option>
                                    <option value="NP" <?= $config['tipo_titulo'] == 'NP' ? 'selected' : '' ?>>NP - Nota Promissória</option>
                                    <option value="RC" <?= $config['tipo_titulo'] == 'RC' ? 'selected' : '' ?>>RC - Recibo</option>
                                    <option value="OU" <?= $config['tipo_titulo'] == 'OU' ? 'selected' : '' ?>>OU - Outros</option>
                                </select>
                                <small class="form-text text-muted">Tipo de documento do título</small>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Coluna Direita -->
                <div class="col-md-6">
                    
                    <!-- Card de Mensagens Personalizadas -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Mensagens Personalizadas</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="mensagem_banco">Mensagem Principal</label>
                                <textarea class="form-control" id="mensagem_banco" name="mensagem_banco" 
                                          rows="3" placeholder="Ex: DÚVIDAS ENTRE EM CONTATO COM O CEDENTE/FAVORECIDO"><?= esc($config['mensagem_banco']) ?></textarea>
                                <small class="form-text text-muted">Mensagem que aparecerá nas instruções do boleto</small>
                            </div>

                            <div class="form-group">
                                <label for="instrucao_1">Instrução Adicional 1</label>
                                <input type="text" class="form-control" id="instrucao_1" 
                                       name="instrucao_1" value="<?= esc($config['instrucao_1'] ?? '') ?>" 
                                       placeholder="Instrução customizada (opcional)">
                            </div>

                            <div class="form-group">
                                <label for="instrucao_2">Instrução Adicional 2</label>
                                <input type="text" class="form-control" id="instrucao_2" 
                                       name="instrucao_2" value="<?= esc($config['instrucao_2'] ?? '') ?>" 
                                       placeholder="Instrução customizada (opcional)">
                            </div>

                            <div class="form-group">
                                <label for="instrucao_3">Instrução Adicional 3</label>
                                <input type="text" class="form-control" id="instrucao_3" 
                                       name="instrucao_3" value="<?= esc($config['instrucao_3'] ?? '') ?>" 
                                       placeholder="Instrução customizada (opcional)">
                            </div>
                        </div>
                    </div>

                    <!-- Card de Informações -->
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-info-circle"></i> Informações Importantes</h3>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0">
                                <li>As configurações afetarão todos os novos boletos gerados</li>
                                <li>Boletos já emitidos não serão alterados</li>
                                <li>Juros são calculados por dia corrido (mês = 30 dias)</li>
                                <li>Multa é aplicada uma única vez após o período especificado</li>
                                <li>Instruções adicionais são opcionais e aparecem no boleto</li>
                                <li>Valores em percentual devem usar ponto como separador decimal</li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Salvar Configurações
                            </button>
                            <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <a href="<?= base_url('admin/boleto-config/testar') ?>" class="btn btn-info btn-lg float-right">
                                <i class="fas fa-file-invoice-dollar"></i> Gerar Boleto de Teste
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </form>

    </div>
</section>

<?= $this->endSection() ?>
