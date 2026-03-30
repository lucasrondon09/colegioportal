<main>
    <div id="carouselIndicators" class="carousel slide carousel-fade d-none d-md-block" data-bs-ride="carousel">
    <!--    
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>-->
        <div class="carousel-inner">
            <?php 
            $active = 'active';
            foreach($banner as $bannerItem):?>
                <div class="carousel-item <?= $active?>">
                    <a href="<?= $bannerItem->link?>"><img src="<?= $bannerItem->imagem?>" class="d-block w-100" alt="<?= $bannerItem->titulo?>"></a>
                </div>
            <?php 
            $active = '';
            endforeach
            ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    <div id="carouselIndicatorsSmall" class="carousel slide carousel-fade d-sm-block d-md-none" data-bs-ride="carousel">
    <!--    
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>-->
        <div class="carousel-inner">
            <?php 
            $active = 'active';
            foreach($banner as $bannerItem):?>
            <div class="carousel-item <?= $active?>">
                <a href="<?= $bannerItem->link?>"><img src="<?= $bannerItem->imagem_responsiva?>" class="d-block w-100" alt="<?= $bannerItem->titulo?>"></a>
            </div>
            <?php 
            $active = '';
            endforeach
            ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselIndicatorsSmall" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselIndicatorsSmall" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    <div class="bg-white">
        <div class="bg-white">
            <div class="container py-5">
                <div class="row d-flex align-items-center justify-content-center">
                    <div class="col-md-12">
                    <img src="<?= base_url('assets/site/img/logo.png')?>" alt="" class="img-fluid w-100 d-block d-md-none">
                    </div>
                    <div class="col-md-8 py-5 text-center text-md-start">
                        <img src="<?= base_url('assets/site/img/logo.png')?>" alt="" class="img-fluid col-md-4">
                        <p class="pt-3">
                            Bem-vindo à PR Passos Consultoria, empresa de consultoria especializada em combustíveis! Com experiencia e conhecimento aprofundado do setor, estamos aqui para oferecer soluções estratégicas e orientação personalizada para o seu negócio. Nossos serviços abrangem desde conformidade regulatória e gestão operacional até eficiência energética e estratégias de marketing. Trabalhamos lado a lado com sua empresa, priorizando transparência e resultados duradouros. Junte-se a nós para impulsionar o sucesso do seu negócio no mercado de combustíveis.
                        </p>
                        <ul class="list-inline">
                            <li class="list-inline-item">
                                <a href="<?= INSTAGRAM?>" class="text-decoration-none text-primary">
                                    <i class="fa-brands fa-2x fa-instagram"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="<?= FACEBOOK?>" class="text-decoration-none text-primary">
                                    <i class="fa-brands fa-2x fa-facebook"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="<?= WHATSAPP?>" class="text-decoration-none text-primary">
                                <i class="fa-brands fa-2x fa-whatsapp"></i>
                                </a>
                            </li>
                            <li class="list-inline-item float-end">
                            <a href="<?= base_url('Sobre');?>" class="btn btn-sm btn-outline-primary rounded-pill ">
                            Saiba mais sobre a  <span class="fw-bolder">PR PASSOS Consultoria</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-arrow-right-circle ms-2" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H4.5z"/>
                            </svg>
                            </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="py-5 position-relative" style="background-color: #C7A47A">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="text-center text-white">Conheça nossos serviços</h1>
                    <h5 class="text-center text-white-50">Temos diversos serviços especializados para atender sua empresa</h5>
                </div>
            </div>
            <div class="row d-flex align-items-center">
                <?php foreach ($servicos as $servicoItem):?>
                <div class="col-md-3 my-3">
                    <div class="card w-100 border-0">
                        <img src="<?= $servicoItem->capa;?>" class="card-img-top" alt="...">
                        <div class="card-body py-3">
                            <h4 class="text-primary"><?= $servicoItem->titulo;?></h4>
                            <p class="text-secondary small"><?= $servicoItem->subtitulo;?></p>
                            <a href="<?= base_url('Agendamento-Consulta');?>" class="btn btn-sm fw-bold px-3 btn-primary rounded-pill">Saiba mais
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                            </svg>
                            </a>
                        </div>
                    </div> 
                </div>
                <?php endforeach;?>
            </div>
        </div>
    </div>
        <div class="bg-white mt-5">
        <div class="container">
            <div class="row d-flex justify-content-center py-5">
                <div class="col-md-8 text-center">
                    <h1 class="text-primary custom-h1">Receba as novidades</h1>
                    <p class="text-secondary">Cadastre seu e-mail e acompanhe as novidades da PR PASSOS CONSULTORIA.</p>
                    <div class="mt-4">
                        <?= form_open(base_url('Home/newsletter')) ?>
                        <div class="row">
                            <div class="col-md-9 mb-3">
                                <input type="email" class="form-control py-3 border-0 border-bottom border-2 border-primary w-100" placeholder="Digite seu e-mail" name="email" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <button class="btn btn-primary py-3 w-100" type="submit" id="button-addon2"><span class="small fw-light">CADASTRAR</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-circle-fill" viewBox="0 0 16 16">
                                    <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0zM4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H4.5z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <?= form_close() ?>
                    </div>    
                </div>
            </div>
        </div>
    </div>
</main>