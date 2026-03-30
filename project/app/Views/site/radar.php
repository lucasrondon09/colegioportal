<div class="container d-none d-xl-block largura1300">
	<div class="row">
		<div class="col-lg-12">
			

			<?php
			
			foreach($radar as $radarItem){

				echo $radarItem->texto;

			}
			
			?>
			
		
		</div>
	</div>
</div>

<?php include('dia-portal.php');?>


