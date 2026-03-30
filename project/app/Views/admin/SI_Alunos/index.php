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
          <h1>Alunos</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('Admin/home') ?>">Home</a></li>
            <?php if (!empty($idPai)): ?>
              <?php if (session()->sistema == 'sisaula'): ?>
                <li class="breadcrumb-item"><a href="<?= base_url('Admin/Pais') ?>">Pais</a></li>
              <?php endif; ?>
            <?php endif; ?>
            <li class="breadcrumb-item active">Alunos</li>
          </ol>

        </div>
      </div>
      <?php if (!empty($idPai)): ?>
        <?php if (session()->sistema == 'sisaula'): ?>
          <div class="row">
            <div class="col-12">
              <a href="<?= base_url('Admin/Alunos/cadastrar') . '/' . $idPai ?>" class="btn btn-primary">
                <i class="fas fa-plus fa-fw"></i>
                Cadastrar
              </a>
            </div>
          </div>
        <?php endif; ?>
      <?php endif; ?>

    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h3 class="card-title">
                <?php if (!empty($idPai)): ?>
                  <a href="<?= base_url('/Admin/Pais/detalhes') . '/' . $idPai ?>" class="text-decoration-none text-dark">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z" />
                    </svg>
                  </a>
                <?php endif; ?>
                Registros
              </h3>
              <?php if (session()->sistema == 'sisaula'): ?>
                <div class="ml-auto">
                  <?= form_open(base_url('Admin/Alunos'), ['method' => 'get']) ?>
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
                  <?= form_close() ?>
                </div>
              <?php endif; ?>
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
                    <th>Matricula</th>
                    <th>Nome</th>
                    <th>Req. Matricula</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($table as $tableItem): ?>
                    <tr>
                      <td><?= $tableItem->matricula ?></td>
                      <td><?= $tableItem->nome ?></td>
                      <td>
                      <?= form_open(base_url('Admin/Relatorios/requerimento-matricula')) ?>
                        <input type="text" value="<?= $tableItem->id?>" name="alunoId" hidden>
                        <div class="input-group">
                          <select class="custom-select" id="turmaId" name="turmaId">
                            <option selected disabled>----</option>  
                            <?php foreach($turma as $turmaItem):?>
                            <option value="<?= $turmaItem->id?>"><?= $turmaItem->nome.' - '.$turmaItem->ano?></option>
                            <?php endforeach;?>
                          </select>
                          <div class="input-group-append">
                            <button class="btn btn-outline-danger" type="submit"><i class="fas fa-file-pdf"></i></button>
                          </div>
                        </div>
                      <?= form_close() ?>  
                      </td>

                      <td class="text-center" width="">

                        <a href="<?= base_url('Admin/Alunos/detalhes') . '/' . $tableItem->id; ?>" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="top" title="Detalhes">
                          <i class="fas fa-eye"></i>
                        </a>
                        <?php if (session()->sistema == 'sisaula'): ?>

                          <!-- Modal -->
                          <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="staticBackdropLabel">Download de Documentos</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <div class="modal-body text-left">

                                  <form>
                                    <div class="form-group">
                                      <label for="exampleInputEmail1">Nome da Turma</label>
                                      <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                                    </div>
                                    <div class="form-group">
                                      <label for="exampleFormControlSelect1">Período</label>
                                      <select class="form-control" id="exampleFormControlSelect1">
                                        <option>Matutino</option>
                                        <option>Vespertino</option>
                                        <option>Diurno</option>
                                      </select>
                                    </div>
                                    <div class="form-group">
                                      <label for="exampleInputPassword1">Ano</label>
                                      <input type="password" class="form-control" id="exampleInputPassword1">
                                    </div>
                                    <div class="form-group form-check">
                                      <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                      <label class="form-check-label" for="exampleCheck1">Requerimento de Matricula</label>
                                    </div>
                                    <div class="form-group form-check">
                                      <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                      <label class="form-check-label" for="exampleCheck1">Contrato</label>
                                    </div>
                                    <div class="form-group form-check">
                                      <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                      <label class="form-check-label" for="exampleCheck1">Termo de Uso de Imagem</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Download</button>
                                  </form>

                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                </div>
                              </div>
                            </div>
                          </div>

                          <!--<a href="#" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#staticBackdrop" data-bs-toggle="tooltip" data-placement="top" title="Download de Documentos">
                            <i class="fas fa-download"></i>
                          </a>-->
                          <!-- <a href="<?= base_url('Admin/Relatorios/requerimento-matricula/' . $tableItem->id . '/' . $tableItem->id); ?>" class="btn btn-sm btn-outline-success" data-toggle="tooltip" data-placement="top" title="Requerimento de Matricula">
                          <i class="fas fa-download"></i>
                          </a> -->
                          <a href="<?= base_url('Admin/Alunos/editar') . '/' . $tableItem->id; ?>" class="btn btn-sm btn-outline-info" data-toggle="tooltip" data-placement="top" title="Editar">
                            <i class="fas fa-pen fa-fw"></i>
                          </a>

                          <a href="<?= base_url('Admin/Alunos/excluir') . '/' . $tableItem->id; ?>" class="btn btn-sm btn-outline-danger" data-toggle="tooltip" data-placement="top" title="Excluir" onClick="return deletar()">
                            <i class="fas fa-trash fa-fw"></i>
                          </a>
                        <?php endif; ?>
                      </td>

                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
              <?= $pager->links('default', 'default_page') ?>
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
  $(document).ready(function() {
    $('[data-bs-toggle="tooltip"]').tooltip();
  });


  function deletar() {

    return confirm('Tem certeza que deseja excluir o registro?');

  }
</script>