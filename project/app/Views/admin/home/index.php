
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Home</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/Admin/home">Home</a></li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="callout callout-info">
              <h5>Bem vindo, <?= session()->userName;?>!</h5>
              <p></p> 
              <a href="<?= SITEURL;?>" class="btn btn-info btn-sm text-decoration-none text-white" target="_blank">Acessar site</a>
            </div>

    
            <?php if(session()->sistema == 'sispai'):?>

              <div class="invoice p-3">
              <div class="row">
                <div class="col-md-12"><h6 class="font-weight-bold text-secondary">Boletins</h6></div>
              </div>
              <div class="row">
                <div class="col-12">
                  <table id="registros" class="table table-bordered table-hover mb-3">
                    <thead>
                    <tr>
                      <th>Matricula</th>
                      <th>Nome</th>
                      <th>Turma</th>
                      <th>Ano</th>
                      <th>Notas Trimestrais</th>
                      <th>Boletim</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($aluno as $alunoItem):?>
                      <tr>
                        <td><?= $alunoItem->alunoMatricula?></td>
                        <td><?= $alunoItem->alunoNome?></td>
                        <td><?= $alunoItem->turmaNome?></td>
                        <td><?= $alunoItem->turmaAno?></td>
                        <td class="text-center">
                          <a href="<?= base_url('/Admin/Relatorios/boletim-turma/notas-trimestrais/'.$alunoItem->turmaId.'/'.$alunoItem->alunoId.'/'.'1')?>" class="btn btn-sm btn-outline-primary">1º Trim</a>
                          <a href="<?= base_url('/Admin/Relatorios/boletim-turma/notas-trimestrais/'.$alunoItem->turmaId.'/'.$alunoItem->alunoId.'/'.'2')?>" class="btn btn-sm btn-outline-primary">2º Trim</a>
                          <a href="<?= base_url('/Admin/Relatorios/boletim-turma/notas-trimestrais/'.$alunoItem->turmaId.'/'.$alunoItem->alunoId.'/'.'3')?>" class="btn btn-sm btn-outline-primary">3º Trim</a>
                        </td>
                        <td class="text-center" width="15%">
                          <a href="<?= base_url('Admin/Relatorios/boletim-turma/gerar-boletim-turma').'/'.$alunoItem->turmaId.'/'.$alunoItem->alunoId?>" class="btn btn-sm btn-outline-danger" data-toggle="tooltip" data-placement="top" title="Gerar Boletim Individual">
                          <i class="fas fa-file-pdf mr-2"></i>Gerar Boletim
                          </a>
                        </td>
                      </tr>
                    <?php endforeach;?>  
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <?php else:?>
            <?php if((int)session()->userPermissao <> 3):?>  
            <div class="invoice p-3">
              <div class="row">
                <div class="col-md-12"><h6 class="font-weight-bold text-secondary">Acesso Rápido <i class="fas fa-arrow-circle-right"></i></h6></div>
              </div>
              <div class="row">
                <div class="col-lg-3 col-6">
                  <!-- small box -->
                  <div class="small-box bg-info">
                    <div class="inner">
                      <h3>Usuários</h3>

                      <p>Visualizar registros</p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-users"></i>
                    </div>
                    <a href="<?= base_url('Admin/Usuarios')?>" class="small-box-footer">Acessar <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                  <!-- small box -->
                  <div class="small-box bg-success">
                    <div class="inner">
                      <h3>Pais</h3>

                      <p>Visualizar registros</p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-user-friends"></i>
                    </div>
                    <a href="<?= base_url('/Admin/Pais')?>" class="small-box-footer">Acessar <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                  <!-- small box -->
                  <div class="small-box bg-secondary text-white">
                    <div class="inner">
                      <h3>Turmas</h3>

                      <p>Visualizar registros</p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-book"></i>
                    </div>
                    <a href="<?= base_url('/Admin/Turma')?>" class="small-box-footer">Acessar <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                  <!-- small box -->
                  <div class="small-box bg-danger">
                    <div class="inner">
                      <h3>Alunos</h3>

                      <p>Visualizar registros</p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-user-graduate"></i>
                    </div>
                    <a href="<?= base_url('/Admin/Alunos')?>" class="small-box-footer">Acessar <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>
                <!-- ./col -->
              </div>
            </div>

            <!-- Main content -->
            <div class="invoice p-3 my-3">
              <!-- title row -->
              <div class="row">
                <div class="col-12">
                  <h4>
                    <img src="<?= base_url('/assets/admin/dist/img/logo_portal.png')?>" alt="Colégio Portal" width="150px">
                    <small class="float-right">Data: <?= date('d/m/Y')?></small>
                  </h4>
                </div>
                <!-- /.col -->
              </div>
              <!-- info row -->
              <div class="row invoice-info">
                <div class="col-sm-6 invoice-col">
                  <address>
                    <strong><?= SITENOME;?></strong><br>
                    <?= SITEENDERECO;?><br>
                    <?= SITEUF;?><br>
                    Telefone: <?= SITETELEFONE;?><br>
                    <a href="<?= SITEURL;?>" target="_blank"><?= SITEURL;?></a>
                  </address>
                </div>
                <div class="col-sm-6 invoice-col">
                  <address>
                    Hospedagem: <?= CMSHOSPEDAGEM;?><br>
                    Sistema Operacional: <?= CMSSISTEMAOPERACIONAL;?><br>
                    Versão PHP: <?= CMSVERSAOPHP;?><br>
                    Versão MySQL: <?= CMSVERSAOMYSQL;?><br>
                    Última atualização: <?= CMSATUALIZADOEM?><br>
                  </address>
                </div>
              </div>
              <!-- /.row -->

            </div>
            <?php endif;?>  
            <?php endif;?>  
            <!-- /.invoice -->
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->