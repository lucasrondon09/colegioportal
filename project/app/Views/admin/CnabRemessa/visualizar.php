<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1><i class="fas fa-eye"></i> Visualizar Remessa</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('Admin/home') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('Admin/Cnab-Remessa') ?>">Remessas</a></li>
            <li class="breadcrumb-item active">Visualizar</li>
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
                            <i class="fas fa-eye"></i> <?= $titulo ?>
                        </h3>
                        <div class="card-tools">
                            <a href="<?= base_url('Admin/Cnab-Remessa') ?>" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                            <a href="<?= base_url('Admin/Cnab-Remessa/Download/' . $remessa['id']) ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Informações da Remessa -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="200">Número da Remessa:</th>
                                        <td><?= str_pad($remessa['numero_remessa'], 6, '0', STR_PAD_LEFT) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Data de Geração:</th>
                                        <td><?= date('d/m/Y H:i:s', strtotime($remessa['data_geracao'])) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Data de Envio:</th>
                                        <td><?= $remessa['data_envio'] ? date('d/m/Y H:i:s', strtotime($remessa['data_envio'])) : 'Não enviada' ?></td>
                                    </tr>
                                    <tr>
                                        <th>Arquivo:</th>
                                        <td><?= $remessa['arquivo_nome'] ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="200">Total de Boletos:</th>
                                        <td><?= $remessa['total_registros'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Valor Total:</th>
                                        <td><strong>R$ <?= number_format($remessa['valor_total'], 2, ',', '.') ?></strong></td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
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
                                    </tr>
                                    <tr>
                                        <th>Observações:</th>
                                        <td><?= $remessa['observacoes'] ?? '-' ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Detalhes dos Boletos -->
                        <h4><i class="fas fa-list"></i> Boletos na Remessa</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="tabelaDetalhes">
                                <thead>
                                    <tr>
                                        <th>Seq.</th>
                                        <th>Nosso Número</th>
                                        <th>Aluno/Contrato</th>
                                        <th>Parcela</th>
                                        <th>Vencimento</th>
                                        <th>Valor</th>
                                        <th>Status Envio</th>
                                        <th>Rejeição</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($detalhes)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center">Nenhum detalhe encontrado</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($detalhes as $detalhe): ?>
                                            <tr>
                                                <td><?= $detalhe['sequencial_registro'] ?></td>
                                                <td><?= $detalhe['nosso_numero'] ?></td>
                                                <td>
                                                    <?= $detalhe['numero_parcela'] ?? '-' ?><br>
                                                    <small class="text-muted">Contrato #<?= $detalhe['id_contrato'] ?? '-' ?></small>
                                                </td>
                                                <td><?= $detalhe['numero_parcela'] ?? '-' ?></td>
                                                <td><?= date('d/m/Y', strtotime($detalhe['vencimento'])) ?></td>
                                                <td class="text-right">R$ <?= number_format($detalhe['valor'], 2, ',', '.') ?></td>
                                                <td>
                                                    <?php
                                                    $badges = [
                                                        'pendente' => 'warning',
                                                        'enviado' => 'primary',
                                                        'registrado' => 'success',
                                                        'rejeitado' => 'danger'
                                                    ];
                                                    $badge = $badges[$detalhe['status_envio']] ?? 'secondary';
                                                    ?>
                                                    <span class="badge badge-<?= $badge ?>"><?= ucfirst($detalhe['status_envio']) ?></span>
                                                </td>
                                                <td>
                                                    <?php if ($detalhe['codigo_rejeicao']): ?>
                                                        <span class="text-danger">
                                                            <?= $detalhe['codigo_rejeicao'] ?>: <?= $detalhe['mensagem_rejeicao'] ?>
                                                        </span>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
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
    $('#tabelaDetalhes').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
        },
        "order": [[0, "asc"]]
    });
});
</script>
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
