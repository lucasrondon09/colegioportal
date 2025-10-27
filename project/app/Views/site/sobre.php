
<main>
    <section id="sobre">
        <div class="container">
            <div class="row py-5">
                <div class="col-md-4">
                    <img src="<?= $pagina->capa?>" alt="logo" class="img-fluid w-100">
                </div>
                <div class="col-md-8">
                    <h1 class="text-primary"><span class="ps-2 fw-bold"><?= $pagina->titulo?></span></h1>
                    <h5><?= $pagina->subtitulo?></h5>
                    <?= $pagina->texto?>
                    <p>
                        <h3 class="text-secondary">Siga nas redes socias</h3>
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
                                <a href="<?= YOUTUBE?>" class="text-decoration-none text-primary">
                                    <i class="fab fa-2x fa-youtube"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="<?= WHATSAPP?>" class="text-decoration-none text-primary">
                                <i class="fa-brands fa-2x fa-whatsapp"></i>
                                </a>
                            </li>
                        </ul>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <h3>Infraestrutura</h3>
                </div>
            </div>
            <div class="row pb-5">
                <?php foreach($imagens as $imagemItem):?>
                    <div class="col-md-4">
                        <a href="<?= $imagemItem->imagem?>" data-fancybox="gallery">
                            <div class="mb-3 pics animation all 3">
                                <img src="<?= $imagemItem->imagem?>" alt="" class="img-fluid w-100">
                            </div>	
                        </a>	
                    </div>
                <?php endforeach;?>
            </div>
        </div>
    </section>
</main>
