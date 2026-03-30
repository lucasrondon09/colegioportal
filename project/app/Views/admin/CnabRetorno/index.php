<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1><i class="fas fa-file-import"></i> Retornos CNAB 240</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('Admin/home') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('Admin/Cnab-Retorno') ?>">CNAB 240</a></li>
            <li class="breadcrumb-item active">Retornos</li>
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
                        <i class="fas fa-file-import"></i> Retornos CNAB 240
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('Admin/Cnab-Retorno/Upload') ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-upload"></i> Processar Retorno
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Estatísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-file-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total de Retornos</span>
                                    <span class="info-box-number"><?= $estatisticas['total'] ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Processados</span>
                                    <span class="info-box-number"><?= $estatisticas['processados'] ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Boletos Liquidados</span>
                                    <span class="info-box-number"><?= $estatisticas['total_liquidados'] ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-primary">
                                <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Valor Liquidado</span>
                                    <span class="info-box-number">R$ <?= number_format($estatisticas['valor_liquidado'], 2, ',', '.') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabela de Retornos -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="tabelaRetornos">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Data Processamento</th>
                                    <th>Arquivo</th>
                                    <th>Registros</th>
                                    <th>Liquidados</th>
                                    <th>Baixados</th>
                                    <th>Rejeitados</th>
                                    <th>Valor Liquidado</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($retornos)): ?>
                                    <tr>
                                        <td colspan="10" class="text-center">Nenhum retorno encontrado</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($retornos as $retorno): ?>
                                        <tr>
                                            <td><?= $retorno['id'] ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($retorno['data_processamento'])) ?></td>
                                            <td><?= $retorno['arquivo_nome'] ?></td>
                                            <td class="text-center"><?= $retorno['total_registros'] ?></td>
                                            <td class="text-center">
                                                <span class="badge badge-success"><?= $retorno['total_liquidados'] ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-warning"><?= $retorno['total_baixados'] ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-danger"><?= $retorno['total_rejeitados'] ?></span>
                                            </td>
                                            <td class="text-right">R$ <?= number_format($retorno['valor_total_liquidado'], 2, ',', '.') ?></td>
                                            <td>
                                                <?php
                                                $badges = [
                                                    'processando' => 'warning',
                                                    'processado' => 'success',
                                                    'erro' => 'danger',
                                                    'reprocessado' => 'info'
                                                ];
                                                $badge = $badges[$retorno['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge badge-<?= $badge ?>"><?= ucfirst($retorno['status']) ?></span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?= base_url('Admin/Cnab-Retorno/Visualizar/' . $retorno['id']) ?>" 
                                                       class="btn btn-sm btn-info" title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= base_url('Admin/Cnab-Retorno/Download/' . $retorno['id']) ?>" 
                                                       class="btn btn-sm btn-success" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <a href="<?= base_url('Admin/Cnab-Retorno/Exportar/' . $retorno['id']) ?>" 
                                                       class="btn btn-sm btn-primary" title="Exportar CSV">
                                                        <i class="fas fa-file-csv"></i>
                                                    </a>
                                                    <button onclick="excluirRetorno(<?= $retorno['id'] ?>)" 
                                                            class="btn btn-sm btn-danger" title="Excluir">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
  </section>
</div>

<script>
$(document).ready(function() {
    $('#tabelaRetornos').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
        },
        "order": [[0, "desc"]]
    });
});

function excluirRetorno(id) {
    Swal.fire({
        title: 'Tem certeza?',
        text: 'Esta ação não pode ser desfeita!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('Admin/Cnab-Retorno/Excluir/') ?>' + id,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.sucesso) {
                        Swal.fire('Excluído!', response.mensagem, 'success').then(() => {
                            location.reload();
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
