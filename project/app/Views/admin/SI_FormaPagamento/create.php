<?php 
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
            <h1>Formas de Pagamento</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/home')?>">Home</a></li>
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/SI_Contrato')?>">Financeiro</a></li>
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/SI_FormaPagamento')?>">Formas de Pagamento</a></li>
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
                <a href="<?= base_url('Admin/SI_FormaPagamento')?>" class="text-decoration-none text-dark">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                  </svg>
                </a>               
                Nova Forma de Pagamento
              </h3>
              <!-- form start -->
              <?php

              if(!empty($session->getFlashdata())){
                $alert = $session->getFlashdata();
                
                if(key($alert) == 'success'){
                  
                  $classAlert = 'success';
                  $message    = $session->getFlashdata('success');
                }else{

                  $classAlert = 'danger';
                  $message    = $session->getFlashdata('error');
                }
              }

              if(isset($alert)):
              
              ?>    
                <div class="row mt-4 px-3">
                  <div class="col-12">
                    <div class="alert alert-<?= $classAlert;?> alert-dismissible fade show" role="alert">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                      </svg>
                      <?= $message;?>
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                  </div>
                </div>                    
              <?php endif;?>
              <div class="row">
                <div class="col-12">
                  <span class="text-danger"><?= $validate->listErrors(); ?></span>
                </div>
              </div>
              <?= form_open(base_url('Admin/SI_FormaPagamento/cadastrar')) ?>
              <?= csrf_field() ?>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="nome">Nome <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="nome" 
                               name="nome" 
                               value="<?= set_value('nome') ?>"
                               placeholder="Ex: Dinheiro, PIX, Boleto"
                               required>
                        <small class="form-text text-muted">Nome da forma de pagamento</small>
                      </div>
                    </div>
                    
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="taxa_percentual">Taxa (%)</label>
                        <input type="number" 
                               class="form-control" 
                               id="taxa_percentual" 
                               name="taxa_percentual" 
                               value="<?= set_value('taxa_percentual', '0.00') ?>"
                               step="0.01"
                               min="0"
                               max="100">
                        <small class="form-text text-muted">Taxa da operadora (ex: 2.5)</small>
                      </div>
                    </div>
                    
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="prazo_compensacao">Prazo Compensação (dias)</label>
                        <input type="number" 
                               class="form-control" 
                               id="prazo_compensacao" 
                               name="prazo_compensacao" 
                               value="<?= set_value('prazo_compensacao', '0') ?>"
                               min="0">
                        <small class="form-text text-muted">Dias até compensação</small>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-9">
                      <div class="form-group">
                        <label for="descricao">Descrição</label>
                        <textarea class="form-control" 
                                  id="descricao" 
                                  name="descricao" 
                                  rows="3"
                                  placeholder="Descrição detalhada da forma de pagamento"><?= set_value('descricao') ?></textarea>
                      </div>
                    </div>
                    
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="ordem_exibicao">Ordem de Exibição</label>
                        <input type="number" 
                               class="form-control" 
                               id="ordem_exibicao" 
                               name="ordem_exibicao" 
                               value="<?= set_value('ordem_exibicao', '0') ?>"
                               min="0">
                        <small class="form-text text-muted">Ordem nos formulários</small>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <div class="custom-control custom-switch">
                          <input type="checkbox" 
                                 class="custom-control-input" 
                                 id="ativo" 
                                 name="ativo" 
                                 value="1"
                                 <?= set_checkbox('ativo', '1', true); ?>>
                          <label class="custom-control-label" for="ativo">Forma de pagamento ativa</label>
                        </div>
                        <small class="form-text text-muted">Apenas formas ativas aparecem nos formulários de pagamento</small>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary" id="submit">
                    <i class="fas fa-save fa-fw"></i>
                    Salvar
                  </button>
                  <a href="<?= base_url('Admin/SI_FormaPagamento')?>" class="btn btn-secondary">
                    <i class="fas fa-times fa-fw"></i>
                    Cancelar
                  </a>
                </div>
                <?= form_close() ?>
            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>

