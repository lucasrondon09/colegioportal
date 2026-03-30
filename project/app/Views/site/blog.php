
<main>
    <div class="bg-white my-5">
        <div class="container">
            <div class="row">
                <div class="col-12 mb-3">
                    <h6 class="mb-0 text-primary border-start border-2 border-primary custom-h1"><span class="ms-2">BLOG AXONS</span></h6>
                    <h1 class="text-secondary fw-bold">FIQUE POR DENTRO DAS NOVIDADES</h1>
                </div>
                <?php foreach($fields as $fieldsItem):?>
                <div class="col-md-4 mb-3">
                    <div class="card w-100 bg-light shadow border-0" >
                        <img src="<?= $fieldsItem->capa;?>" class="card-img-top" alt="<?= $fieldsItem->tituloNoticia;?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= $fieldsItem->tituloNoticia;?></h5>
                            <p class="small fw-bold text-primary"><?= $fieldsItem->tituloCategoria;?></p>
                            <p class="card-text"><?= mb_strimwidth(strip_tags($fieldsItem->texto), 0, 160, "...");?></p>
                            <a href="<?= base_url('Blog').'/'.$fieldsItem->id ;?>" class="btn btn-primary rounded-pill btn-sm fw-bold">Ler notícia</a>
                        </div>
                    </div>
                </div>
                <?php endforeach;?>
                <?= $pager->links('default', 'default_site') ?>  
            </div>
        </div>
    </div>
</main>
