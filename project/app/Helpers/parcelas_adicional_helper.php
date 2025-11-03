<?php
// ============================================================================
// FUNÇÕES ADICIONAIS PARA O HELPER parametros_helper.php
// Adicione estas funções ao arquivo existente: project/app/Helpers/parametros_helper.php
// ============================================================================

/**
 * Retorna o status da parcela formatado
 * 
 * @param int $status
 * @return string
 */
function statusParcela($status = null)
{
    $statuses = [
       '1' => 'Aberto',
       '2' => 'Pago',
       '3' => 'Pago Parcialmente',
       '4' => 'Atrasado',
    ];

    if ($status === null) {
        return $statuses;
    }   

    return $statuses[$status] ?? 'Desconhecido';
}

/**
 * Retorna o badge HTML do status da parcela
 * 
 * @param int $status
 * @return string
 */
function badgeStatusParcela($status)
{
    $badges = [
       '1' => '<span class="badge badge-secondary">Aberto</span>',
       '2' => '<span class="badge badge-success">Pago</span>',
       '3' => '<span class="badge badge-warning">Pago Parcialmente</span>',
       '4' => '<span class="badge badge-danger">Atrasado</span>',
    ];

    return $badges[$status] ?? '<span class="badge badge-secondary">Desconhecido</span>';
}

/**
 * Verifica se uma data está vencida
 * 
 * @param string $data_vencimento
 * @return bool
 */
function isVencido($data_vencimento)
{
    $hoje = date('Y-m-d');
    return $data_vencimento < $hoje;
}

/**
 * Calcula dias até o vencimento (negativo se vencido)
 * 
 * @param string $data_vencimento
 * @return int
 */
function diasAteVencimento($data_vencimento)
{
    $hoje = new DateTime();
    $vencimento = new DateTime($data_vencimento);
    $intervalo = $hoje->diff($vencimento);
    
    return $intervalo->invert ? -$intervalo->days : $intervalo->days;
}

/**
 * Retorna texto formatado de dias até vencimento
 * 
 * @param string $data_vencimento
 * @return string
 */
function textoVencimento($data_vencimento)
{
    $dias = diasAteVencimento($data_vencimento);
    
    if($dias < 0){
        $dias_abs = abs($dias);
        return "<span class='text-danger'>Vencido há {$dias_abs} dia(s)</span>";
    } elseif($dias == 0){
        return "<span class='text-warning'>Vence hoje</span>";
    } elseif($dias <= 7){
        return "<span class='text-warning'>Vence em {$dias} dia(s)</span>";
    } else {
        return "<span class='text-muted'>Vence em {$dias} dia(s)</span>";
    }
}

/**
 * Formata valor monetário com cor baseado no valor
 * 
 * @param float $valor
 * @param string $prefixo
 * @return string
 */
function monetarioExibirColorido($valor, $prefixo = 'R$ ')
{
    $valor_formatado = number_format($valor, 2, ',', '.');
    
    if($valor > 0){
        return "<span class='text-success'>{$prefixo}{$valor_formatado}</span>";
    } elseif($valor < 0){
        return "<span class='text-danger'>{$prefixo}{$valor_formatado}</span>";
    } else {
        return "<span class='text-muted'>{$prefixo}{$valor_formatado}</span>";
    }
}

/**
 * Calcula percentual de pagamento
 * 
 * @param float $valor_pago
 * @param float $valor_total
 * @return float
 */
function percentualPago($valor_pago, $valor_total)
{
    if($valor_total == 0){
        return 0;
    }
    
    return ($valor_pago / $valor_total) * 100;
}

/**
 * Retorna barra de progresso HTML
 * 
 * @param float $percentual
 * @return string
 */
function barraProgresso($percentual)
{
    $percentual = min(100, max(0, $percentual)); // Limitar entre 0 e 100
    $cor = 'bg-success';
    
    if($percentual < 30){
        $cor = 'bg-danger';
    } elseif($percentual < 70){
        $cor = 'bg-warning';
    }
    
    return "
    <div class='progress' style='height: 20px;'>
        <div class='progress-bar {$cor}' role='progressbar' style='width: {$percentual}%' aria-valuenow='{$percentual}' aria-valuemin='0' aria-valuemax='100'>
            " . number_format($percentual, 1) . "%
        </div>
    </div>";
}

