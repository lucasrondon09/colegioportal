
<div class="container">
	<div class="row px-3">
		<div class="col-md-12" style="background-color: #E8E9E4;">
			<div class="p-5">
				<h1 class="mt-3 text-dark">MATERIAL</h1>
				<ul class="list-unstyled">
					<?php foreach($materiais as $materiaisItem):?>
						<li><a href="<?= base_url('/uploads/arquivo_download'.$materiaisItem->nome_arquivo)?>"><?= $materiaisItem->descricao?></a></li>
					<?php endforeach;?>	
				</ul>
			</div>
		</div>
	</div>
</div>

<?php include('dia-portal.php');?>

