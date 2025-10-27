<style>

.img-container {
    display: inline-block;
}

.gallery img {
    display: block;
    max-height: 120px;
    width: auto;
}



</style>

<div class="container">
	<div class="row px-3">
		<div class="col-md-12" style="background-color: #828D6B;">
			<div class="p-5">
				<h1 class="mt-3 text-white">Galeria de Fotos</h1>
				
				<?php
				use App\Controllers\Site\Home;

				

				if(isset(session()->userId) and session()->sistema == 'sispai'):
					
					
					foreach($ano as $anoItem){
						echo "<a href='".base_url('/Galeria-de-Fotos/'.$anoItem->ano)."' class='btn btn-light mr-2'>".$anoItem->ano."</a>";
					}

					foreach($galeria as $galeriaItem): ?>

					<h3 class="text-white mt-4"><?= $galeriaItem->titulo?></h3>
						<div class="gallery-container text-center rounded mb-5">
							
					
							<div class="gallery d-flex flex-wrap justify-content-center">
								<?php
								$img = new Home;
								$imagens = $img->imagensGaleria($galeriaItem->id);
					
								foreach($imagens as $imgItem): ?>
									<a href="<?= base_url('uploads/img/'.$imgItem->imagem) ?>" data-lightbox="gallery" class="m-2 bg-dark">
										<div class="img-container">
											<img src="<?= base_url('uploads/img/'.$imgItem->imagem) ?>" alt="<?= $galeriaItem->titulo ?>" height="120px" class="img-fluid p-2">
										</div>
									</a>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>
					
					
				<?php else:?>
					<p class="text-white">
						Acesso restrito aos Pais. Favor fazer login no <a href="<?= base_url('/Admin/Autenticacao/login/sispai')?>" class="text-white font-weight-bold">SISPAI</a>
					</p>
				<?php endif;?>	

			</div>
		</div>
	</div>
</div>

<?php include('dia-portal.php');?>

<script>
    $(document).ready(function(){
        $('.gallery').slick({
			dots: true,
            infinite: false,
            slidesToShow: 6,
            slidesToScroll: 1,
            prevArrow: '<button type="button" class="slick-prev">Previous</button>',
            nextArrow: '<button type="button" class="slick-next">Next</button>',
            responsive: [
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });
    });
</script>
