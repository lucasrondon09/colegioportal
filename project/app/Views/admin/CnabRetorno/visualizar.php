<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1><i class="fas fa-eye"></i> Visualizar Retorno</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('Admin/home') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('Admin/Cnab-Retorno') ?>">Retornos</a></li>
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
                        <a href="<?= base_url('Admin/Cnab-Retorno') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        <a href="<?= base_url('Admin/Cnab-Retorno/Download/' . $retorno['id']) ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Download
                        </a>
                        <a href="<?= base_url('Admin/Cnab-Retorno/Exportar/' . $retorno['id']) ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-file-csv"></i> Exportar CSV
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Informações do Retorno -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">ID do Retorno:</th>
                                    <td><?= $retorno['id'] ?></td>
                                </tr>
                                <tr>
                                    <th>Data de Processamento:</th>
                                    <td><?= date('d/m/Y H:i:s', strtotime($retorno['data_processamento'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Data do Arquivo:</th>
                                    <td><?= $retorno['data_arquivo'] ? date('d/m/Y', strtotime($retorno['data_arquivo'])) : '-' ?></td>
                                </tr>
                                <tr>
                                    <th>Arquivo:</th>
                                    <td><?= $retorno['arquivo_nome'] ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">Total de Registros:</th>
                                    <td><?= $retorno['total_registros'] ?></td>
                                </tr>
                                <tr>
                                    <th>Boletos Liquidados:</th>
                                    <td><span class="badge badge-success"><?= $retorno['total_liquidados'] ?></span></td>
                                </tr>
                                <tr>
                                    <th>Boletos Baixados:</th>
                                    <td><span class="badge badge-warning"><?= $retorno['total_baixados'] ?></span></td>
                                </tr>
                                <tr>
                                    <th>Boletos Rejeitados:</th>
                                    <td><span class="badge badge-danger"><?= $retorno['total_rejeitados'] ?></span></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-success">
                                <h5><i class="fas fa-dollar-sign"></i> Valor Total Liquidado</h5>
                                <h3 class="mb-0">R$ <?= number_format($retorno['valor_total_liquidado'], 2, ',', '.') ?></h3>
                            </div>
                        </div>
                    </div>

                    <!-- Resumo de Ocorrências -->
                    <?php if (!empty($ocorrencias)): ?>
                        <h4><i class="fas fa-chart-pie"></i> Resumo de Ocorrências</h4>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Descrição</th>
                                        <th>Quantidade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ocorrencias as $ocorrencia): ?>
                                        <tr>
                                            <td><?= $ocorrencia['codigo_ocorrencia'] ?></td>
                                            <td><?= $ocorrencia['descricao_ocorrencia'] ?></td>
                                            <td><strong><?= $ocorrencia['total'] ?></strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <!-- Detalhes das Ocorrências -->
                    <h4><i class="fas fa-list"></i> Detalhes das Ocorrências</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm" id="tabelaDetalhes">
                            <thead>
                                <tr>
                                    <th>Nosso Número</th>
                                    <th>Aluno/Parcela</th>
                                    <th>Ocorrência</th>
                                    <th>Data Ocorrência</th>
                                    <th>Data Crédito</th>
                                    <th>Valor Título</th>
                                    <th>Valor Pago</th>
                                    <th>Tarifa</th>
                                    <th>Juros</th>
                                    <th>Multa</th>
                                    <th>Desconto</th>
                                    <th>Processado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($detalhes)): ?>
                                    <tr>
                                        <td colspan="12" class="text-center">Nenhum detalhe encontrado</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($detalhes as $detalhe): ?>
                                        <tr>
                                            <td><?= $detalhe['nosso_numero'] ?></td>
                                            <td>
                                                <?php if ($detalhe['id_parcela']): ?>
                                                    Parcela #<?= $detalhe['numero_parcela'] ?? $detalhe['id_parcela'] ?><br>
                                                    <small class="text-muted">Contrato #<?= $detalhe['id_contrato'] ?? '-' ?></small>
                                                <?php else: ?>
                                                    <span class="text-danger">Não encontrada</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= $detalhe['codigo_ocorrencia'] ?></strong><br>
                                                <small><?= $detalhe['descricao_ocorrencia'] ?></small>
                                            </td>
                                            <td><?= $detalhe['data_ocorrencia'] ? date('d/m/Y', strtotime($detalhe['data_ocorrencia'])) : '-' ?></td>
                                            <td><?= $detalhe['data_credito'] ? date('d/m/Y', strtotime($detalhe['data_credito'])) : '-' ?></td>
                                            <td class="text-right">R$ <?= number_format($detalhe['valor_titulo'], 2, ',', '.') ?></td>
                                            <td class="text-right">
                                                <?php if ($detalhe['valor_pago'] > 0): ?>
                                                    <strong class="text-success">R$ <?= number_format($detalhe['valor_pago'], 2, ',', '.') ?></strong>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-right">
                                                <?= $detalhe['valor_tarifa'] > 0 ? 'R$ ' . number_format($detalhe['valor_tarifa'], 2, ',', '.') : '-' ?>
                                            </td>
                                            <td class="text-right">
                                                <?= $detalhe['valor_juros'] > 0 ? 'R$ ' . number_format($detalhe['valor_juros'], 2, ',', '.') : '-' ?>
                                            </td>
                                            <td class="text-right">
                                                <?= $detalhe['valor_multa'] > 0 ? 'R$ ' . number_format($detalhe['valor_multa'], 2, ',', '.') : '-' ?>
                                            </td>
                                            <td class="text-right">
                                                <?= $detalhe['valor_desconto'] > 0 ? 'R$ ' . number_format($detalhe['valor_desconto'], 2, ',', '.') : '-' ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($detalhe['processado']): ?>
                                                    <span class="badge badge-success"><i class="fas fa-check"></i> Sim</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> Não</span>
                                                <?php endif; ?>
                                                <?php if ($detalhe['erro_processamento']): ?>
                                                    <br><small class="text-danger"><?= $detalhe['erro_processamento'] ?></small>
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
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
$(document).ready(function() {
    $('#tabelaDetalhes').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
        },
        "pageLength": 50,
        "order": [[3, "desc"]]
    });
});
</script>
