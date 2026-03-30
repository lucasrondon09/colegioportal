<div class="container">
	<div class="row px-3">
		<div class="col-md-12" style="background-color: #828D6B;">
			<div class="p-5">
				<h1 class="mt-3 text-white">CONHEÇA NOSSA ESTRUTURA</h1>
				<?php
				use App\Controllers\Site\Home;

 				foreach($galeria as $galeriaItem):?>
					<h3 class="text-white mt-5"><?= $galeriaItem->titulo?></h3>

					<div class="gallery">
						<?php
						$img = new Home;
						$imagens = $img->imagensGaleria($galeriaItem->id);

						foreach($imagens as $imgItem):?>
							<a href="<?= base_url('uploads/img/'.$imgItem->imagem) ?>" data-lightbox="gallery"><img src="<?= base_url('uploads/img/'.$imgItem->imagem) ?>" alt="<?= $galeriaItem->titulo ?>" height="120px" class="pr-2"></a>
						<?php endforeach; ?>
					</div>	
				<?php endforeach; ?>
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
