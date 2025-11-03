
$(document).ready(function () {
    function atualizarResumo() {
        let valorPago = parseFloat($('#valor_pago').val().replace('.', '').replace(',', '.')) || 0;
        let desconto = parseFloat($('#desconto_aplicado').val().replace('.', '').replace(',', '.')) || 0;
        let juros = parseFloat($('#juros_aplicado').val().replace('.', '').replace(',', '.')) || 0;
        let multa = parseFloat($('#multa_aplicada').val().replace('.', '').replace(',', '.')) || 0;

        let valorLiquido = valorPago - desconto + juros + multa;

        $('#resumo_valor_pago').text('R$ ' + valorPago.toLocaleString('pt-BR', { minimumFractionDigits: 2 }));
        $('#resumo_desconto').text('R$ ' + desconto.toLocaleString('pt-BR', { minimumFractionDigits: 2 }));
        $('#resumo_juros').text('R$ ' + juros.toLocaleString('pt-BR', { minimumFractionDigits: 2 }));
        $('#resumo_multa').text('R$ ' + multa.toLocaleString('pt-BR', { minimumFractionDigits: 2 }));
        $('#resumo_liquido').text('R$ ' + valorLiquido.toLocaleString('pt-BR', { minimumFractionDigits: 2 }));
    }

    // Atualizar quando a página carregar
    atualizarResumo();

    // Atualizar quando os campos mudarem
    $('#valor_pago, #desconto_aplicado, #juros_aplicado, #multa_aplicada').on('keyup change', function () {
        atualizarResumo();
    });
  

  
  // Função para converter string monetária para número
  function moneyToFloat(value) {
    if(!value) return 0;
    return parseFloat(value.replace(/\./g, '').replace(',', '.')) || 0;
  }
  
  // Função para formatar número para string monetária
  function formatMoney(value) {
    return value.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
  }
  
  // Atualizar resumo quando campos mudarem
  function atualizarResumo() {
    var valorPago = moneyToFloat($('#valor_pago').val());
    var desconto = moneyToFloat($('#desconto_aplicado').val());
    var juros = moneyToFloat($('#juros_aplicado').val());
    var multa = moneyToFloat($('#multa_aplicada').val());
    
    var valorLiquido = valorPago - desconto + juros + multa;
    
    $('#resumo_valor_pago').text('R$ ' + formatMoney(valorPago));
    $('#resumo_desconto').text('R$ ' + formatMoney(desconto));
    $('#resumo_juros').text('R$ ' + formatMoney(juros));
    $('#resumo_multa').text('R$ ' + formatMoney(multa));
    $('#resumo_liquido').text('R$ ' + formatMoney(valorLiquido));
    
    // Destacar se valor líquido for diferente do valor pago
    if(valorLiquido != valorPago) {
      $('#resumo_liquido').parent().addClass('font-weight-bold text-primary');
    } else {
      $('#resumo_liquido').parent().removeClass('font-weight-bold text-primary');
    }
  }
  
  // Atualizar resumo ao carregar página
  atualizarResumo();
  
  // Atualizar resumo quando campos mudarem
  $('#valor_pago, #desconto_aplicado, #juros_aplicado, #multa_aplicada').on('keyup change', function(){
    atualizarResumo();
  });
  
  
});