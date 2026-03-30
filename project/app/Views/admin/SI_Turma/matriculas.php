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
          <h5><a href="<?= base_url('Admin/Turma/alunos/' . $idTurma) ?>" class="text-decoration-none text-dark">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"></path>
              </svg>
            </a>
            Gerar Novas Matriculas</h5>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('Admin/home') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('Admin/Turma') ?>">Turmas</a></li>
            <li class="breadcrumb-item active">Alunos</li>
          </ol>

        </div>
      </div>
      <div class="row">
        <div class="col-12 d-flex align-items-center">
          <h5>Turma: <?= $turmaAtual->nome . ' - ' . $turmaAtual->ano ?></h5>
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
              <div class="ml-auto">

                <div class="form-inline">
                  <div class="input-group">
                    <input class="form-control form-control-sidebar" type="search" placeholder="Procurar" name="search">
                    <div class="input-group-append">
                      <button class="btn btn-sidebar bg-secondary">
                        <i class="fas fa-search fa-fw"></i>
                      </button>
                    </div>
                  </div>
                </div>

              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <?= form_open(base_url('/Admin/Turma/Alunos/matriculas/send')) ?>
              <input type="text" hidden name="turmaAtual" value="<?= $turmaAtual->id?>">
              <div class="row mt-2">
                <div class="col-md-12">
                  
                  <label class="my-1 mr-2" for="inlineFormCustomSelectPref">Matricular:</label>
                  <select class="custom-select my-1 mr-sm-2" id="turmaId" name="turmaId">
                    <?php foreach ($turma as $turmaItem): ?>
                      <option value="<?= $turmaItem->id ?>"><?= $turmaItem->nome . ' - ' . $turmaItem->ano ?></option>
                    <?php endforeach; ?>
                  </select>

                  <div class="custom-control custom-checkbox my-1 mr-sm-2">
                    <input type="checkbox" class="custom-control-input" id="selecionar">
                    <label class="custom-control-label" for="selecionar">Selecionar Todos os Alunos</label>
                  </div>

                  <button type="submit" class="btn btn-primary my-2">Salvar</button>
                  
                </div>
              </div>

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
                    <th>Matricula</th>
                    <th>Nome</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($table as $tableItem): ?>
                    <tr>
                      <td><?= $tableItem->matricula ?></td>
                      <td><?= $tableItem->nome ?></td>
                      <td style="width: 5%;" class="text-center">
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" id="matricular" name="matricular[]" value="<?= $tableItem->id ?>">
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <?= form_close() ?>
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

  $(document).ready(function() {
    // Quando o checkbox "Selecionar Todos" for clicado
    $('#selecionar').click(function() {
      // Verifica se o "Selecionar Todos" está marcado
      var isChecked = $(this).prop('checked');

      // Define todos os checkboxes de alunos com o mesmo estado (marcado ou desmarcado)
      $('#registros .form-check-input').prop('checked', isChecked);
    });
  });
</script>