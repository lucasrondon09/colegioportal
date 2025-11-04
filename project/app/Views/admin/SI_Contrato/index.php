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
          <h1>Contratos</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('Admin/home') ?>">Home</a></li>
            <li class="breadcrumb-item active">Contratos</li>
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
            <div class="card-header d-flex align-items-center">
              <h3 class="card-title">Registros</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <?php

              if (!empty($session->getFlashdata())) {
                $alert = $session->getFlashdata();

                if (key($alert) == 'success') {

                  $classAlert = 'success';
                  $message    = $session->getFlashdata('success');
                } else {

                  $classAlert = 'danger';
                  $message    = $session->getFlashdata('error');
                }
              }

              if (isset($alert)):

              ?>
                <div class="mt-4">
                  <div>
                    <div class="alert alert-<?= $classAlert; ?> alert-dismissible fade show" role="alert">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                      </svg>
                      <?= $message; ?>
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
              <table id="registros" class="table table-bordered table-hover mb-3">

                <thead>
                  <tr>
                    <th>Contrato</th>
                    <th>Aluno</th>
                    <th>Respons. Financeiro</th>
                    <th>Turma</th>
                    <th>Ano</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Ações</th>
                  </tr>
                  <tr class="filters">
                    <th>Contrato</th>
                    <th>Aluno</th>
                    <th>Respons. Financeiro</th>
                    <th>Turma</th>
                    <th>Ano</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th></th>
                </thead>
                <tbody>
                  <?php foreach ($table as $tableItem): 
                  // Define badge class based on status
                  $badgeClass = '';
                  switch($tableItem->status) {
                    case 1: $badgeClass = 'badge-info'; break;      // Aberto - blue
                    case 2: $badgeClass = 'badge-success'; break;   // Ativo - green  
                    case 3: $badgeClass = 'badge-secondary'; break; // Concluído - gray
                    case 4: $badgeClass = 'badge-danger'; break;    // Cancelado - red
                    case 5: $badgeClass = 'badge-warning'; break;   // Transferido - yellow
                    case 6: $badgeClass = 'badge-light'; break;     // Suspenso - light gray
                    case 7: $badgeClass = 'badge-danger'; break;    // Inadimplente - red
                    case 8: $badgeClass = 'badge-dark'; break;      // Expirado - dark
                  }
                  ?>
                  <tr>
                    <td><?= $tableItem->numero_contrato ?></td>
                    <td><?= $tableItem->aluno_nome ?></td>
                    <td><?= $tableItem->responsavel_nome ?></td>
                    <td><?= $tableItem->turma_nome ?></td>
                    <td><?= $tableItem->turma_ano ?></td>
                    <td>R$ <?= monetarioExibir($tableItem->valor_total) ?></td>
                    <td><span class="badge <?= $badgeClass ?>"><?= statusContrato($tableItem->status) ?></span></td>
                    <td class="text-center" name="acoes">
                      <a href="<?= base_url('Admin/Contrato/editar/'.$tableItem->id_turma.'/'.$tableItem->id_aluno); ?>" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="tooltip" data-placement="top" title="Editar">
                        <i class="fas fa-edit"></i>
                      </a>
                    <a href="<?= base_url('Admin/Contrato/lancamentos/'.$tableItem->id); ?>" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="top" title="Visualizar">
                    <i class="fas fa-arrow-right"></i>
                    </a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
  function deletar() {

    return confirm('Tem certeza que deseja excluir o registro?');

  }
  
</script>

