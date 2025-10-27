
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
                <?php foreach($fields as $fieldsItem):?>
                <div class="col-md-3 mb-3">
                    <div class="card w-100 h-100 bg-light border-0 mb-5 text-center">
                        <img src="<?= $fieldsItem->capa;?>" class="card-img-top p-2" alt="...">
                        <div class="card-body d-flex align-items-center flex-column">
                            <h5 class="card-title text-primary fw-bold"><?= $fieldsItem->titulo;?></h5>
                            <h6 class="text-secondary"><?= explode("|", $fieldsItem->subtitulo)[0]?></h6>
                            <p class="card-text"><?= explode("|", $fieldsItem->subtitulo)[1]?></p>
                            <a href="<?= base_url('Corpo-Clinico').'/'.$fieldsItem->id ?>" class="btn btn-sm btn-primary rounded-pill w-100  mt-auto">
                                Conheça
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-circle-fill" viewBox="0 0 16 16">
                                <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0zM4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H4.5z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach;?>
            </div>
        </div>
    </section>
</main>
