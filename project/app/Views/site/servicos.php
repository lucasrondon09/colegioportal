
<main>
    <div class="bg-white my-5">
        <div class="container">
            <div class="row">
                <div class="col-12 mb-3">
                    <h6 class="mb-0 text-primary border-start border-2 border-primary custom-h1"><span class="ms-2">EXAMES</span></h6>
                    <h1 class="text-secondary fw-bold">EXAMES REALIZADOS NA AXONS</h1>
                </div>
                <?php foreach($fields as $fieldsItem):?>
                <div class="col-md-4 mb-3">
                    <div class="card w-100 bg-light shadow border-0 h-100" >
                        <img src="<?= $fieldsItem->capa?>" class="card-img-top" alt="<?= $fieldsItem->titulo?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= $fieldsItem->titulo?></h5>
                            <a href="<?= base_url('Exames').'/'.$fieldsItem->id?>" class="btn btn-primary rounded-pill btn-sm fw-bold">Saiba mais</a>
                        </div>
                    </div>
                </div>
                <?php endforeach;?>
                <?= $pager->links('default', 'default_site') ?>    
            </div>
        </div>
    </div>
</main>
