<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-export"></i> Remessas CNAB 240
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('Admin/Cnab-Remessa/Nova') ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Nova Remessa
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
                                    <span class="info-box-text">Total de Remessas</span>
                                    <span class="info-box-number"><?= $estatisticas['total'] ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Geradas</span>
                                    <span class="info-box-number"><?= $estatisticas['geradas'] ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-primary">
                                <span class="info-box-icon"><i class="fas fa-paper-plane"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Enviadas</span>
                                    <span class="info-box-number"><?= $estatisticas['enviadas'] ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Processadas</span>
                                    <span class="info-box-number"><?= $estatisticas['processadas'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabela de Remessas -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="tabelaRemessas">
                            <thead>
                                <tr>
                                    <th>Nº Remessa</th>
                                    <th>Data Geração</th>
                                    <th>Data Envio</th>
                                    <th>Arquivo</th>
                                    <th>Boletos</th>
                                    <th>Valor Total</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($remessas)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Nenhuma remessa encontrada</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($remessas as $remessa): ?>
                                        <tr>
                                            <td><?= str_pad($remessa['numero_remessa'], 6, '0', STR_PAD_LEFT) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($remessa['data_geracao'])) ?></td>
                                            <td>
                                                <?= $remessa['data_envio'] ? date('d/m/Y H:i', strtotime($remessa['data_envio'])) : '-' ?>
                                            </td>
                                            <td><?= $remessa['arquivo_nome'] ?></td>
                                            <td class="text-center"><?= $remessa['total_registros'] ?></td>
                                            <td class="text-right">R$ <?= number_format($remessa['valor_total'], 2, ',', '.') ?></td>
                                            <td>
                                                <?php
                                                $badges = [
                                                    'gerado' => 'warning',
                                                    'enviado' => 'primary',
                                                    'processado' => 'success',
                                                    'erro' => 'danger'
                                                ];
                                                $badge = $badges[$remessa['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge badge-<?= $badge ?>"><?= ucfirst($remessa['status']) ?></span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?= base_url('Admin/Cnab-Remessa/Visualizar/' . $remessa['id']) ?>" 
                                                       class="btn btn-sm btn-info" title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= base_url('Admin/Cnab-Remessa/Download/' . $remessa['id']) ?>" 
                                                       class="btn btn-sm btn-success" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <?php if ($remessa['status'] === 'gerado'): ?>
                                                        <button onclick="marcarEnviada(<?= $remessa['id'] ?>)" 
                                                                class="btn btn-sm btn-primary" title="Marcar como Enviada">
                                                            <i class="fas fa-paper-plane"></i>
                                                        </button>
                                                        <button onclick="excluirRemessa(<?= $remessa['id'] ?>)" 
                                                                class="btn btn-sm btn-danger" title="Excluir">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
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

<script>
$(document).ready(function() {
    $('#tabelaRemessas').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
        },
        "order": [[0, "desc"]]
    });
});

function marcarEnviada(id) {
    if (!confirm('Confirma que esta remessa foi enviada ao banco?')) {
        return;
    }

    $.ajax({
        url: '<?= base_url('Admin/Cnab-Remessa/MarcarEnviada/') ?>' + id,
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.sucesso) {
                Swal.fire('Sucesso!', response.mensagem, 'success').then(() => {
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

function excluirRemessa(id) {
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
                url: '<?= base_url('Admin/Cnab-Remessa/Excluir/') ?>' + id,
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