<!-- Coloque sua tag de logo (pode estar oculta com style="display:none") -->
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
      language: { url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json" },
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
              // exclui a coluna "ações" (coluna com header vazio ou contendo "acao/ações")
              columns: function (idx, data, node) {
              var header = $('#registros thead tr').first().find('th').eq(idx).text().trim().toLowerCase();
              if (!header) return false; // exclui colunas de ações/controle com header vazio
              if (header.indexOf('ação') !== -1 || header.indexOf('acao') !== -1 || header.indexOf('ações') !== -1 || header.indexOf('acoes') !== -1) return false;
              return true;
              },
              modifier: { search: 'applied' } // exporta apenas linhas filtradas
              },
              customize: function (doc) {
              // Ajustes de margem para espaço de cabeçalho/rodapé
              doc.pageMargins = [40, 60, 40, 60];

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
                { image: logoBase64, width: 80 },
                { text: 'Relatório Sintético - Contratos ', alignment: 'right', margin: [0,20,0,0], bold: true }
              ],
              margin: [0, 0, 0, 10]
              });
              } else {
              doc.content.splice(0,0, { text: 'Relatório - ' + new Date().toLocaleString(), margin:[0,0,0,10], bold:true });
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

              // --- AQUI: aplica bordas na tabela PDF ---
              c.layout = {
                hLineWidth: function(i, node) {
                return 0.5; // espessura das linhas horizontais
                },
                vLineWidth: function(i, node) {
                return 0.5; // espessura das linhas verticais
                },
                hLineColor: function(i, node) {
                return '#888888'; // cor das linhas horizontais
                },
                vLineColor: function(i, node) {
                return '#888888'; // cor das linhas verticais
                },
                paddingLeft: function(i, node) { return 6; },
                paddingRight: function(i, node) { return 6; },
                paddingTop: function(i, node) { return 4; },
                paddingBottom: function(i, node) { return 4; }
              };

              // --- ALINHAR TODOS OS CAMPOS AO CENTRO (INCLUINDO CABEÇALHO) ---
              for (var rr = 0; rr < c.table.body.length; rr++) {
                for (var cc = 0; cc < c.table.body[rr].length; cc++) {
                var cell = c.table.body[rr][cc];
                // células nulas
                if (cell === null || cell === undefined) {
                c.table.body[rr][cc] = { text: '', alignment: 'center' };
                continue;
                }
                // se for string ou número, transforma em objeto com alinhamento
                if (typeof cell === 'string' || typeof cell === 'number') {
                c.table.body[rr][cc] = {
                text: String(cell),
                alignment: 'center',
                bold: rr === 0 ? true : false // deixa o cabeçalho em negrito
                };
                } else if (typeof cell === 'object') {
                // mantém propriedades existentes e força alinhamento; cabeçalho em negrito
                cell.alignment = 'center';
                if (rr === 0) cell.bold = true;
                c.table.body[rr][cc] = cell;
                }
                }
              }

              }
              }

              // 3) CALCULAR SOMA APENAS DA COLUNA "Valor"
              // localiza índice da coluna no cabeçalho do DOM (case-insensitive)
              var colIndex = null;
              $('#registros thead tr').first().find('th').each(function(idx){
              var txt = $(this).text().trim().toLowerCase();
              if (txt.indexOf('valor') !== -1) {
              colIndex = idx;
              return false; // break
              }
              });
              if (colIndex === null) {
              // fallback manual (ajuste se necessário)
              console.warn('Coluna "Valor" não encontrada no thead; ajuste index manualmente.');
              // colIndex = 3;
              }

              if (colIndex !== null) {
              // pega os dados já filtrados via DataTables (search: 'applied')
              var data = table.column(colIndex, { search: 'applied' }).data().toArray();

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

              var total = data.reduce(function(acc, cur){
              return acc + parseBR(cur);
              }, 0);

              var totalStr = total.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

              // adiciona total no documento (logo abaixo do header)
              doc.content.splice(1,0, {
              text: 'Total: R$ ' + totalStr,
              alignment: 'right',
              margin: [0, 0, 0, 8],
              bold: true
              });
              }

              // 4) RODAPÉ COM NUMERAÇÃO DE PÁGINAS
              doc.footer = function(currentPage, pageCount) {
              return {
                columns: [
                { text: 'Gerado em: ' + new Date().toLocaleString(), alignment: 'left', margin: [40, 0, 0, 0] },
                { text: 'Página ' + currentPage.toString() + ' de ' + pageCount, alignment: 'right', margin: [0, 0, 40, 0] }
                ],
                fontSize: 9
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
