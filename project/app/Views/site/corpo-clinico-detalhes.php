
<main>
    <section id="medicos">
        <div class="container">
            <div class="row d-flex justify-content-center pt-5">
                <div class="col-md-12 text-center mb-3">
                    <h1 class="text-uppercase text-primary fw-bold custom-h1">Corpo Clinico</h1>
                    <h6 class="text-uppercase text-secondary">Conheça nossos especialistas</h6>
                </div>
            </div>
            <div class="row d-flex justify-content-center pb-5 pt-4">
                <div class="col-md-3">
                    <div class="card w-100 h-100 bg-light border-0 mb-5 text-center">
                        <img src="<?= $fields->capa;?>" class="card-img-top p-2" alt="...">
                        <div class="card-body">
                            <h5 class="card-title text-primary fw-bold"><?= $fields->titulo;?></h5>
                            <h6 class="text-secondary"><?= explode("|", $fields->subtitulo)[0]?></h6>
                            <p class="card-text"><?= explode("|", $fields->subtitulo)[1]?></p>
                            <a href="<?= base_url('Corpo-Clinico')?>" class="btn btn-sm btn-primary rounded-pill w-100 mt-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle-fill" viewBox="0 0 16 16">
                                <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"/>
                                </svg>
                                Voltar
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <?= $fields->texto;?>
                </div>
            </div>
        </div>
    </section>
</main>
