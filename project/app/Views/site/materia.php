
<main>
    <div class="bg-white my-5">
        <div class="container">
            <div class="row">
                <?php

                $materiaItem = $materia[0];
                

                ?>
                <div class="col-md-12">
                    <a href="javascript:history.back()" class="btn btn-sm btn-primary rounded-pill">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                    </svg>    
                    Voltar</a>
                    <p class="fw-bold mt-3 mb-0 text-primary"><?= $materiaItem->tituloCategoria;?></p>
                    <h1 class="mt-0">
                    <?= $materiaItem->titulo;?>
                    </h1>
                    <h3><?= $materiaItem->subtitulo;?></h3>
                    <p class="text-secondary mb-3">Data da publicação: <?= date('d/m/Y', strtotime($materiaItem->dataNoticia));?> <br/> Autor: <?= $materiaItem->nome;?></p>
                    <?= $materiaItem->texto;?>
                </div>
            </div>
            <div class="row mt-5">
                <h3 class="text-center">Últimas postagens</h3>
                <?php foreach($noticia as $noticiaItem):?>
                <div class="col-md-4">
                    <div class="card w-100 bg-light shadow border-0" >
                        <img src="<?= $noticiaItem->capa?>" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title"><?= $noticiaItem->tituloNoticia?></h5>
                            <p class="small fw-bold text-primary"><?= $noticiaItem->tituloCategoria?></p>
                            <p class="card-text"><?= mb_strimwidth(strip_tags($noticiaItem->texto), 0, 160, "...");?></p>
                            <a href="<?= base_url('Blog/materia').'/'.$noticiaItem->id?>" class="btn btn-primary rounded-pill btn-sm fw-bold">Leia mais</a>
                        </div>
                    </div>
                </div>
                <?php endforeach;?>
            </div>
        </div>
    </div>
</main>
