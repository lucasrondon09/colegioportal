  <?php

  use App\Controllers\Admin\SI_PaginasInternas;

  ?>
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= base_url('/Admin/home'); ?>" class="brand-link">
      <img src="<?= base_url('assets/admin/dist/img/logo_portal_branca.png'); ?>" alt="Colégio Portal" class="brand-image elevation-3"><br>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <!--
      <div class="mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?= base_url('assets/admin/dist/img/logoempresa.png'); ?>" class="w-100" alt="Logo">
        </div>
      </div>
      -->

      <!-- SidebarSearch Form 
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Procurar" aria-label="Procurar">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>-->

      <!-- Sidebar Menu -->
      <nav class="mt-2">


        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- AULA - GERENCIAR -->
          <h4 class="text-white mt-4">AULA - GERENCIAR</h4>
          <!-- MENU PAI -->
          <?php if (session()->sistema == 'sispai'): ?>
            <li class="nav-item">
              <a href="<?= base_url('/Admin/home') ?>" class="nav-link">
                <i class="fas fa-th-list mr-2"></i>
                <p>
                  Boletins
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= base_url('/Admin/Pais/detalhes/' . session()->userId) ?>" class="nav-link">
                <i class="fas fa-user mr-2"></i>
                <p>
                  Dados Pessoais
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= base_url('/Admin/Alunos/' . session()->userId) ?>" class="nav-link">
                <i class="fas fa-users mr-2"></i>
                <p>
                  Filhos
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= base_url('/Galeria-de-Fotos') ?>" class="nav-link">
                <i class="fas fa-images mr-2"></i>
                <p>
                  Galeria de Fotos
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="fas fa-file mr-2"></i>
                <p>
                  Páginas Internas
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php $paginasInternas = (new SI_PaginasInternas)->getPaginasInternas(); ?>
                <?php foreach ($paginasInternas as $paginasInternasItem): ?>
                  <li class="nav-item">
                    <a href="<?= base_url('Admin/Paginas-Internas') . '/' . $paginasInternasItem->id ?>" class="nav-link">
                      <i class="fas fa-angle-right mr-2"></i>
                      <p><?= $paginasInternasItem->descricao ?></p>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            </li>
          <?php endif; ?>
          <!-- MENU PAI -->

          <!-- MENU ADMIN -->
          <?php if (session()->sistema == 'sisaula'): ?>

            <!-- MENU PROFESSOR -->
            <?php if ((int)session()->userPermissao == 3): ?>
              <li class="nav-item">
                <a href="<?= base_url('Admin/home') ?>" class="nav-link">
                  <i class="fas fa-home mr-2"></i>
                  <p>
                    Página Inicial
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= base_url('Admin/Turma/permissao-professor') ?>" class="nav-link">
                  <i class="fas fa-th-list mr-2"></i>
                  <p>
                    Turmas do Professor
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="far fa-newspaper mr-2"></i>
                  <p>
                    Relatórios
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="<?= base_url('Admin/Relatorios/listas') ?>" class="nav-link">
                      <i class="fas fa-angle-right mr-2"></i>
                      <p>Listas</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('/Admin/Relatorios/boletim') ?>" class="nav-link">
                      <i class="fas fa-angle-right mr-2"></i>
                      <p>Notas (Boletim)</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('Admin/Relatorios/pais') ?>" class="nav-link">
                      <i class="fas fa-angle-right mr-2"></i>
                      <p>Diário (Classe/Tarefas)</p>
                    </a>
                  </li>
                </ul>
              </li>
            <?php else: ?>
              <li class="nav-item">
                <a href="<?= base_url('/Admin/home') ?>" class="nav-link">
                  <i class="fa fa-home mr-2" aria-hidden="true"></i>
                  <p>
                    Home
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= base_url('Admin/Usuarios') ?>" class="nav-link">
                  <i class="fa fa-users mr-2" aria-hidden="true"></i>
                  <p>
                    Usuários
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= base_url('Admin/Pais') ?>" class="nav-link">
                  <i class="fas fa-user-friends mr-2"></i>
                  <p>
                    Pais
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= base_url('Admin/Alunos') ?>" class="nav-link">
                  <i class="fas fa-user-graduate mr-2"></i>
                  <p>
                    Alunos
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= base_url('Admin/Turma') ?>" class="nav-link">
                  <i class="fas fa-book mr-2"></i>
                  <p>
                    Turmas
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= base_url('Admin/Ano-Letivo') ?>" class="nav-link">
                  <i class="fas fa-calendar mr-2"></i>
                  <p>
                    Ano Letivo
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= base_url('Admin/Turma/permissao-professor') ?>" class="nav-link">
                  <i class="fas fa-pen-alt mr-2"></i>
                  <p>
                    Lançar Notas
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="fas fa-file mr-2"></i>
                  <p>
                    Páginas Internas
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <?php $paginasInternas = (new SI_PaginasInternas)->getPaginasInternas(); ?>
                  <?php foreach ($paginasInternas as $paginasInternasItem): ?>
                    <li class="nav-item">
                      <a href="<?= base_url('Admin/Paginas-Internas') . '/' . $paginasInternasItem->id ?>" class="nav-link">
                        <i class="fas fa-angle-right mr-2"></i>
                        <p><?= $paginasInternasItem->descricao ?></p>
                      </a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="far fa-newspaper mr-2"></i>
                  <p>
                    Relatórios
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="<?= base_url('Admin/Relatorios/listas') ?>" class="nav-link">
                      <i class="fas fa-angle-right mr-2"></i>
                      <p>Listas</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('/Admin/Relatorios/boletim') ?>" class="nav-link">
                      <i class="fas fa-angle-right mr-2"></i>
                      <p>Notas (Boletim)</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('Admin/Relatorios/pais') ?>" class="nav-link">
                      <i class="fas fa-angle-right mr-2"></i>
                      <p>Pais (PDF)</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('Admin/Relatorios/alunos') ?>" class="nav-link">
                      <i class="fas fa-angle-right mr-2"></i>
                      <p>Alunos (PDF)</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('Admin/Relatorios/livro-matricula') ?>" class="nav-link">
                      <i class="fas fa-angle-right mr-2"></i>
                      <p>Livro de Matrículas (PDF)</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('/Admin/Relatorios/media-turma') ?>" class="nav-link">
                      <i class="fas fa-angle-right mr-2"></i>
                      <p>Média das Turmas</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('/Admin/Relatorios/media-alunos') ?>" class="nav-link">
                      <i class="fas fa-angle-right mr-2"></i>
                      <p>Média dos Alunos</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('/Admin/Relatorios/media-individual-nucleo-comum') ?>" class="nav-link">
                      <i class="fas fa-angle-right mr-2"></i>
                      <p>Média Individual - Núcleo Comum</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('/Admin/Relatorios/media-individual-todas-disciplinas') ?>" class="nav-link">
                      <i class="fas fa-angle-right mr-2"></i>
                      <p>Média Individual - Todas as Disciplinas</p>
                    </a>
                  </li>
                </ul>
              </li>

              <!-- AULA - GERENCIAR -->

              <h4 class="text-white mt-4">FINANCEIRO</h4>
              <li class="nav-item">
                  <a href="<?= base_url('/Admin/Visao-Geral-Financeiro') ?>" class="nav-link">
                      <i class="fas fa-chart-bar mr-2"></i>
                      <p>Visão Geral Financeira</p>
                  </a>
              </li>
              <li class="nav-item">
                <a href="<?= base_url('/Admin/Contrato') ?>" class="nav-link">
                  <i class="fas fa-file mr-2"></i>
                  <p>
                    Contratos
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= base_url('Admin/SI_FormaPagamento')?>" class="nav-link">
                  <i class="fas fa-credit-card mr-2"></i>
                  <p>Formas de Pagamento</p>
                </a>
              </li>


              <!-- GERENCIAR SITE-->
              <h4 class="text-white mt-4">GERENCIAR SITE</h4>
              <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="far fa-newspaper mr-2"></i>
                  <p>
                    Notícias
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="<?= base_url('Admin/Noticias') ?>" class="nav-link">
                      <i class="fas fa-angle-right mr-2"></i>
                      <p>Notícias</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('Admin/Categorias') ?>" class="nav-link">
                      <i class="fas fa-angle-right mr-2"></i>
                      <p>Categorias</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="fas fa-images mr-2"></i>
                  <p>
                    Galeria
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="<?= base_url('Admin/Galerias') ?>" class="nav-link">
                      <i class="fas fa-angle-right mr-2"></i>
                      <p>Galeria</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('Admin/Categorias-galeria') ?>" class="nav-link">
                      <i class="fas fa-angle-right mr-2"></i>
                      <p>Categorias</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="fas fa-file mr-2"></i>
                  <p>
                    Páginas
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="<?= base_url('Admin/Paginas') ?>" class="nav-link">
                      <i class="fas fa-angle-right mr-2"></i>
                      <p>Páginas</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('Admin/PaginasCategorias') ?>" class="nav-link">
                      <i class="fas fa-angle-right mr-2"></i>
                      <p>Categorias</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="<?= base_url('Admin/Servicos') ?>" class="nav-link">
                  <i class="fas fa-briefcase mr-2"></i>
                  <p>
                    Serviços
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= base_url('Admin/Banners') ?>" class="nav-link">
                  <i class="fas fa-image mr-2"></i>
                  <p>
                    Banners
                  </p>
                </a>
              </li>
              <!--
          <li class="nav-item">
            <a href="<?= base_url('Admin/Leads'); ?>" class="nav-link">
              <i class="fas fa-filter mr-2"></i>
              <p>
                Leads
              </p>
            </a>
          </li>-->
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="fas fa-tools mr-2"></i>
                  <p>
                    Configurações
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="<?= base_url('Admin/RedesSociais'); ?>" class="nav-link">
                      <i class="fas fa-angle-right mr-2"></i>
                      <p>Redes Sociais</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('Admin/Email'); ?>" class="nav-link">
                      <i class="fas fa-angle-right mr-2"></i>
                      <p>E-mail</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('Admin/GoogleAnalytics'); ?>" class="nav-link">
                      <i class="fas fa-angle-right mr-2"></i>
                      <p>Google Analytics</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('Admin/MetaTags'); ?>" class="nav-link">
                      <i class="fas fa-angle-right mr-2"></i>
                      <p>Meta Tags</p>
                    </a>
                  </li>
                </ul>
              </li>
            <?php endif; ?>
          <?php endif; ?>
        </ul>
        <!-- GERENCIAR SITE-->
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>