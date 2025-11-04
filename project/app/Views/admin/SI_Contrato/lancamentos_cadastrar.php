<?php

use App\Models\Admin\SI_DisciplinaModel;
use App\Models\Admin\SI_ParametroModel;
use App\Models\Admin\SI_ProfessorTurmaModel;
use App\Models\Admin\SI_TurmaModel;

$session = \Config\Services::session();
$validate = \Config\Services::validation();
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Cadastrar Lançamento</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('Admin/home') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('Admin/Contrato') ?>">Contrato</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('/Admin/Contrato/Lançamentos/' . $idContrato) ?>">Lançamentos</a></li>
            <li class="breadcrumb-item active">Cadastrar</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <!-- left column -->
        <div class="col-md-12">
          <!-- jquery validation -->
          <div class="card">
            <h3 class="card-title mt-3">
              <a href="javascript:history.back()" class="text-decoration-none text-dark">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z" />
                </svg>
              </a>
              Dados do Registro
            </h3>
            <!-- form start -->
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
              <div class="row mt-4 px-3">
                <div class="col-12">
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
            <div class="row">
              <div class="col-12">
                <span class="text-danger"><?= $validate->listErrors(); ?></span>
              </div>
            </div>
            <?= form_open(base_url('/Admin/Contrato/lancamentos/salvar/' . $idContrato)) ?>
            <fieldset>
              <?= csrf_field() ?>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-9">
                    <h3><?= $contrato->aluno_nome ?></h3>
                    <h6 class="text-muted"><?= $contrato->turma_nome ?> <?= getPeriodo($contrato->id_periodo) ?></h6>
                    <h6 class="text-muted">Responsável Financeiro: <?= $contrato->responsavel_nome ?></h6>
                  </div>
                </div>

                <div class="form-group row">
                  <div class="col-md-4">
                    <label for="tipo_lancamento">Tipo</label>
                    <select class="form-control" id="tipo_lancamento" name="tipo_lancamento">
                      <option value="">Selecione</option>
                      <option value="Matricula" <?= !empty($fields->tipo_lancamento) && $fields->tipo_lancamento === 'matricula' ? 'selected' : ''; ?>>Matricula</option>
                      <option value="Mensalidade" <?= !empty($fields->tipo_lancamento) && $fields->tipo_lancamento === 'mensalidade' ? 'selected' : ''; ?>>Mensalidade</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-md-3">
                    <label for="Parcelas">Parcelas</label>
                    <input type="number" class="form-control" id="parcelas" name="parcelas" value="<?= !empty($fields->parcelas) ? $fields->parcelas : ''; ?>">
                  </div>
                  <div class="col-md-3">
                    <label for="Data_emissao">Data Emissão</label>
                    <input type="date" class="form-control" id="data_emissao" name="data_emissao" value="<?= !empty($fields->data_emissao) ? $fields->data_emissao : date('Y-m-d') ?>">
                  </div>
                  <div class="col-md-3">
                    <label for="Dia_vencimento">Dia Vencimento</label>
                    <input type="date" class="form-control" id="data_vencimento" name="data_vencimento" value="<?= !empty($fields->dia_vencimento) ? $fields->dia_vencimento : ''; ?>">
                  </div>
                  <div class="col-md-3">
                    <label for="Valor_Total">Valor</label>
                    <input type="text" class="form-control" id="valor_total" name="valor_parcela" value="<?= !empty($fields->valor_total) ? $fields->valor_total : ''; ?>">
                  </div>
                </div>
                <div class="form-check mb-3">
                  <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                  <label class="form-check-label" for="defaultCheck1">
                    Registrar Boletos
                  </label>
                </div>
                <div class="form-group row">
                  <div class="col-md-12">
                  <label for="observacao">Descrição</label>
                  <textarea class="form-control" id="observacao" name="descricao" rows="3" placeholder="Descrição ou Observação sobre o lançamento"><?= !empty($fields->descricao) ? $fields->descricao : ''; ?></textarea>
                  </div>
                </div>
              </div>
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary" id="submit">
                    <i class="fas fa-save fa-fw"></i>Salvar
                  </button>
                  <a href="<?= base_url('/Admin/Contrato/lancamentos/cadastrar/'.$idContrato)?>" class="btn btn-secondary">
                    <i class="fas fa-times fa-fw"></i>
                    Cancelar
                  </a>
                </div>

            </fieldset>

            <?= form_close() ?>
          </div>
          <!-- /.card -->
        </div>
        <!--/.col (left) -->
      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>