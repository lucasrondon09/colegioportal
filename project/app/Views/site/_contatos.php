
<main>
    <div class="container">
        <div class="row d-flex justify-content-center py-5">
            <div class="col-md-6">
                <h1 class="text-center text-primary mb-0 fw-bold custom-h1">CONTATOS</h1>
                <h6 class="text-center text-secondary text-uppercase mb-4">Preencha o formulário abaixo</h6>
                    <?= form_open(base_url('Contatos')) ?>
                    <div class="form-floating mb-3">
                    <input type="text" class="form-control border-primary" id="nome" name="nome" placeholder="Nome" required>
                    <label for="floatingInput"><span class="fw-bold text-primary">Nome</span></label>
                    </div>
                    <div class="form-floating mb-3">
                    <input type="text" class="form-control border-primary" id="telefone" name="telefone" placeholder="Telefone" required>
                    <label for="floatingInput"><span class="fw-bold text-primary">Telefone</span></label>
                    </div>
                    <div class="form-floating mb-3">
                    <input type="email" class="form-control border-primary" id="email" name="email" placeholder="Email" required>
                    <label for="floatingInput"><span class="fw-bold text-primary">E-mail</span></label>
                    </div>
                    <div class="form-floating mb-3">
                    <select class="form-select border-primary"  id="assunto" name="assunto" required>
                        <option selected disabled>Selecione</option>
                        <option value="Informações">Informações</option>
                        <option value="Sugestões">Sugestões</option>
                        <option value="Reclamações">Reclamações</option>
                    </select>
                    <label><span class="fw-bold text-primary">Motivo do contato</span></label>
                    </div>
                    <div class="form-floating mb-3">
                    <textarea class="form-control border-primary" id="mensagem" name="mensagem" style="height: 100px" placeholder="Mensagem" required></textarea>
                    <label for="floatingTextarea2"><span class="fw-bold text-primary">Mensagem</span></label>
                    </div>
                    <button type="submit" class="btn btn-primary rounded-pill w-100">Enviar</button>
                    <?= form_close() ?>
            </div>
        </div>
        <section id="Localizacao">
            <div class="row d-flex justify-content-center">
                <div class="col-12"><h1 class="text-center text-primary mb-0 fw-bold custom-h1 mt-5">LOCALIZAÇÃO</h1>
                <h6 class="text-center text-secondary text-uppercase mb-4">
                Edíficio SB Tower <br>
                Av. Historiador Rubens de Mendonça, nº 1756 - sl. 1305/1306 <br>
                Alvorada, Cuiabá - MT, 78048-340 <br>
                </h6>
            </div>
            </div>
        </section>
    </div>
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3843.0735048833503!2d-56.08564528555883!3d-15.587721122057202!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x939dad6122752e2d%3A0x3bf1a42f9690eb5a!2sAxons%20Centro%20Especializado%20em%20Neurologia%20e%20Neurofisiologia%20de%20MT!5e0!3m2!1spt-BR!2sbr!4v1674484470348!5m2!1spt-BR!2sbr" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</main>
