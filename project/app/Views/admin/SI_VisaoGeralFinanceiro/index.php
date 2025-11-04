<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-chart-line"></i> <?= $titulo ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('/Admin/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Visão Geral Financeira</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- DASHBOARD - Cards de Resumo -->
            <div class="row">
                <!-- Total a Receber -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>R$ <?= number_format($resumo['total_a_receber'], 2, ',', '.') ?></h3>
                            <p>Total a Receber</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Recebido -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>R$ <?= number_format($resumo['total_recebido'], 2, ',', '.') ?></h3>
                            <p>Total Recebido</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>

                <!-- Lançamentos Vencidos -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $resumo['qtd_vencidos'] ?></h3>
                            <p>Vencidos - R$ <?= number_format($resumo['valor_vencido'], 2, ',', '.') ?></p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>

                <!-- Lançamentos a Vencer (30 dias) -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $resumo['qtd_a_vencer'] ?></h3>
                            <p>A Vencer (30d) - R$ <?= number_format($resumo['valor_a_vencer'], 2, ',', '.') ?></p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas por Status -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-pie"></i> Distribuição por Status</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon"><i class="fas fa-folder-open"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Abertos</span>
                                            <span class="info-box-number"><?= $resumo['qtd_abertos'] ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="info-box bg-success">
                                        <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Pagos</span>
                                            <span class="info-box-number"><?= $resumo['qtd_pagos'] ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="info-box bg-warning">
                                        <span class="info-box-icon"><i class="fas fa-adjust"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Parcialmente Pagos</span>
                                            <span class="info-box-number"><?= $resumo['qtd_parcialmente_pagos'] ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="info-box bg-danger">
                                        <span class="info-box-icon"><i class="fas fa-times"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Atrasados</span>
                                            <span class="info-box-number"><?= $resumo['qtd_atrasados'] ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-filter"></i> Filtros Avançados</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="<?= base_url('/Admin/Visao-Geral-Financeiro') ?>">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select name="status" class="form-control">
                                                <option value="">Todos</option>
                                                <?php foreach ($status_opcoes as $key => $value): ?>
                                                    <option value="<?= $key ?>" <?= (isset($filtros['status']) && $filtros['status'] == $key) ? 'selected' : '' ?>>
                                                        <?= $value ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Tipo de Lançamento</label>
                                            <select name="tipo_lancamento" class="form-control">
                                                <option value="">Todos</option>
                                                <?php foreach ($tipos_lancamento as $tipo): ?>
                                                    <option value="<?= $tipo['id'] ?>" <?= (isset($filtros['tipo_lancamento']) && $filtros['tipo_lancamento'] == $tipo['id']) ? 'selected' : '' ?>>
                                                        <?= $tipo['nome'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Turma</label>
                                            <select name="id_turma" class="form-control">
                                                <option value="">Todas</option>
                                                <?php foreach ($turmas as $turma): ?>
                                                    <option value="<?= $turma->id ?>" <?= (isset($filtros['id_turma']) && $filtros['id_turma'] == $turma->id) ? 'selected' : '' ?>>
                                                        <?= $turma->nome ?> (<?= $turma->ano ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Buscar</label>
                                            <input type="text" name="search" class="form-control" placeholder="Nome, matrícula, CPF..." value="<?= $filtros['search'] ?? '' ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Data Início</label>
                                            <input type="date" name="data_inicio" class="form-control" value="<?= $filtros['data_vencimento_inicio'] ?? '' ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Data Fim</label>
                                            <input type="date" name="data_fim" class="form-control" value="<?= $filtros['data_vencimento_fim'] ?? '' ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>&nbsp;</label><br>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search"></i> Filtrar
                                            </button>
                                            <a href="<?= base_url('/Admin/Visao-Geral-Financeiro') ?>" class="btn btn-secondary">
                                                <i class="fas fa-eraser"></i> Limpar
                                            </a>
                                            <a href="<?= base_url('/Admin/Visao-Geral-Financeiro/exportar-csv') . '?' . http_build_query($filtros) ?>" class="btn btn-success">
                                                <i class="fas fa-file-excel"></i> Exportar CSV
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela de Lançamentos -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-list"></i> Lançamentos Financeiros</h3>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="registros" class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Contrato</th>
                                        <th>Aluno</th>
                                        <th>Turma</th>
                                        <th>Tipo</th>
                                        <th>Parcela</th>
                                        <th>Vencimento</th>
                                        <th>Valor</th>
                                        <th>Pago</th>
                                        <th>Saldo</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                    <tr class="filters">
                                        <th>ID</th>
                                        <th>Contrato</th>
                                        <th>Aluno</th>
                                        <th>Turma</th>
                                        <th>Tipo</th>
                                        <th>Parcela</th>
                                        <th>Vencimento</th>
                                        <th>Valor</th>
                                        <th>Pago</th>
                                        <th>Saldo</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($lancamentos)): ?>
                                        <tr>
                                            <td colspan="12" class="text-center">Nenhum lançamento encontrado</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($lancamentos as $lanc): 
                                            $total_pago = $lanc->total_pago ?? 0;
                                            $saldo = $lanc->valor_parcela - $total_pago;
                                            
                                            // Definir classe de status
                                            $status_class = '';
                                            $status_text = '';
                                            switch ($lanc->status) {
                                                case 1:
                                                    $status_class = 'badge-secondary';
                                                    $status_text = 'Aberto';
                                                    break;
                                                case 2:
                                                    $status_class = 'badge-success';
                                                    $status_text = 'Pago';
                                                    break;
                                                case 3:
                                                    $status_class = 'badge-warning';
                                                    $status_text = 'Pago Parcialmente';
                                                    break;
                                                case 4:
                                                    $status_class = 'badge-danger';
                                                    $status_text = 'Atrasado';
                                                    break;
                                            }
                                        ?>
                                            <tr>
                                                <td><?= $lanc->id ?></td>
                                                <td><?= $lanc->numero_contrato ?? '-' ?></td>
                                                <td>
                                                    <strong><?= $lanc->aluno_nome ?? '-' ?></strong><br>
                                                    <small class="text-muted">Mat: <?= $lanc->aluno_matricula ?? '-' ?></small>
                                                </td>
                                                <td><?= $lanc->turma_nome ?? '-' ?></td>
                                                <td><span class="badge badge-info"><?= $lanc->tipo_lancamento?? '-' ?></span></td>
                                                <td><?= $lanc->numero_parcela ?? '-' ?></td>
                                                <td><?= date('d/m/Y', strtotime($lanc->data_vencimento)) ?></td>
                                                <td>R$ <?= number_format($lanc->valor_parcela, 2, ',', '.') ?></td>
                                                <td class="text-success">R$ <?= number_format($total_pago, 2, ',', '.') ?></td>
                                                <td class="<?= $saldo > 0 ? 'text-danger' : 'text-success' ?>">
                                                    R$ <?= number_format($saldo, 2, ',', '.') ?>
                                                </td>
                                                <td><span class="badge <?= $status_class ?>"><?= $status_text ?></span></td>
                                                <td name="acoes">
                                                    <a href="<?= base_url('/Admin/Contrato/lancamentos/' . $lanc->id_contrato) ?>" 
                                                       class="btn btn-sm btn-info" 
                                                       title="Ver Detalhes">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
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

            <!-- Estatísticas por Tipo de Lançamento -->
            <?php if (!empty($estatisticas_tipo)): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-bar"></i> Estatísticas por Tipo de Lançamento</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Quantidade</th>
                                        <th>Valor Total</th>
                                        <th>Valor Recebido</th>
                                        <th>Saldo</th>
                                        <th>% Recebido</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($estatisticas_tipo as $stat): 
                                        $saldo_tipo = $stat['valor_total'] - $stat['valor_recebido'];
                                        $percentual = $stat['valor_total'] > 0 ? ($stat['valor_recebido'] / $stat['valor_total']) * 100 : 0;
                                    ?>
                                        <tr>
                                            <td><strong><?= $stat['tipo_nome'] ?></strong></td>
                                            <td><?= $stat['quantidade'] ?></td>
                                            <td>R$ <?= number_format($stat['valor_total'], 2, ',', '.') ?></td>
                                            <td class="text-success">R$ <?= number_format($stat['valor_recebido'], 2, ',', '.') ?></td>
                                            <td class="text-danger">R$ <?= number_format($saldo_tipo, 2, ',', '.') ?></td>
                                            <td>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar bg-success" style="width: <?= $percentual ?>%"></div>
                                                </div>
                                                <small><?= number_format($percentual, 1) ?>%</small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </section>
</div>

<img id="logo" src="<?= base_url('assets/admin/dist/img/logo_portal.png')?>" style="display:none" alt="logo">



<script>
$(document).ready(function(){
  // função utilitária: carrega imagem URL/elemento e retorna dataURL (Base64)
  async function imageToDataURL(imgEl) {
    try {
      const src = imgEl.src;
      const res = await fetch(src);
      const blob = await res.blob();
      return await new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onloadend = () => resolve(reader.result);
        reader.onerror = reject;
        reader.readAsDataURL(blob);
      });
    } catch (err) {
      console.warn('Falha ao converter imagem:', err);
      return '';
    }
  }

  (async function init() {
    // pega logo (se existir) em Base64
    var logoEl = document.getElementById('logo');
    var logoBase64 = '';
    if (logoEl && logoEl.src) {
      logoBase64 = await imageToDataURL(logoEl).catch(()=> '');
    }

    var table = $('#registros').DataTable({
      language: { url: "<?= base_url('assets/admin/plugins/datatables-bs5/pt-BR.json')?>" },
      layout: {
        topStart: {
          buttons: [
            {
              extend: 'copyHtml5',
              text: 'Copiar',
              exportOptions: {
                columns: ':visible',
                modifier: { search: 'applied' },
                format: {
                  header: function (data, columnIdx) {
                    var txt = $('<div>').html(data).text().trim();
                    // remove header cells that are actually filter selectors (ex: "Todos", "—", "Selecionar")
                    if (/^(Todos|—|Selecionar)/i.test(txt)) return '';
                    return txt;
                  }
                }
              }
            },
            {
              extend: 'excelHtml5',
              text: 'Excel',
              exportOptions: {
                columns: ':visible',
                modifier: { search: 'applied' },
                format: {
                  header: function (data, columnIdx) {
                    var txt = $('<div>').html(data).text().trim();
                    if (/^(Todos|—|Selecionar)/i.test(txt)) return '';
                    return txt;
                  }
                }
              },
              customizeData: function (data) {
                // garante que linhas de cabeçalho que contenham apenas filtros sejam removidas/limpas
                if (data && data.header && data.header.length) {
                  for (var i = 0; i < data.header.length; i++) {
                    var h = String(data.header[i]).trim();
                    if (/^(Todos|—|Selecionar|)$/i.test(h)) {
                      data.header[i] = '';
                    }
                  }
                }
              }
            },
            {
              extend: 'pdfHtml5',
              text: 'PDF',
              orientation: 'landscape',
              pageSize: 'A4',
              title: '',
              exportOptions: {
              // exclui a coluna "ações" e a coluna "ID" do PDF
              columns: function (idx, data, node) {
              var header = $('#registros thead tr').first().find('th').eq(idx).text().trim().toLowerCase();
              if (!header) return false; // exclui colunas de ações/controle com header vazio
              if (header === 'id') return false; // exclui explicitamente coluna ID
              if (header.indexOf('ação') !== -1 || header.indexOf('acao') !== -1 || header.indexOf('ações') !== -1 || header.indexOf('acoes') !== -1) return false;
              return true;
              },
              modifier: { search: 'applied' } // exporta apenas linhas filtradas
              },
              customize: function (doc) {
              // reduz margens para aproveitar mais largura da página
              doc.pageMargins = [20, 40, 20, 40];

              // diminui fonte padrão e cabeçalho da tabela para caber mais colunas
              doc.defaultStyle = doc.defaultStyle || {};
              doc.defaultStyle.fontSize = 8;
              doc.styles = doc.styles || {};
              doc.styles.tableHeader = doc.styles.tableHeader || {};
              doc.styles.tableHeader.fontSize = 9;
              doc.styles.tableHeader.bold = true;

              const tableNode = doc.content.find(c => c.table);
              if (tableNode && tableNode.table && tableNode.table.body.length > 1) {
              // Remove a segunda linha do cabeçalho (aquela com "Todos")
              tableNode.table.body.splice(1, 1);
              }

              // Garante que a primeira linha do table seja tratada como header repetido nas páginas
              if (tableNode && tableNode.table) {
              tableNode.table.headerRows = 1;
              }

              // 1) INSERIR CABEÇALHO COM LOGO
              if (logoBase64) {
              doc.content.splice(0,0, {
              columns: [
                { image: logoBase64, width: 60 },
                { text: 'Relatório Sintético - Lançamentos ', alignment: 'right', margin: [0,18,0,0], bold: true, fontSize: 10 }
              ],
              margin: [0, 0, 0, 8]
              });
              } else {
              doc.content.splice(0,0, { text: 'Relatório - ' + new Date().toLocaleString(), margin:[0,0,0,8], bold:true, fontSize: 10 });
              }

              // 2) REMOVER LINHA DE FILTROS/SELETORES DO CABEÇALHO (geralmente contém "Todos" ou "—")
              // procura o objeto table dentro de doc.content
              for (var i = 0; i < doc.content.length; i++) {
              var c = doc.content[i];
              if (c && c.table && c.table.body && c.table.body.length > 1) {
              // as primeiras linhas costumam ser os header rows
              // vamos filtrar linhas de header que contenham "Todos" ou "—" ou "Selecionar" etc.
              var newBody = [];
              // manter a primeira linha de header (títulos)
              newBody.push(c.table.body[0]);
              // copiar o restante das linhas (dados)
              for (var r = 1; r < c.table.body.length; r++) {
                var rowText = c.table.body[r].join(' ').toString();
                // se for linha de filtro (contém "Todos" / "—" / "Selecionar" / vazia), pule-a
                if (/Todos|—|Selecionar|^$|Todos$|Selecionar colunas/i.test(rowText)) {
                continue;
                }
                newBody.push(c.table.body[r]);
              }
              c.table.body = newBody;
              // torna as colunas flexíveis
              c.table.widths = Array(c.table.body[0].length).fill('*');

              // --- AQUI: aplica bordas na tabela PDF e paddings menores ---
              c.layout = {
                hLineWidth: function(i, node) { return 0.4; },
                vLineWidth: function(i, node) { return 0.4; },
                hLineColor: function(i, node) { return '#888888'; },
                vLineColor: function(i, node) { return '#888888'; },
                paddingLeft: function(i, node) { return 4; },
                paddingRight: function(i, node) { return 4; },
                paddingTop: function(i, node) { return 2; },
                paddingBottom: function(i, node) { return 2; }
              };

              // --- ALINHAR TODOS OS CAMPOS AO CENTRO E REDUZIR FONTES (INCLUINDO CABEÇALHO) ---
              for (var rr = 0; rr < c.table.body.length; rr++) {
                for (var cc = 0; cc < c.table.body[rr].length; cc++) {
                var cell = c.table.body[rr][cc];
                // células nulas
                if (cell === null || cell === undefined) {
                c.table.body[rr][cc] = { text: '', alignment: 'center', fontSize: 8 };
                continue;
                }
                // se for string ou número, transforma em objeto com alinhamento
                if (typeof cell === 'string' || typeof cell === 'number') {
                c.table.body[rr][cc] = {
                text: String(cell),
                alignment: 'center',
                fontSize: rr === 0 ? 9 : 8,
                bold: rr === 0 ? true : false
                };
                } else if (typeof cell === 'object') {
                // mantém propriedades existentes e força alinhamento; ajusta fontSize; cabeçalho em negrito
                cell.alignment = 'center';
                cell.fontSize = rr === 0 ? 9 : (cell.fontSize || 8);
                if (rr === 0) cell.bold = true;
                c.table.body[rr][cc] = cell;
                }
                }
              }

              }
              }

              // 3) CALCULAR SOMAS PARA "Valor", "Pago" E "Saldo"
              // localiza índices das colunas no cabeçalho do DOM (case-insensitive)
              var colIndexValor = null, colIndexPago = null, colIndexSaldo = null;
              $('#registros thead tr').first().find('th').each(function(idx){
              var txt = $(this).text().trim().toLowerCase();
              if (colIndexValor === null && txt.indexOf('valor') !== -1) { colIndexValor = idx; }
              if (colIndexPago === null && txt.indexOf('pago') !== -1) { colIndexPago = idx; }
              if (colIndexSaldo === null && txt.indexOf('saldo') !== -1) { colIndexSaldo = idx; }
              });

              if (colIndexValor === null && colIndexPago === null && colIndexSaldo === null) {
              console.warn('Colunas "Valor", "Pago" e "Saldo" não encontradas no thead; ajuste indices manualmente se necessário.');
              }

              // Função simples para parse de valores pt-BR e valores com R$
              function parseBR(v){
              if (v === null || v === undefined) return 0;
              var s = String(v).replace(/\s/g,'');
              // remove tags HTML caso existam
              s = s.replace(/<[^>]*>/g,'');
              // remover "R$" e outros símbolos
              s = s.replace(/[^\d,.-]/g,'');
              // tratar milhares e decimal pt-BR
              if (s.indexOf(',') > -1 && s.indexOf('.') > -1) {
                s = s.replace(/\./g,'').replace(',','.');
              } else if (s.indexOf(',') > -1) {
                s = s.replace(',','.');
              } else {
                s = s.replace(/\./g,'');
              }
              var n = parseFloat(s);
              return isNaN(n) ? 0 : n;
              }

              function sumColumnByIndex(colIdx) {
              if (colIdx === null) return 0;
              var data = table.column(colIdx, { search: 'applied' }).data().toArray();
              return data.reduce(function(acc, cur){
              return acc + parseBR(cur);
              }, 0);
              }

              var totalValor = sumColumnByIndex(colIndexValor);
              var totalPago = sumColumnByIndex(colIndexPago);
              var totalSaldo = sumColumnByIndex(colIndexSaldo);

              var totalValorStr = totalValor.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
              var totalPagoStr = totalPago.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
              var totalSaldoStr = totalSaldo.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

              // adiciona os três totais no documento (logo abaixo do header)
              var totalsColumns = [];
              if (colIndexValor !== null) {
              totalsColumns.push({ text: 'Total Valor: R$ ' + totalValorStr, alignment: 'right', margin: [0, 0, 10, 0], bold: true, fontSize: 9 });
              }
              if (colIndexPago !== null) {
              totalsColumns.push({ text: 'Total Pago: R$ ' + totalPagoStr, alignment: 'right', margin: [0, 0, 10, 0], bold: true, fontSize: 9 });
              }
              if (colIndexSaldo !== null) {
              totalsColumns.push({ text: 'Total Saldo: R$ ' + totalSaldoStr, alignment: 'right', margin: [0, 0, 0, 0], bold: true, fontSize: 9 });
              }

              if (totalsColumns.length > 0) {
              doc.content.splice(1,0, {
              columns: totalsColumns,
              margin: [0, 0, 0, 6]
              });
              }

              // 4) RODAPÉ COM NUMERAÇÃO DE PÁGINAS
              doc.footer = function(currentPage, pageCount) {
              return {
                columns: [
                { text: 'Gerado em: ' + new Date().toLocaleString(), alignment: 'left', margin: [20, 0, 0, 0], fontSize: 8 },
                { text: 'Página ' + currentPage.toString() + ' de ' + pageCount, alignment: 'right', margin: [0, 0, 20, 0], fontSize: 8 }
                ],
                fontSize: 8
              };
              };

              } // customize
            }
          ]
        }
      },
      orderCellsTop: true,
      initComplete: function () {
        var api = this.api();
        // mantém seus selects no cabeçalho (igual antes) — exceto coluna de ações
        api.columns().every(function () {
          var column = this;
          // detectar coluna "ações" verificando se alguma célula tem name="acoes"
          if ($(column.nodes()).filter('td[name="acoes"]').length > 0) {
        return; // pula adicionar filtro para a coluna de ações
          }

          var select = $('<select class="form-control form-control-sm"><option value="">Todos</option></select>')
        .appendTo($(column.header()).closest('thead').find('tr.filters th').eq(column.index()).empty())
        .on('change', function () {
          var val = $.fn.dataTable.util.escapeRegex($(this).val());
          column.search(val ? '^' + val + '$' : '', true, false).draw();
        });

          column.data().unique().sort().each(function (d) {
        if (d) select.append('<option value="' + d + '">' + d + '</option>');
          });
        });
      }
    });
  })(); // init async
});
</script>