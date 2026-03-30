<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - ColégioPortal</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .boleto-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
            margin: 30px auto;
            max-width: 1200px;
        }
        
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #0d6efd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .info-box h5 {
            margin: 0 0 10px 0;
            color: #0d6efd;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        
        .info-value {
            color: #212529;
        }
        
        .btn-action {
            margin: 5px;
        }
        
        .boleto-html {
            margin-top: 30px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            overflow: hidden;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            .boleto-container {
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="row no-print">
            <div class="col-12">
                <div class="boleto-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-barcode"></i> Visualizar Boleto</h2>
                        <a href="<?= base_url('Admin/Lancamentos') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                    
                    <!-- Informações do Boleto -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <h5><i class="fas fa-file-invoice-dollar"></i> Dados do Boleto</h5>
                                <div class="info-row">
                                    <span class="info-label">Nosso Número:</span>
                                    <span class="info-value"><?= esc($boleto['nosso_numero']) ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Valor:</span>
                                    <span class="info-value">R$ <?= number_format($boleto['valor'], 2, ',', '.') ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Vencimento:</span>
                                    <span class="info-value"><?= date('d/m/Y', strtotime($boleto['vencimento'])) ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-box">
                                <h5><i class="fas fa-user"></i> Dados do Pagador</h5>
                                <div class="info-row">
                                    <span class="info-label">Nome:</span>
                                    <span class="info-value"><?= esc($boleto['pagador']['nome']) ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">CPF/CNPJ:</span>
                                    <span class="info-value"><?= esc($boleto['pagador']['cpf_cnpj']) ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Cidade:</span>
                                    <span class="info-value"><?= esc($boleto['pagador']['cidade']) ?>/<?= esc($boleto['pagador']['uf']) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Linha Digitável -->
                    <div class="alert alert-info">
                        <h6 class="mb-2"><i class="fas fa-barcode"></i> Linha Digitável:</h6>
                        <h5 class="mb-0 font-monospace"><?= esc($boleto['linha_digitavel']) ?></h5>
                    </div>
                    
                    <!-- Botões de Ação -->
                    <div class="text-center mb-4">
                        <a href="<?= base_url('Admin/Lancamentos/imprimirBoleto/' . $parcela->id) ?>" 
                           class="btn btn-primary btn-lg btn-action" target="_blank">
                            <i class="fas fa-print"></i> Imprimir Boleto
                        </a>
                        <a href="<?= base_url('Admin/Lancamentos/imprimirBoleto/' . $parcela->id) ?>" 
                           class="btn btn-success btn-lg btn-action" download>
                            <i class="fas fa-download"></i> Download PDF
                        </a>
                        <button onclick="window.print()" class="btn btn-secondary btn-lg btn-action">
                            <i class="fas fa-print"></i> Imprimir Página
                        </button>
                        <button onclick="copiarLinhaDigitavel()" class="btn btn-info btn-lg btn-action">
                            <i class="fas fa-copy"></i> Copiar Linha Digitável
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Boleto HTML -->
        <div class="row">
            <div class="col-12">
                <div class="boleto-html">
                    <?php
                    // Renderizar boleto em HTML
                    $boletoService = new \App\Libraries\BoletoService();
                    echo $boletoService->renderizarBoletoHTML($boleto['boleto_obj']);
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        function copiarLinhaDigitavel() {
            const linhaDigitavel = '<?= esc($boleto['linha_digitavel']) ?>';
            
            // Método alternativo que funciona em HTTP e HTTPS
            const textarea = document.createElement('textarea');
            textarea.value = linhaDigitavel;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            
            try {
                const sucesso = document.execCommand('copy');
                document.body.removeChild(textarea);
                
                if (sucesso) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Copiado!',
                        text: 'Linha digitável copiada para a área de transferência',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error('Falha ao copiar');
                }
            } catch (err) {
                document.body.removeChild(textarea);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Não foi possível copiar. Por favor, copie manualmente: ' + linhaDigitavel
                });
            }
        }
        
        // Exibir mensagens de sucesso/erro
        <?php if (session()->has('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: '<?= session('success') ?>',
                timer: 3000,
                showConfirmButton: false
            });
        <?php endif; ?>
        
        <?php if (session()->has('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: '<?= session('error') ?>'
            });
        <?php endif; ?>
    </script>
</body>
</html>
