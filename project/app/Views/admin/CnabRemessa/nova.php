<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1><i class="fas fa-plus"></i> Nova Remessa CNAB 240</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('Admin/home') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('Admin/Cnab-Remessa') ?>">Remessas</a></li>
            <li class="breadcrumb-item active">Nova</li>
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
                            <i class="fas fa-plus"></i> Nova Remessa CNAB 240
                        </h3>
                        <div class="card-tools">
                            <a href="<?= base_url('Admin/Cnab-Remessa') ?>" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($parcelas)): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Atenção!</strong> Não há parcelas com boletos gerados e pendentes de envio.
                            </div>
                            <a href="<?= base_url('Admin/Cnab-Remessa') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar para Lista
                            </a>
                        <?php else: ?>
                            <!-- Resumo -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="info-box bg-info">
                                        <span class="info-box-icon"><i class="fas fa-file-invoice"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total de Parcelas Disponíveis</span>
                                            <span class="info-box-number"><?= $total_parcelas ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box bg-success">
                                        <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Valor Total</span>
                                            <span class="info-box-number">R$ <?= number_format($valor_total, 2, ',', '.') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Filtros -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary" onclick="selecionarTodos()">
                                            <i class="fas fa-check-square"></i> Selecionar Todos
                                        </button>
                                        <button type="button" class="btn btn-secondary" onclick="deselecionarTodos()">
                                            <i class="fas fa-square"></i> Desmarcar Todos
                                        </button>
                                        <button type="button" class="btn btn-warning" onclick="selecionarVencidos()">
                                            <i class="fas fa-exclamation-triangle"></i> Apenas Vencidos
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabela de Parcelas -->
                            <form id="formGerarRemessa">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="tabelaParcelas">
                                        <thead>
                                            <tr>
                                                <th width="50">
                                                    <input type="checkbox" id="checkTodos" onchange="toggleTodos()">
                                                </th>
                                                <th>Contrato</th>
                                                <th>Parcela</th>
                                                <th>Vencimento</th>
                                                <th>Valor</th>
                                                <th>Status</th>
                                                <th>Nosso Número</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($parcelas as $parcela): ?>
                                                <?php
                                                $vencido = strtotime($parcela['data_vencimento']) < strtotime(date('Y-m-d'));
                                                $classVencido = $vencido ? 'table-danger' : '';
                                                ?>
                                                <tr class="<?= $classVencido ?>" data-vencido="<?= $vencido ? '1' : '0' ?>">
                                                    <td>
                                                        <input type="checkbox" name="parcelas[]" value="<?= $parcela['id'] ?>" class="check-parcela">
                                                    </td>
                                                    <td>Contrato #<?= $parcela['id_contrato'] ?></td>
                                                    <td><?= $parcela['numero_parcela'] ?></td>
                                                    <td>
                                                        <?= date('d/m/Y', strtotime($parcela['data_vencimento'])) ?>
                                                        <?php if ($vencido): ?>
                                                            <span class="badge badge-danger">Vencido</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-right">R$ <?= number_format($parcela['valor_parcela'], 2, ',', '.') ?></td>
                                                    <td>
                                                        <?php
                                                        $badges = [
                                                            'pendente' => 'warning',
                                                            'pago' => 'success',
                                                            'cancelado' => 'danger',
                                                            'vencido' => 'danger'
                                                        ];
                                                        $badge = $badges[$parcela['status']] ?? 'secondary';
                                                        ?>
                                                        <span class="badge badge-<?= $badge ?>"><?= ucfirst($parcela['status']) ?></span>
                                                    </td>
                                                    <td><?= $parcela['nosso_numero'] ?? '-' ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-success btn-lg" onclick="gerarRemessa()">
                                            <i class="fas fa-file-export"></i> Gerar Arquivo de Remessa
                                        </button>
                                        <a href="<?= base_url('Admin/Cnab-Remessa') ?>" class="btn btn-secondary btn-lg">
                                            <i class="fas fa-times"></i> Cancelar
                                        </a>
                                    </div>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </section>
</div>

<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('#tabelaParcelas').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
        },
        "pageLength": 50,
        "order": [[3, "asc"]]
    });
});

function toggleTodos() {
    const checkTodos = document.getElementById('checkTodos');
    const checks = document.querySelectorAll('.check-parcela');
    checks.forEach(check => {
        check.checked = checkTodos.checked;
    });
}

function selecionarTodos() {
    document.querySelectorAll('.check-parcela').forEach(check => {
        check.checked = true;
    });
    document.getElementById('checkTodos').checked = true;
}

function deselecionarTodos() {
    document.querySelectorAll('.check-parcela').forEach(check => {
        check.checked = false;
    });
    document.getElementById('checkTodos').checked = false;
}

function selecionarVencidos() {
    deselecionarTodos();
    document.querySelectorAll('tr[data-vencido="1"] .check-parcela').forEach(check => {
        check.checked = true;
    });
}

function gerarRemessa() {
    const checkboxes = document.querySelectorAll('.check-parcela:checked');
    
    if (checkboxes.length === 0) {
        Swal.fire('Atenção!', 'Selecione pelo menos uma parcela', 'warning');
        return;
    }

    Swal.fire({
        title: 'Confirmar Geração?',
        text: `Será gerado arquivo de remessa com ${checkboxes.length} boleto(s)`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, gerar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData(document.getElementById('formGerarRemessa'));

            Swal.fire({
                title: 'Gerando remessa...',
                text: 'Por favor, aguarde',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '<?= base_url('Admin/Cnab-Remessa/Gerar') ?>',
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
                                <p><strong>Número da Remessa:</strong> ${response.numero_remessa}</p>
                                <p><strong>Total de Boletos:</strong> ${response.total_registros}</p>
                                <p><strong>Valor Total:</strong> R$ ${response.valor_total.toFixed(2).replace('.', ',')}</p>
                            `,
                            icon: 'success',
                            confirmButtonText: 'Ver Remessas'
                        }).then(() => {
                            window.location.href = '<?= base_url('Admin/Cnab-Remessa') ?>';
                        });
                    } else {
                        Swal.fire('Erro!', response.mensagem, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Erro!', 'Erro ao processar requisição', 'error');
                }
            });
        }
    });
}
</script>
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
