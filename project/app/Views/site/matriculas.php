<div class="container">
	<div class="row">
		<div class="col-lg-12">
			

			<?= $page->texto ?>
			
		
		</div>
	</div>
</div>

<div class="container">
	<div class="row px-3">
		<div class="col-md-12 bg-dark">
			<h3 class="bg-dark p-3 text-white">RESERVA DE VAGAS</h3>
		</div>
	</div>
	<div class="row px-3">
		<div class="col-md-12" style="background-color: #DEE2E2;">
			<div class="p-3">
				<form>
					<div class="form-group">
						<label for="nomeAluno" class="h5 text-secondary">Nome do Aluno:</label>
						<input type="email" class="form-control" name="nomeAluno">
					</div>
					<div class="row">
						<div class="col-12 col-lg-6">
						<label for="nomeAluno" class="h5 text-secondary">Série:</label>
						<input type="text" class="form-control" name="serie">
						</div>
						<div class="col-12 col-lg-6">
						<label for="nomeAluno" class="h5 text-secondary">Data de nascimento do aluno:</label>
						<input type="date" class="form-control" name="dtNascimento">
						</div>
					</div>
					<div class="btn btn-sm btn-light rounded-pill text-primary my-3" id="btnIrmao">+ Aluno (irmão): </div>
					<div class="border border-white p-3 my-3" id="gridIrmao">
						<div class="form-group">
							<label for="nomeAluno" class="h5 text-secondary">Nome do Aluno:</label>
							<input type="email" class="form-control" name="nomeAluno">
						</div>
						<div class="row">
							<div class="col">
							<label for="nomeAluno" class="h5 text-secondary">Série:</label>
							<input type="text" class="form-control" name="serie">
							</div>
							<div class="col">
							<label for="nomeAluno" class="h5 text-secondary">Data de nascimento do aluno:</label>
							<input type="date" class="form-control" name="dtNascimento">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="nomeAluno" class="h5 text-secondary">Nome do responsável:</label>
						<input type="email" class="form-control" name="nomeResponsavel">
					</div>
					<div class="row">
						<div class="col">
						<label for="nomeAluno" class="h5 text-secondary">Celular:</label>
						<input type="text" class="form-control" name="celular">
						</div>
						<div class="col">
						<label for="nomeAluno" class="h5 text-secondary">E-mail:</label>
						<input type="text" class="form-control" name="email">
						</div>
					</div>
					<button type="submit" class="btn btn-secondary rounded-0 float-right my-3"><span class="h5">ENVIAR</span> </button>
				</form>
			</div>
		
		</div>
	</div>
</div>

<?php include('dia-portal.php');?>

<script>

$(document).ready(function() {
    var gridIrmao = $('#gridIrmao');
    gridIrmao.hide();

    $('#btnIrmao').click(function() {
        if (gridIrmao.is(':visible')) {
            gridIrmao.hide();
        } else {
            gridIrmao.show();
        }
    });
});



</script>

