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
              <?php if(session()->sistema == 'sisaula'):?>
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/Pais')?>">Pais</a></li>
              <?php endif;?>
              <li class="breadcrumb-item active">Detalhes</li>
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
                <?php if(session()->sistema == 'sisaula'):?>
                <a href="<?= base_url('/Admin/Pais')?>" class="text-decoration-none text-dark">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                  </svg>
                </a> 
                <?php endif;?>              
                <span class="ml-3">Detalhes do Registro</span>
              </h3>
              <!-- form start -->
                <div class="m-4">
                  <a href="<?= base_url('Admin/Alunos').'/'.$field->id;?>" class="btn btn-sm btn-outline-success">
                      <i class="fas fa-users"></i> Filhos
                  </a>
                  <a href="<?= base_url('Admin/Pais/editar').'/'.$field->id;?>" class="btn btn-sm btn-outline-info">
                    <i class="fas fa-pen fa-fw"></i> Editar
                  </a>
                  </div>
                <hr>
              <form>
                <fieldset disabled>
                <div class="card-body">
                  <div class="form-group row">
                    <label class="col-form-label col-sm-2">Matricula Pai</label>
                    <input type="text" class="form-control-plaintext col-sm-2" name="mat_pai" value="<?= $field->mat_pai;?>">
                    <?php if(session()->sistema == 'sisaula'):?>
                    <label class="col-form-label col-sm-2">Saldo Devedor</label>
                    <input type="text" class="form-control col-sm-2" name="devedor" value="<?= $field->devedor;?>" placeholder="R$">
                    <?php endif;?>
                  </div>
                  <h5 class="bg-secondary p-3">Dados do Pai</h5>
                  <div class="form-group">
                    <label>Nome</label>
                    <input type="text" class="form-control" name="nome_pai" value="<?= $field->nome_pai;?>">
                  </div>
                  <div class="form-group">
                    <label>Senha</label>
                    <input type="text" class="form-control" name="senha_pai" value="<?= $field->senha_pai;?>">
                  </div>
                  <div class="form-group">
                    <label>CPF</label>
                    <input type="text" class="form-control" name="cpf_pai" value="<?= $field->cpf_pai;?>">
                  </div>
                  <div class="form-group">
                    <label>RG</label>
                    <input type="text" class="form-control" name="rg_pai" value="<?= $field->rg_pai;?>">
                  </div>
                  <div class="form-group">
                    <label>Nascimento Pai</label>
                    <input type="text" class="form-control" name="nasc_pai" value="<?= $field->nasc_pai;?>">
                  </div>
                  <div class="form-group">
                    <label>Trabalho Pai</label>
                    <input type="text" class="form-control" name="trabalho_pai" value="<?= $field->trabalho_pai;?>">
                  </div>
                  <div class="form-group">
                    <label>Profissão Pai</label>
                    <input type="text" class="form-control" name="profissao_pai" value="<?= $field->profissao_pai;?>">
                  </div>
                  <div class="form-group">
                    <label>Naturalidade Pai</label>
                    <input type="text" class="form-control" name="nat_pai" value="<?= $field->nat_pai;?>">
                  </div>
                  <div class="form-group">
                    <label>Celular Pai</label>
                    <input type="text" class="form-control" name="cel_pai" value="<?= $field->cel_pai;?>">
                  </div>
                  <div class="form-group">
                    <label>Fone Pai</label>
                    <input type="text" class="form-control" name="fone_pai" value="<?= $field->fone_pai;?>">
                  </div>
                  <div class="form-group">
                    <label>Email Pai</label>
                    <input type="text" class="form-control" name="email_pai" value="<?= $field->email_pai;?>">
                  </div>
                  <div class="form-group">
                    <label>Bairro Pai</label>
                    <input type="text" class="form-control" name="bairro_pai" value="<?= $field->bairro_pai;?>">
                  </div>
                  <div class="form-group">
                    <label>Cidade Pai</label>
                    <input type="text" class="form-control" name="cid_pai" value="<?= $field->cid_pai;?>">
                  </div>
                  <div class="form-group">
                    <label>Endereço Pai</label>
                    <input type="text" class="form-control" name="end_pai" value="<?= $field->end_pai;?>">
                  </div>
                  <div class="form-group">
                    <label>UF Pai</label>
                    <input type="text" class="form-control" name="uf_pai" value="<?= $field->uf_pai;?>">
                  </div>
                  <div class="form-group">
                    <label>Estado Civil Pai</label>
                    <input type="text" class="form-control" name="rm_pai_estado_civil" value="<?= $field->rm_pai_estado_civil;?>">
                  </div>
                  <h5 class="bg-secondary p-3 mt-5">Dados da Mãe</h5>
                  <div class="form-group">
                    <label>Nome da Mãe</label>
                    <input type="text" class="form-control" name="nome_mae" value="<?= $field->nome_mae;?>">
                  </div>
                  <div class="form-group">
                    <label>RG da Mãe</label>
                    <input type="text" class="form-control" name="rg_mae" value="<?= $field->rg_mae;?>">
                  </div>
                  <div class="form-group">
                    <label>CPF da Mãe</label>
                    <input type="text" class="form-control" name="cpf_mae" value="<?= $field->cpf_mae;?>">
                  </div>
                  <div class="form-group">
                    <label>Trabalho da Mãe</label>
                    <input type="text" class="form-control" name="trabalho_mae" value="<?= $field->trabalho_mae;?>">
                  </div>
                  <div class="form-group">
                    <label>Profissão da Mãe</label>
                    <input type="text" class="form-control" name="profissao_mae" value="<?= $field->profissao_mae;?>">
                  </div>
                  <div class="form-group">
                    <label>Naturalidade da Mãe</label>
                    <input type="text" class="form-control" name="nat_mae" value="<?= $field->nat_mae;?>">
                  </div>
                  <div class="form-group">
                    <label>Celular da Mãe</label>
                    <input type="text" class="form-control" name="cel_mae" value="<?= $field->cel_mae;?>">
                  </div>
                  <div class="form-group">
                    <label>Telefone da Mãe</label>
                    <input type="text" class="form-control" name="fone_mae" value="<?= $field->fone_mae;?>">
                  </div>
                  <div class="form-group">
                    <label>Email da Mãe</label>
                    <input type="text" class="form-control" name="email_mae" value="<?= $field->email_mae;?>">
                  </div>
                  <div class="form-group">
                    <label>Endereço da Mãe</label>
                    <input type="text" class="form-control" name="end_mae" value="<?= $field->end_mae;?>">
                  </div>
                  <div class="form-group">
                    <label>Bairro da Mãe</label>
                    <input type="text" class="form-control" name="bairro_mae" value="<?= $field->bairro_mae;?>">
                  </div>
                  <div class="form-group">
                    <label>Cidade da Mãe</label>
                    <input type="text" class="form-control" name="cid_mae" value="<?= $field->cid_mae;?>">
                  </div>
                  <div class="form-group">
                    <label>UF da Mãe</label>
                    <input type="text" class="form-control" name="uf_mae" value="<?= $field->uf_mae;?>">
                  </div>
                  <div class="form-group">
                    <label>Estado Civil Mãe</label>
                    <input type="text" class="form-control" name="rm_mae_estado_civil" value="<?= $field->rm_mae_estado_civil;?>">
                  </div>
                  <h5 class="bg-secondary p-3 mt-5">Dados do Responsável</h5>
                  <div class="form-group">
                    <label>Nome do Responsável</label>
                    <input type="text" class="form-control" name="nome_resp" value="<?= $field->nome_resp;?>">
                  </div>
                  <div class="form-group">
                    <label>RG do Responsável</label>
                    <input type="text" class="form-control" name="rg_resp" value="<?= $field->rg_resp;?>">
                  </div>
                  <div class="form-group">
                    <label>CPF do Responsável</label>
                    <input type="text" class="form-control" name="cpf_resp" value="<?= $field->cpf_resp;?>">
                  </div>
                  <div class="form-group">
                    <label>Nascimento do Responsável</label>
                    <input type="text" class="form-control" name="nasc_resp" value="<?= $field->nasc_resp;?>">
                  </div>
                  <div class="form-group">
                    <label>Celular do Responsável</label>
                    <input type="text" class="form-control" name="fone_resp" value="<?= $field->fone_resp;?>">
                  </div>
                  <div class="form-group">
                    <label>Telefone do Responsável</label>
                    <input type="text" class="form-control" name="tel_resp" value="<?= $field->tel_resp;?>">
                  </div>
                  <div class="form-group">
                    <label>Email do Responsável</label>
                    <input type="text" class="form-control" name="email_resp" value="<?= $field->email_resp;?>">
                  </div>
                  <div class="form-group">
                    <label>Endereço do Responsável</label>
                    <input type="text" class="form-control" name="end_resp" value="<?= $field->end_resp;?>">
                  </div>
                  <div class="form-group">
                    <label>Bairro do Responsável</label>
                    <input type="text" class="form-control" name="bairro_resp" value="<?= $field->bairro_resp;?>">
                  </div>
                  <div class="form-group">
                    <label>Cidade do Responsável</label>
                    <input type="text" class="form-control" name="cid_resp" value="<?= $field->cid_resp;?>">
                  </div>
                  <div class="form-group">
                    <label>UF do Responsável</label>
                    <input type="text" class="form-control" name="uf_resp" value="<?= $field->uf_resp;?>">
                  </div>
                  <div class="form-group">
                    <label>UF do Responsável</label>
                    <input type="text" class="form-control" name="uf_resp" value="<?= $field->uf_resp;?>">
                  </div>
                  <div class="form-group">
                    <label>Grau de Parentesco do Responsável</label>
                    <input type="text" class="form-control" name="rm_grau_parentesco_responsavel" value="<?= $field->rm_grau_parentesco_responsavel;?>">
                  </div>
                  <div class="form-group">
                    <label>Sacado</label>
                    <input type="text" class="form-control" name="sacado" value="<?= $field->sacado;?>">
                  </div>
                  <h5 class="bg-secondary p-3 mt-5">Dados do Responsável Financeiro</h5>
                  <div class="form-group">
                    <label>Responsável Financeiro - Nome</label>
                    <input type="text" class="form-control" name="rm_resp_financeiro_nome" value="<?= $field->rm_resp_financeiro_nome;?>">
                  </div>
                  <div class="form-group">
                    <label>Responsável Financeiro - RG</label>
                    <input type="text" class="form-control" name="rm_resp_financeiro_rg" value="<?= $field->rm_resp_financeiro_rg;?>">
                  </div>
                  <div class="form-group">
                    <label>Responsável Financeiro - CPF</label>
                    <input type="text" class="form-control" name="rm_resp_financeiro_cpf" value="<?= $field->rm_resp_financeiro_cpf;?>">
                  </div>
                  <div class="form-group">
                    <label>Responsável Financeiro - Grau de Parentesco</label>
                    <input type="text" class="form-control" name="rm_resp_financeiro_grau_parentesco" value="<?= $field->rm_resp_financeiro_grau_parentesco;?>">
                  </div>
                  <div class="form-group">
                    <label>Responsável Financeiro - Endereço para Correspondência</label>
                    <input type="text" class="form-control" name="rm_resp_financeiro_endereco_correspondencia" value="<?= $field->rm_resp_financeiro_endereco_correspondencia;?>">
                  </div>
                  <div class="form-group">
                    <label>Responsável Financeiro - Bairro</label>
                    <input type="text" class="form-control" name="rm_resp_financeiro_bairro" value="<?= $field->rm_resp_financeiro_bairro;?>">
                  </div>
                  <div class="form-group">
                    <label>Responsável Financeiro - CEP</label>
                    <input type="text" class="form-control" name="rm_resp_financeiro_cep" value="<?= $field->rm_resp_financeiro_cep;?>">
                  </div>
                  <div class="form-group">
                    <label>Responsável Financeiro - Cidade/UF</label>
                    <input type="text" class="form-control" name="rm_resp_financeiro_cidade_estado" value="<?= $field->rm_resp_financeiro_cidade_estado;?>">
                  </div>
                </div>
                </fieldset>
                <!-- /.card-body -->
              </form>
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

