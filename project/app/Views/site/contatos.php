<div class="container d-none d-xl-block largura1300">
	<div class="row">
		<div class="col-lg-12">
			

			<?= $page->texto ?>
			
		
		</div>
	</div>
</div>


<div class="container">
	<div class="row px-3">
		<div class="col-md-12 bg-dark">
			<h3 class="bg-dark p-3 text-white">FALE CONOSCO OU AGENDE SUA VISITA</h3>
		</div>
	</div>
	<div class="row px-3">
		<div class="col-md-12" style="background-color: #DEE2E2;">
			<div class=" p-3">
				<form>
					<div class="form-group">
						<label for="nome" class="h5 text-secondary">Nome:</label>
						<input type="text" class="form-control" name="nome">
					</div>
					<div class="form-group">
						<label for="telefone" class="h5 text-secondary">Telefone:</label>
						<input type="text" class="form-control" name="telefone">
					</div>
					<div class="form-group">
						<label for="email" class="h5 text-secondary">E-mail:</label>
						<input type="email" class="form-control" name="email">
					</div>
					<div class="form-group">
						<label for="mensagem" class="h5 text-secondary">Mensagem:</label>
						<textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
					</div>
					<button type="submit" class="btn btn-secondary rounded-0 float-right my-3"><span class="h5">ENVIAR</span> </button>
				</form>
			</div>
		
		</div>
	</div>
</div>


<?php include('dia-portal.php');?>