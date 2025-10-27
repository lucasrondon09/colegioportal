<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Sobre</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= base_url('Admin/home');?>">Home</a></li>
              <li class="breadcrumb-item active">Sobre</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card text-center border-0 shadow">
              <div class="card-body">
                <img src="<?= base_url(CMSLOGO);?>" class="m-3" alt="Logo">
                <h5 class="mt-2"><?= CMSNOME?></h5>
                <p><strong><?= CMSDESCRICAO?></strong></p>
                <p>Versão: <?= CMSVERSAO?></p>
                <hr>
                <ul class="list-unstyled">
                  <li>Desenvolvido em: <?= CMSDESENVOLVIDOEM?></li>
                  <li>Atualizado em: <?= CMSATUALIZADOEM?></li>
                  <li>Hospedagem: <?= CMSHOSPEDAGEM;?></li>
                  <li>Sistema Operacional: <?= CMSSISTEMAOPERACIONAL;?></li>
                  <li>Versão PHP: <?= CMSVERSAOPHP;?></li>
                  <li>Versão MySQL: <?= CMSVERSAOMYSQL;?></li>
                  <br>
                  <li>Desenvolvido por: <a href="<?= CMSSITEDESENVOLVEDOR?>" target="_blank"><?= CMSDESENVOLVEDOR?></a></li>
                  <li><strong><?= CMSNOME?> &copy; <?= CMSANODESENVOLVIMENTO?>-<?= date('Y')?> .</strong>Todos os direitos reservados.</li>
                  <hr>
                  <li>Desenvolvido a partir do projeto AdminLTE</li>
                  <li>Arquitetura front-end de código aberto licenciado sob a <strong>Licença MIT</strong></li>
                  <li>Direitos Autorais &copy; <a href="https://adminlte.io/" target="_blank">AdminLTE.io</a></li>
                  <hr>
                  <li>Framework Codeigniter 4.3</li>
                  <li>© 2024 CodeIgniter Foundation. CodeIgniter é um projeto de código aberto lançado sob a <strong>licença de código aberto do MIT.</strong></li>
                  <li>Direitos Autorais &copy; <a href="https://www.codeigniter.com/" target="_blank">codeigniter.com</a></li>
                </ul>
              </div>  
            </div>
            <!-- /.invoice -->
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->