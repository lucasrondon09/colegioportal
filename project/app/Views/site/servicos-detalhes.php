
<main>
    <div class="bg-white my-5">
        <div class="container">
            <div class="row">
                <div class="col-12 mb-3">
                    <h6 class="mb-0 text-primary border-start border-2 border-primary"><span class="ms-2">SERVIÇOS</span></h6>
                    <h1 class="text-secondary fw-bold"><?= $fields->titulo?></h1>
                    <h5 class="text-secondary fw-bold"><?= $fields->subtitulo?></h5>
                </div>
                <div class="col-md-12 mb-3">
                    <div class="card w-100 bg-light shadow border-0 h-100" >
                        <div class="card-body">
                            <?= $fields->texto?>
                            <a href="<?= base_url('Exames')?>" class="btn btn-primary rounded-pill btn-sm fw-bold px-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                            </svg>
                            Voltar
                            </a>
                        </div>
                    </div>
                </div>    
            </div>
        </div>
    </div>
</main>
