
<main>
    <div class="container">
        <div class="row d-flex justify-content-center py-5">
            <div class="col-md-6 d-flex align-items-center bg-white rounded text-center justify-content-center">
                <div class="">
                    <img src="<?= base_url('assets/site/img/agendamento.png')?>" alt="Agendamento de Consulta" class="img-fluid col-md-6">
                    <h3 class="text-primary mt-2 custom-h1">Agendamento de Exames</h3>
                    
                    <div class="">
                        <ul class="list-inline">
                            <li class="list-inline-item text-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/>
                                </svg>
                                (65)3622-1200
                            </li>
                            <li class="list-inline-item text-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                                    <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
                                </svg>
                                (65)99915-0805
                            </li>
                        </ul>
                        <h6 class="text-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16">
                            <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A31.493 31.493 0 0 1 8 14.58a31.481 31.481 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94zM8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z"/>
                            <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                            </svg>
                            Localização
                        </h6>
                        <p class="mt-0 pt-0 text-secondary small">
                        SB Tower, Av. Historiador Rubens de Mendonça, nº 1756 - sl. 1305/1306 <br>
                        Alvorada, Cuiabá - MT, 78048-340
                        </p>
                    </div>
                    <p class="text-danger small mt-2">Atendimento de segunda a sexta <br> das 08:00 às 18:00</p>
                </div>
            </div>
            <div class="col-md-6 px-4">
                <?= form_open(base_url('Contatos')) ?>
                <div class="mt-4">
                    <h3 class="text-primary">Agendamento online</h3>
                    <h5 class="text-secondary mb-3">Preencha o formulário abaixo</h5>
                    <div class="mb-3">
                        <label for="nome" class="form-label fw-bold text-secondary">Nome</label>
                        <input type="text" class="form-control border-primary" name="nome" required>
                        <input type="text" class="form-control" name="assunto" value="Agendamento de Exame" hidden>
                    </div>
                    <div class="mb-3">
                        <label for="telefone" class="form-label fw-bold text-secondary">Telefone</label>
                        <input type="text" class="form-control border-primary" id="telefone" name="telefone" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold text-secondary">E-mail</label>
                        <input type="email" class="form-control border-primary" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold text-secondary">Exame</label>
                        <select class="form-select border-primary"  id="exame" name="exame" required>
                        <option selected disabled>Selecione</option>
                        <?php foreach($servico as $servicoItem):?>
                        <option value="<?= $servicoItem->titulo?>"><?= $servicoItem->titulo?></option>
                        <?php endforeach;?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="mensagem" class="form-label fw-bold text-secondary">Mensagem</label>
                        <div class="form-floating">
                        <textarea class="form-control border-primary" placeholder="Escreva sua mensagem" name="mensagem" style="height: 100px" required></textarea>
                        <label for="floatingTextarea2">Escreva sua mensagem</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary rounded-pill w-100">Enviar</button>
                </div>
                <?= form_close() ?>
            </div> 
        </div>
    </div>
</main>
