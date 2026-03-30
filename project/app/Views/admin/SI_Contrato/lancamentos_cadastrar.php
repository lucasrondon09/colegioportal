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

                <!-- Configurações de Boleto -->
                <hr class="my-4">
                <h5 class="mb-3"><i class="fas fa-cog"></i> Configurações do Boleto</h5>
                <p class="text-muted small">Os campos abaixo são pré-preenchidos com as configurações padrão, mas você pode personalizá-los para este lançamento.</p>

                <div class="form-group row">
                  <div class="col-md-3">
                    <label for="juros_percentual">Juros ao Mês (%)</label>
                    <input type="number" step="0.01" class="form-control" id="juros_percentual" name="juros_percentual" 
                           value="<?= !empty($configBoleto['juros_percentual']) ? $configBoleto['juros_percentual'] : '1.00' ?>">
                    <small class="form-text text-muted">Ex: 1.00 para 1%</small>
                  </div>
                  <div class="col-md-3">
                    <label for="multa_percentual">Multa (%)</label>
                    <input type="number" step="0.01" class="form-control" id="multa_percentual" name="multa_percentual" 
                           value="<?= !empty($configBoleto['multa_percentual']) ? $configBoleto['multa_percentual'] : '2.00' ?>">
                    <small class="form-text text-muted">Ex: 2.00 para 2%</small>
                  </div>
                  <div class="col-md-3">
                    <label for="multa_apos_dias">Multa Após (dias)</label>
                    <input type="number" class="form-control" id="multa_apos_dias" name="multa_apos_dias" 
                           value="<?= !empty($configBoleto['multa_apos_dias']) ? $configBoleto['multa_apos_dias'] : '1' ?>">
                    <small class="form-text text-muted">Dias após vencimento</small>
                  </div>
                  <div class="col-md-3">
                    <label for="desconto_percentual">Desconto (%)</label>
                    <input type="number" step="0.01" class="form-control" id="desconto_percentual" name="desconto_percentual" 
                           value="<?= !empty($configBoleto['desconto_percentual']) ? $configBoleto['desconto_percentual'] : '0.00' ?>">
                    <small class="form-text text-muted">Até o vencimento</small>
                  </div>
                </div>

                <div class="form-group row">
                  <div class="col-md-3">
                    <label for="nao_receber_apos_dias">Não Receber Após (dias)</label>
                    <input type="number" class="form-control" id="nao_receber_apos_dias" name="nao_receber_apos_dias" 
                           value="<?= !empty($configBoleto['nao_receber_apos_dias']) ? $configBoleto['nao_receber_apos_dias'] : '29' ?>">
                    <small class="form-text text-muted">Dias de atraso</small>
                  </div>
                  <div class="col-md-3">
                    <label for="protestar_apos_dias">Protestar Após (dias)</label>
                    <input type="number" class="form-control" id="protestar_apos_dias" name="protestar_apos_dias" 
                           value="<?= !empty($configBoleto['protestar_apos_dias']) ? $configBoleto['protestar_apos_dias'] : '0' ?>">
                    <small class="form-text text-muted">0 = não protestar</small>
                  </div>
                  <div class="col-md-3">
                    <label for="aceite">Aceite</label>
                    <select class="form-control" id="aceite" name="aceite">
                      <option value="S" <?= !empty($configBoleto['aceite']) && $configBoleto['aceite'] === 'S' ? 'selected' : '' ?>>SIM</option>
                      <option value="N" <?= empty($configBoleto['aceite']) || $configBoleto['aceite'] === 'N' ? 'selected' : '' ?>>NÃO</option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <label for="tipo_titulo">Tipo de Título</label>
                    <select class="form-control" id="tipo_titulo" name="tipo_titulo">
                      <option value="DM" <?= !empty($configBoleto['tipo_titulo']) && $configBoleto['tipo_titulo'] === 'DM' ? 'selected' : '' ?>>DM - Duplicata Mercantil</option>
                      <option value="DS" <?= !empty($configBoleto['tipo_titulo']) && $configBoleto['tipo_titulo'] === 'DS' ? 'selected' : '' ?>>DS - Duplicata de Serviço</option>
                      <option value="NP" <?= !empty($configBoleto['tipo_titulo']) && $configBoleto['tipo_titulo'] === 'NP' ? 'selected' : '' ?>>NP - Nota Promissória</option>
                      <option value="RC" <?= !empty($configBoleto['tipo_titulo']) && $configBoleto['tipo_titulo'] === 'RC' ? 'selected' : '' ?>>RC - Recibo</option>
                      <option value="OU" <?= !empty($configBoleto['tipo_titulo']) && $configBoleto['tipo_titulo'] === 'OU' ? 'selected' : '' ?>>OU - Outros</option>
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <div class="col-md-12">
                    <label for="mensagem_banco">Mensagem do Banco</label>
                    <textarea class="form-control" id="mensagem_banco" name="mensagem_banco" rows="3" 
                              placeholder="Instruções que aparecerão no boleto"><?= !empty($configBoleto['mensagem_banco']) ? $configBoleto['mensagem_banco'] : 'DÚVIDAS ENTRE EM CONTATO COM O CEDENTE/FAVORECIDO' ?></textarea>
                    <small class="form-text text-muted">Mensagem que aparecerá no boleto</small>
                  </div>
                </div>

                <hr class="my-4">

                <div class="form-check mb-3">
                  <input class="form-check-input" type="checkbox" name="registrar_boletos" value="1" id="registrar_boletos">
                  <label class="form-check-label" for="registrar_boletos">
                    Registrar Boletos (gera automaticamente os boletos para todas as parcelas)
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