<div class="container  largura1300 d-none d-lg-block">	
	<div class="row">
		<div class="col-lg-12">
			<div class="position-relative">
				<div class="position-absolute top-0 start-0 shadow-lg">
					<div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel">
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
					</div>
				</div>
			</div>

			<div class="slides_portal end-0">
				<div id="carouselExampleControls" class="carousel slide carousel-fade" data-ride="carousel">
					<div class="carousel-inner">
							<?php 
							$active = 'active';
							foreach($img as $imgItem):?>
							<div class="carousel-item <?= $active?>">
								<img src="<?= base_url('uploads/img/'.$imgItem->imagem)?>" class="d-block w-100" alt="Colégio Portal">
							</div>
							<?php 
							$active = '';
							endforeach
							?>
					</div>
					<button class="carousel-control-prev" type="button" data-target="#carouselExampleControls" data-slide="prev">
						<span class="carousel-control-prev-icon" aria-hidden="true"></span>
						<span class="sr-only">Previous</span>
					</button>
					<button class="carousel-control-next" type="button" data-target="#carouselExampleControls" data-slide="next">
						<span class="carousel-control-next-icon" aria-hidden="true"></span>
						<span class="sr-only">Next</span>
					</button>
				</div>
			</div>
			<div class="video_portal">
				<iframe src="https://player.vimeo.com/video/18649349?h=d236466733&title=0&byline=0" width="640" height="180" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
			</div>
			

			<img src="<?= base_url()?>/assets/portal/img/banner_menu.png" alt="Imagem Portal" usemap="#Map" class="position-relative w-100 shadow-lg">
			<map name="Map">
				<area shape="rect" coords="0,100,165,338" href="<?= base_url('/Modalidades/educacao-infantil')?>" title="educacao_infantil">
				<area shape="rect" coords="200,200,426,472" href="<?= base_url('/Modalidades/ensino-fundamental-1')?>" title="fundamental_1">
				<area shape="rect" coords="300,400,688,599" href="<?= base_url('/Modalidades/ensino-fundamental-2')?>" title="fundamental_2">
				<area shape="rect" coords="300,200,600,800" href="<?= base_url()?>/Radar" title="radar">
			</map>
			<img src="<?= $educacao_infantil->capa?>" alt="Imagem Portal" class="triangle">
			<img src="<?= $fundamental_1->capa?>" alt="Imagem Portal" class="triangle-02">
			<img src="<?= $fundamental_2->capa?>" alt="Imagem Portal" class="triangle-03">

			
			
		
		</div>
	</div>
</div>

<div class="container-fluid d-lg-none">
	<div class="row">
		<div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel">
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
		</div>
	</div>
	<div class="row bg-white">
		<div class="col-12 mt-3">
			<a href="<?= base_url('/Modalidades/educacao-infantil')?>" class="btn btn-success w-100">Educação Infantil</a>
		</div>
		<div class="col-12 mt-3">
			<a href="<?= base_url('/Modalidades/ensino-fundamental-1')?>" class="btn btn-danger w-100">Ensino Fundamental I</a>
		</div>
		<div class="col-12 my-3">
			<a href="<?= base_url('/Modalidades/ensino-fundamental-2')?>" class="btn btn-primary w-100">Ensino Fundamental II</a>
		</div>
	</div>
</div>

<?php include('dia-portal.php');?>
