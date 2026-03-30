<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1><i class="fas fa-upload"></i> Processar Retorno CNAB</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('Admin/home') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('Admin/Cnab-Retorno') ?>">Retornos</a></li>
            <li class="breadcrumb-item active">Upload</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-upload"></i> Processar Retorno CNAB 240
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('Admin/Cnab-Retorno') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <!-- Instruções -->
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle"></i> Instruções</h5>
                                <ol>
                                    <li>Acesse o Internet Banking da Caixa Econômica Federal</li>
                                    <li>Navegue até a área de Cobrança / CNAB</li>
                                    <li>Faça o download do arquivo de retorno (.RET ou .TXT)</li>
                                    <li>Selecione o arquivo abaixo e clique em "Processar"</li>
                                </ol>
                                <p class="mb-0"><strong>Atenção:</strong> O sistema não processará arquivos duplicados.</p>
                            </div>

                            <!-- Formulário de Upload -->
                            <form id="formUploadRetorno" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="arquivo_retorno">
                                        <i class="fas fa-file"></i> Arquivo de Retorno
                                    </label>
                                    <div class="custom-file">
                                        <input type="file" 
                                               class="custom-file-input" 
                                               id="arquivo_retorno" 
                                               name="arquivo_retorno" 
                                               accept=".ret,.txt,.RET,.TXT"
                                               required>
                                        <label class="custom-file-label" for="arquivo_retorno">Selecione o arquivo...</label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Formatos aceitos: .RET, .TXT (CNAB 240)
                                    </small>
                                </div>

                                <div class="form-group">
                                    <div id="preview-info" class="alert alert-secondary" style="display: none;">
                                        <h6><i class="fas fa-file-alt"></i> Arquivo Selecionado:</h6>
                                        <p class="mb-0" id="preview-filename"></p>
                                        <p class="mb-0" id="preview-filesize"></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="button" class="btn btn-success btn-lg btn-block" onclick="processarRetorno()">
                                        <i class="fas fa-cog"></i> Processar Arquivo de Retorno
                                    </button>
                                </div>
                            </form>

                            <!-- Informações Adicionais -->
                            <div class="card mt-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-question-circle"></i> O que acontece ao processar?</h5>
                                </div>
                                <div class="card-body">
                                    <ul>
                                        <li><strong>Liquidações (Código 06):</strong> Parcelas serão marcadas como "Pagas" automaticamente</li>
                                        <li><strong>Baixas (Código 09):</strong> Parcelas serão marcadas como "Canceladas"</li>
                                        <li><strong>Rejeições:</strong> Serão registradas para análise</li>
                                        <li><strong>Outras ocorrências:</strong> Serão registradas no histórico</li>
                                    </ul>
                                    <p class="mb-0 text-muted">
                                        <i class="fas fa-shield-alt"></i> Todos os processamentos são registrados no log de auditoria.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
// Preview do arquivo selecionado
$('#arquivo_retorno').on('change', function() {
    const fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').html(fileName);
    
    if (this.files && this.files[0]) {
        const fileSize = (this.files[0].size / 1024).toFixed(2);
        $('#preview-filename').text('Nome: ' + fileName);
        $('#preview-filesize').text('Tamanho: ' + fileSize + ' KB');
        $('#preview-info').show();
    }
});

function processarRetorno() {
    const fileInput = document.getElementById('arquivo_retorno');
    
    if (!fileInput.files || fileInput.files.length === 0) {
        Swal.fire('Atenção!', 'Selecione um arquivo de retorno', 'warning');
        return;
    }

    const file = fileInput.files[0];
    const fileName = file.name.toLowerCase();
    
    // Validar extensão
    if (!fileName.endsWith('.ret') && !fileName.endsWith('.txt')) {
        Swal.fire('Erro!', 'Arquivo inválido. Use arquivos .RET ou .TXT', 'error');
        return;
    }

    Swal.fire({
        title: 'Confirmar Processamento?',
        text: 'O arquivo de retorno será processado e as parcelas serão atualizadas automaticamente',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, processar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData(document.getElementById('formUploadRetorno'));

            Swal.fire({
                title: 'Processando retorno...',
                text: 'Por favor, aguarde. Isso pode levar alguns segundos.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '<?= base_url('Admin/Cnab-Retorno/Processar') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.sucesso) {
                        Swal.fire({
                            title: 'Sucesso!',
                            html: `
                                <p>${response.mensagem}</p>
                                <hr>
                                <p><strong>Total de Registros:</strong> ${response.total_registros}</p>
                                <p><strong>Liquidados:</strong> <span class="badge badge-success">${response.total_liquidados}</span></p>
                                <p><strong>Baixados:</strong> <span class="badge badge-warning">${response.total_baixados}</span></p>
                                <p><strong>Rejeitados:</strong> <span class="badge badge-danger">${response.total_rejeitados}</span></p>
                                <p><strong>Valor Total Liquidado:</strong> R$ ${response.valor_total_liquidado.toFixed(2).replace('.', ',')}</p>
                            `,
                            icon: 'success',
                            confirmButtonText: 'Ver Retornos'
                        }).then(() => {
                            window.location.href = '<?= base_url('Admin/Cnab-Retorno') ?>';
                        });
                    } else {
                        Swal.fire('Erro!', response.mensagem, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Erro!', 'Erro ao processar arquivo: ' + error, 'error');
                }
            });
        }
    });
}
</script>
