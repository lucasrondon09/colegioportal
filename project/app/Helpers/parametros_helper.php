<?php

function getPeriodo($param)
{
    $periodos = [
       'm' => 'Matutino',
       'v' => 'Vespertino',
       'n' => 'Noturno',
    ];

    return $periodos[$param] ?? 'Desconhecido';

}


function tipoContrato($param = null)
{
    $tipos = [
       '1' => 'Matrícula',
       '2' => 'Matícula 2º Filho',
       '3' => 'Rematícula',
       '4' => 'Bolsista',
    ];

    if ($param === null) {
        return $tipos;
    }   

    return $tipos[$param] ?? 'Desconhecido';

}

function statusContrato($param = null)
{
    $status = [
       '1' => 'Aberto',
       '2' => 'Ativo',
       '3' => 'Concluído',
       '4' => 'Cancelado',
       '5' => 'Transferido',
       '6' => 'Suspenso',
       '7' => 'Inadimplente',
       '8' => 'Expirado',
    ];

    if ($param === null) {
        return $status;
    }   

    return $status[$param] ?? 'Desconhecido';

}

function monetarioExibir($valor)
{
    return number_format($valor, 2, ',', '.');
}

function monetarioSalvar($valor)
{
    $valor_formatado = number_format((float)preg_replace('/[^\d]/', '', $valor) / 100, 2, '.', '');

    return floatval($valor_formatado);
}

function dataBR($param)
{
    $date = date_create($param);
    return date_format($date, 'd/m/Y');

}