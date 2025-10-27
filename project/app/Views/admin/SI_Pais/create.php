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
            <h1>Pais</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/home')?>">Home</a></li>
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/Pais')?>">Pais</a></li>
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
                <a href="<?= base_url('Admin/Pais')?>" class="text-decoration-none text-dark">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                  </svg>
                </a>               
                Novo Registro
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
              <?= form_open(base_url('Admin/Pais/cadastrar')) ?>
              <?= csrf_field() ?>
              <div class="card-body">
                  <h5 class="bg-secondary p-3">Dados do Pai</h5>
                  <div class="form-group">
                    <label>Nome</label>
                    <input type="text" class="form-control" name="nome_pai" value="<?= set_value('nome_pai');?>">
                  </div>
                  <div class="form-group">
                    <label>Senha</label>
                    <input type="text" class="form-control" name="senha_pai" value="<?= set_value('senha_pai');?>">
                  </div>
                  <div class="form-group">
                    <label>CPF</label>
                    <input type="text" class="form-control" name="cpf_pai" value="<?= set_value('cpf_pai');?>">
                  </div>
                  <div class="form-group">
                    <label>RG</label>
                    <input type="text" class="form-control" name="rg_pai" value="<?= set_value('rg_pai');?>">
                  </div>
                  <div class="form-group">
                    <label>Nascimento Pai</label>
                    <input type="date" class="form-control" name="nasc_pai" value="<?= set_value('nasc_pai');?>">
                  </div>
                  <div class="form-group">
                    <label>Trabalho Pai</label>
                    <input type="text" class="form-control" name="trabalho_pai" value="<?= set_value('trabalho_pai');?>">
                  </div>
                  <div class="form-group">
                    <label>Profissão Pai</label>
                    <input type="text" class="form-control" name="profissao_pai" value="<?= set_value('profissao_pai');?>">
                  </div>
                  <div class="form-group">
                    <label>Naturalidade Pai</label>
                    <input type="text" class="form-control" name="nat_pai" value="<?= set_value('nat_pai');?>">
                  </div>
                  <div class="form-group">
                    <label>Nacionaliade Pai</label>
                    <input type="text" class="form-control" name="rm_pai_nacionalidade" value="<?= set_value('rm_pai_nacionalidade');?>">
                  </div>
                  <div class="form-group">
                    <label>Celular Pai</label>
                    <input type="text" class="form-control" name="cel_pai" value="<?= set_value('cel_pai');?>">
                  </div>
                  <div class="form-group">
                    <label>Fone Pai</label>
                    <input type="text" class="form-control" name="fone_pai" value="<?= set_value('fone_pai');?>">
                  </div>
                  <div class="form-group">
                    <label>Email Pai</label>
                    <input type="text" class="form-control" name="email_pai" value="<?= set_value('email_pai');?>">
                  </div>
                  <div class="form-group">
                    <label>Bairro Pai</label>
                    <input type="text" class="form-control" name="bairro_pai" value="<?= set_value('bairro_pai');?>">
                  </div>
                  <div class="form-group">
                    <label>Cidade Pai</label>
                    <input type="text" class="form-control" name="cid_pai" value="<?= set_value('cid_pai');?>">
                  </div>
                  <div class="form-group">
                    <label>Endereço Pai</label>
                    <input type="text" class="form-control" name="end_pai" value="<?= set_value('end_pai');?>">
                  </div>
                  <div class="form-group">
                    <label>UF Pai</label>
                    <input type="text" class="form-control" name="uf_pai" value="<?= set_value('uf_pai');?>">
                  </div>
                  <div class="form-group">
                    <label>Estado Civil Pai</label>
                    <input type="text" class="form-control" name="rm_pai_estado_civil" value="<?= set_value('rm_pai_estado_civil');?>">
                  </div>
                  <h5 class="bg-secondary p-3 mt-5">Dados da Mãe</h5>
                  <div class="form-group">
                    <label>Nome da Mãe</label>
                    <input type="text" class="form-control" name="nome_mae" value="<?= set_value('nome_mae');?>">
                  </div>
                  <div class="form-group">
                    <label>CPF da Mãe</label>
                    <input type="text" class="form-control" name="cpf_mae" value="<?= set_value('cpf_mae');?>">
                  </div>
                  <div class="form-group">
                    <label>RG da Mãe</label>
                    <input type="text" class="form-control" name="rg_mae" value="<?= set_value('rg_mae');?>">
                  </div>
                  <div class="form-group">
                    <label>Nascimento Mãe</label>
                    <input type="date" class="form-control" name="nasc_mae" value="<?= set_value('nasc_mae');?>">
                  </div>
                  <div class="form-group">
                    <label>Trabalho da Mãe</label>
                    <input type="text" class="form-control" name="trabalho_mae" value="<?= set_value('trabalho_mae');?>">
                  </div>
                  <div class="form-group">
                    <label>Profissão da Mãe</label>
                    <input type="text" class="form-control" name="profissao_mae" value="<?= set_value('profissao_mae');?>">
                  </div>
                  <div class="form-group">
                    <label>Naturalidade da Mãe</label>
                    <input type="text" class="form-control" name="nat_mae" value="<?= set_value('nat_mae');?>">
                  </div>
                  <div class="form-group">
                    <label>Nacionaliade Mãe</label>
                    <input type="text" class="form-control" name="rm_mae_nacionalidade" value="<?= set_value('rm_mae_nacionalidade');?>">
                  </div>
                  <div class="form-group">
                    <label>Celular da Mãe</label>
                    <input type="text" class="form-control" name="cel_mae" value="<?= set_value('cel_mae');?>">
                  </div>
                  <div class="form-group">
                    <label>Telefone da Mãe</label>
                    <input type="text" class="form-control" name="fone_mae" value="<?= set_value('fone_mae');?>">
                  </div>
                  <div class="form-group">
                    <label>Email da Mãe</label>
                    <input type="text" class="form-control" name="email_mae" value="<?= set_value('email_mae');?>">
                  </div>
                  <div class="form-group">
                    <label>Endereço da Mãe</label>
                    <input type="text" class="form-control" name="end_mae" value="<?= set_value('end_mae');?>">
                  </div>
                  <div class="form-group">
                    <label>Bairro da Mãe</label>
                    <input type="text" class="form-control" name="bairro_mae" value="<?= set_value('bairro_mae');?>">
                  </div>
                  <div class="form-group">
                    <label>Cidade da Mãe</label>
                    <input type="text" class="form-control" name="cid_mae" value="<?= set_value('cid_mae');?>">
                  </div>
                  <div class="form-group">
                    <label>UF da Mãe</label>
                    <input type="text" class="form-control" name="uf_mae" value="<?= set_value('uf_mae');?>">
                  </div>
                  <div class="form-group">
                    <label>Estado Civil Mãe</label>
                    <input type="text" class="form-control" name="rm_mae_estado_civil" value="<?= set_value('rm_mae_estado_civil');?>">
                  </div>
                  <h5 class="bg-secondary p-3 mt-5">Dados do Responsável</h5>
                  <div class="form-group">
                    <label>Nome do Responsável</label>
                    <input type="text" class="form-control" name="nome_resp" value="<?= set_value('nome_resp');?>">
                  </div>
                  <div class="form-group">
                    <label>RG do Responsável</label>
                    <input type="text" class="form-control" name="rg_resp" value="<?= set_value('rg_resp');?>">
                  </div>
                  <div class="form-group">
                    <label>CPF do Responsável</label>
                    <input type="text" class="form-control" name="cpf_resp" value="<?= set_value('cpf_resp');?>">
                  </div>
                  <div class="form-group">
                    <label>Nascimento do Responsável</label>
                    <input type="date" class="form-control" name="nasc_resp" value="<?= set_value('nasc_resp');?>">
                  </div>
                  <div class="form-group">
                    <label>Celular do Responsável</label>
                    <input type="text" class="form-control" name="fone_resp" value="<?= set_value('fone_resp');?>">
                  </div>
                  <div class="form-group">
                    <label>Telefone do Responsável</label>
                    <input type="text" class="form-control" name="tel_resp" value="<?= set_value('tel_resp');?>">
                  </div>
                  <div class="form-group">
                    <label>Email do Responsável</label>
                    <input type="text" class="form-control" name="email_resp" value="<?= set_value('email_resp');?>">
                  </div>
                  <div class="form-group">
                    <label>Endereço do Responsável</label>
                    <input type="text" class="form-control" name="end_resp" value="<?= set_value('end_resp');?>">
                  </div>
                  <div class="form-group">
                    <label>Bairro do Responsável</label>
                    <input type="text" class="form-control" name="bairro_resp" value="<?= set_value('bairro_resp');?>">
                  </div>
                  <div class="form-group">
                    <label>Cidade do Responsável</label>
                    <input type="text" class="form-control" name="cid_resp" value="<?= set_value('cid_resp');?>">
                  </div>
                  <div class="form-group">
                    <label>UF do Responsável</label>
                    <input type="text" class="form-control" name="uf_resp" value="<?= set_value('uf_resp');?>">
                  </div>
                  <div class="form-group">
                    <label>UF do Responsável</label>
                    <input type="text" class="form-control" name="uf_resp" value="<?= set_value('uf_resp');?>">
                  </div>
                  <div class="form-group">
                    <label>Grau de Parentesco do Responsável</label>
                    <input type="text" class="form-control" name="rm_grau_parentesco_responsavel" value="<?= set_value('rm_grau_parentesco_responsavel');?>">
                  </div>
                  <div class="form-group">
                    <label>Sacado</label>
                    <input type="text" class="form-control" name="sacado" value="<?= set_value('sacado');?>">
                  </div>
                  <h5 class="bg-secondary p-3 mt-5">Dados do Responsável Financeiro</h5>
                  <div class="form-group">
                    <label>Responsável Financeiro - Nome</label>
                    <input type="text" class="form-control" name="rm_resp_financeiro_nome" value="<?= set_value('rm_resp_financeiro_nome');?>">
                  </div>
                  <div class="form-group">
                    <label>Responsável Financeiro - RG</label>
                    <input type="text" class="form-control" name="rm_resp_financeiro_rg" value="<?= set_value('rm_resp_financeiro_rg');?>">
                  </div>
                  <div class="form-group">
                    <label>Responsável Financeiro - CPF</label>
                    <input type="text" class="form-control" name="rm_resp_financeiro_cpf" value="<?= set_value('rm_resp_financeiro_cpf');?>">
                  </div>
                  <div class="form-group">
                    <label>Responsável Financeiro - Grau de Parentesco</label>
                    <input type="text" class="form-control" name="rm_resp_financeiro_grau_parentesco" value="<?= set_value('rm_resp_financeiro_grau_parentesco');?>">
                  </div>
                  <div class="form-group">
                    <label>Responsável Financeiro - Endereço para Correspondência</label>
                    <input type="text" class="form-control" name="rm_resp_financeiro_endereco_correspondencia" value="<?= set_value('rm_resp_financeiro_endereco_correspondencia');?>">
                  </div>
                  <div class="form-group">
                    <label>Responsável Financeiro - Bairro</label>
                    <input type="text" class="form-control" name="rm_resp_financeiro_bairro" value="<?= set_value('rm_resp_financeiro_bairro');?>">
                  </div>
                  <div class="form-group">
                    <label>Responsável Financeiro - CEP</label>
                    <input type="text" class="form-control" name="rm_resp_financeiro_cep" value="<?= set_value('rm_resp_financeiro_cep');?>">
                  </div>
                  <div class="form-group">
                    <label>Responsável Financeiro - Cidade/UF</label>
                    <input type="text" class="form-control" name="rm_resp_financeiro_cidade_estado" value="<?= set_value('rm_resp_financeiro_cidade_estado');?>">
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary" id="submit">
                    Salvar
                  </button>
                </div>
                <?= form_close() ?>
            </div>
            <!-- /.card -->
            </div>
          <!--/.col (left) -->
          <!-- right column -->
          <div class="col-md-6">

          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>

