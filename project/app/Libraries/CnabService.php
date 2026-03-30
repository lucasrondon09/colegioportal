<?php

namespace App\Libraries;

use Exception;
use Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab240\Banco\Caixa as RemessaCaixa;
use Eduardokum\LaravelBoleto\Cnab\Retorno\Cnab240\Banco\Caixa as RetornoCaixa;
use Eduardokum\LaravelBoleto\Pessoa;
use App\Models\SI_CnabRemessaModel;
use App\Models\SI_CnabRemessaDetalheModel;
use App\Models\SI_CnabRetornoModel;
use App\Models\SI_CnabRetornoDetalheModel;
use App\Models\SI_CnabLogModel;
use App\Models\SI_ParcelasContratoModel;

/**
 * Serviço CNAB 240
 * 
 * Responsável pela geração de arquivos de remessa e processamento de retorno
 * para comunicação com a Caixa Econômica Federal
 */
class CnabService
{
    private $config;
    private $db;
    private $remessaModel;
    private $remessaDetalheModel;
    private $retornoModel;
    private $retornoDetalheModel;
    private $logModel;
    private $parcelasModel;

    public function __construct()
    {
        // Inicializar conexão com banco de dados
        $this->db = \Config\Database::connect();
        
        // Inicializar models
        $this->remessaModel = new SI_CnabRemessaModel();
        $this->remessaDetalheModel = new SI_CnabRemessaDetalheModel();
        $this->retornoModel = new SI_CnabRetornoModel();
        $this->retornoDetalheModel = new SI_CnabRetornoDetalheModel();
        $this->logModel = new SI_CnabLogModel();
        $this->parcelasModel = new SI_ParcelasContratoModel();
        
        // Carregar configurações do .env
        $this->config = [
            'codigo_banco'           => getenv('caixa.codigo_banco') ?: '104',
            'razao_social'           => getenv('caixa.razao_social') ?: 'SOCIEDADE EDUC PORTAL DO ITALIA LTDA',
            'numero_inscricao'       => getenv('caixa.numero_inscricao') ?: '00976721000131',
            'agencia'                => getenv('caixa.agencia') ?: '1681',
            'agencia_dv'             => getenv('caixa.agencia_dv') ?: '0',
            'conta'                  => getenv('caixa.conta') ?: '000000',
            'conta_dv'               => getenv('caixa.conta_dv') ?: '0',
            'codigo_beneficiario'    => getenv('caixa.codigo_beneficiario') ?: '592947',
            'codigo_beneficiario_dv' => getenv('caixa.codigo_beneficiario_dv') ?: '0',
            'carteira'               => getenv('caixa.carteira') ?: 'RG',
            'dir_remessas'           => WRITEPATH . (getenv('caixa.dir_remessas') ?: 'remessas/'),
            'dir_retornos'           => WRITEPATH . (getenv('caixa.dir_retornos') ?: 'retornos/'),
        ];

        // Criar diretórios se não existirem
        $this->criarDiretorios();
    }

    /**
     * Criar diretórios necessários
     */
    private function criarDiretorios()
    {
        if (!is_dir($this->config['dir_remessas'])) {
            mkdir($this->config['dir_remessas'], 0755, true);
        }
        if (!is_dir($this->config['dir_retornos'])) {
            mkdir($this->config['dir_retornos'], 0755, true);
        }
    }

    /**
     * Gerar arquivo de remessa CNAB 240
     * 
     * @param array $idsParcelas IDs das parcelas a serem incluídas na remessa
     * @param int|null $usuarioId ID do usuário que está gerando
     * @return array Resultado com sucesso e dados da remessa
     */
    public function gerarRemessa(array $idsParcelas, ?int $usuarioId = null): array
    {
        try {
            // Validar se há parcelas
            if (empty($idsParcelas)) {
                throw new Exception('Nenhuma parcela selecionada para remessa');
            }

            // Buscar parcelas com boletos gerados
            $parcelas = $this->parcelasModel
                ->whereIn('id', $idsParcelas)
                ->where('boleto_gerado', 1)
                ->where('enviado_remessa', 0)
                ->findAll();

            if (empty($parcelas)) {
                throw new Exception('Nenhuma parcela válida encontrada. Verifique se os boletos foram gerados e não foram enviados anteriormente.');
            }

            // Obter próximo número de remessa
            $numeroRemessa = $this->remessaModel->getProximoNumeroRemessa();

            // Criar objeto beneficiário
            $beneficiario = new Pessoa([
                'nome'      => $this->config['razao_social'],
                'endereco'  => 'Rua Exemplo, 123',
                'bairro'    => 'Centro',
                'cep'       => '00000000',
                'uf'        => 'SP',
                'cidade'    => 'São Paulo',
                'documento' => $this->config['numero_inscricao'],
            ]);

            // Criar objeto de remessa
            $remessa = new RemessaCaixa([
                'agencia'              => $this->config['agencia'],
                'agenciaDv'            => $this->config['agencia_dv'],
                'conta'                => $this->config['conta'],
                'contaDv'              => $this->config['conta_dv'],
                'codigoCliente'        => $this->config['codigo_beneficiario'],
                'carteira'             => $this->config['carteira'],
                'beneficiario'         => $beneficiario,
                'idremessa'            => $numeroRemessa,
            ]);

            // Adicionar boletos à remessa
            $totalRegistros = 0;
            $valorTotal = 0;
            $detalhesRemessa = [];

            foreach ($parcelas as $index => $parcela) {
                // Buscar dados do pagador (aluno/responsável)
                $pagador = $this->buscarDadosPagador($parcela['id_contrato']);

                // Criar objeto pagador
                $pagadorObj = new Pessoa([
                    'nome'      => $pagador['nome'],
                    'endereco'  => $pagador['endereco'] ?? 'Não informado',
                    'bairro'    => $pagador['bairro'] ?? 'Não informado',
                    'cep'       => $pagador['cep'] ?? '00000000',
                    'uf'        => $pagador['uf'] ?? 'SP',
                    'cidade'    => $pagador['cidade'] ?? 'São Paulo',
                    'documento' => $pagador['cpf'] ?? '00000000000',
                ]);

                // Criar boleto para remessa
                $boleto = new \Eduardokum\LaravelBoleto\Boleto\Banco\Caixa([
                    'logo'                => FCPATH . 'assets/img/logo-caixa.png',
                    'dataVencimento'      => new \Carbon\Carbon($parcela['data_vencimento']),
                    'valor'               => (float) $parcela['valor_parcela'],
                    'multa'               => (float) ($parcela['multa_percentual'] ?? 2.00),
                    'juros'               => (float) ($parcela['juros_percentual'] ?? 1.00),
                    'numero'              => $parcela['id'],
                    'numeroDocumento'     => str_pad($parcela['id'], 10, '0', STR_PAD_LEFT),
                    'pagador'             => $pagadorObj,
                    'beneficiario'        => $beneficiario,
                    'carteira'            => $this->config['carteira'],
                    'agencia'             => $this->config['agencia'],
                    'agenciaDv'           => $this->config['agencia_dv'],
                    'conta'               => $this->config['conta'],
                    'contaDv'             => $this->config['conta_dv'],
                    'codigoCliente'       => $this->config['codigo_beneficiario'],
                    'descricaoDemonstrativo' => ['Mensalidade escolar - Parcela ' . $parcela['numero_parcela']],
                    'instrucoes'          => $this->gerarInstrucoes($parcela),
                    'aceite'              => $parcela['aceite'] ?? 'N',
                    'especieDoc'          => $parcela['tipo_titulo'] ?? 'DM',
                ]);

                // Adicionar boleto à remessa
                $remessa->addBoleto($boleto);

                // Preparar dados para salvar no detalhe
                $detalhesRemessa[] = [
                    'id_parcela'          => $parcela['id'],
                    'nosso_numero'        => $boleto->getNossoNumero(),
                    'valor'               => $parcela['valor_parcela'],
                    'vencimento'          => $parcela['data_vencimento'],
                    'sequencial_registro' => $index + 1,
                    'status_envio'        => 'pendente',
                ];

                $totalRegistros++;
                $valorTotal += (float) $parcela['valor_parcela'];
            }

            // Gerar arquivo
            $nomeArquivo = 'CB' . date('dmY') . '_' . str_pad($numeroRemessa, 6, '0', STR_PAD_LEFT) . '.REM';
            $caminhoCompleto = $this->config['dir_remessas'] . $nomeArquivo;
            
            $remessa->save($caminhoCompleto);

            // Pós-processar arquivo para garantir 240 caracteres por linha e corrigir campos
            $this->corrigirLayoutCnab240($caminhoCompleto, $detalhesRemessa);

            // Salvar remessa no banco
            $idRemessa = $this->remessaModel->insert([
                'numero_remessa'  => $numeroRemessa,
                'data_geracao'    => date('Y-m-d H:i:s'),
                'arquivo_nome'    => $nomeArquivo,
                'arquivo_path'    => $caminhoCompleto,
                'total_registros' => $totalRegistros,
                'valor_total'     => $valorTotal,
                'status'          => 'gerado',
                'usuario_id'      => $usuarioId,
            ]);

            // Adicionar id_remessa aos detalhes e salvar
            foreach ($detalhesRemessa as &$detalhe) {
                $detalhe['id_remessa'] = $idRemessa;
            }
            $this->remessaDetalheModel->inserirLote($detalhesRemessa);

            // Atualizar parcelas como enviadas
            foreach ($parcelas as $parcela) {
                $this->parcelasModel->update($parcela['id'], [
                    'enviado_remessa'     => 1,
                    'data_envio_remessa'  => date('Y-m-d H:i:s'),
                    'id_remessa'          => $idRemessa,
                ]);
            }

            // Registrar log
            $this->logModel->logRemessa(
                'gerar_remessa',
                "Remessa #{$numeroRemessa} gerada com {$totalRegistros} boletos no valor total de R$ " . number_format($valorTotal, 2, ',', '.'),
                $idRemessa,
                $usuarioId
            );

            return [
                'sucesso'         => true,
                'mensagem'        => 'Remessa gerada com sucesso!',
                'id_remessa'      => $idRemessa,
                'numero_remessa'  => $numeroRemessa,
                'arquivo'         => $nomeArquivo,
                'caminho'         => $caminhoCompleto,
                'total_registros' => $totalRegistros,
                'valor_total'     => $valorTotal,
            ];

        } catch (Exception $e) {
            // Registrar erro
            $this->logModel->logErro(
                'gerar_remessa',
                'Erro ao gerar remessa: ' . $e->getMessage(),
                $usuarioId
            );

            return [
                'sucesso'  => false,
                'mensagem' => 'Erro ao gerar remessa: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Processar arquivo de retorno CNAB 240
     * 
     * @param string $caminhoArquivo Caminho do arquivo de retorno
     * @param int|null $usuarioId ID do usuário que está processando
     * @return array Resultado com sucesso e dados do retorno
     */
    public function processarRetorno(string $caminhoArquivo, ?int $usuarioId = null): array
    {
        try {
            // Validar arquivo
            if (!file_exists($caminhoArquivo)) {
                throw new Exception('Arquivo não encontrado');
            }

            // Calcular hash do arquivo para evitar reprocessamento
            $hashArquivo = md5_file($caminhoArquivo);
            
            if ($this->retornoModel->arquivoJaProcessado($hashArquivo)) {
                throw new Exception('Este arquivo já foi processado anteriormente');
            }

            // Processar arquivo de retorno
            $retorno = new RetornoCaixa($caminhoArquivo);
            $retorno->processar();

            // Preparar dados do retorno
            $nomeArquivo = basename($caminhoArquivo);
            $dataProcessamento = date('Y-m-d H:i:s');
            
            $totalLiquidados = 0;
            $totalBaixados = 0;
            $totalRejeitados = 0;
            $valorTotalLiquidado = 0;

            // Salvar retorno no banco
            $idRetorno = $this->retornoModel->insert([
                'data_processamento'  => $dataProcessamento,
                'arquivo_nome'        => $nomeArquivo,
                'arquivo_path'        => $caminhoArquivo,
                'total_registros'     => 0, // Será atualizado depois
                'total_liquidados'    => 0,
                'total_baixados'      => 0,
                'total_rejeitados'    => 0,
                'valor_total_liquidado' => 0,
                'status'              => 'processando',
                'usuario_id'          => $usuarioId,
                'hash_arquivo'        => $hashArquivo,
            ]);

            // Processar detalhes
            $detalhesRetorno = [];
            $totalRegistros = 0;

            foreach ($retorno->getDetalhes() as $detalhe) {
                $nossoNumero = $detalhe->getNossoNumero();
                $codigoOcorrencia = $detalhe->getOcorrencia();
                $descricaoOcorrencia = $detalhe->getOcorrenciaDescricao();

                // Buscar parcela pelo nosso número
                // O retorno da Caixa tem 17 dígitos, mas o banco salva 18 dígitos
                // Exemplo: Retorno = "43000000000000166" | Banco = "143000000000000166"
                
                // DEBUG: Log do nosso número recebido
                log_message('debug', "[CNAB RETORNO] Nosso Número recebido: '{$nossoNumero}' (" . strlen($nossoNumero) . " dígitos)");
                
                // Método 1: Buscar com nosso número original (17 dígitos do retorno)
                $parcela = $this->parcelasModel->where('nosso_numero', $nossoNumero)->first();
                if ($parcela) {
                    log_message('debug', "[CNAB RETORNO] Parcela encontrada no Método 1 (busca direta): ID {$parcela['id']}");
                }
                
                // Método 2: Adicionar "1" no início (converter 17 para 18 dígitos)
                if (!$parcela) {
                    $nossoNumero18Digitos = '1' . $nossoNumero;
                    log_message('debug', "[CNAB RETORNO] Tentando Método 2: '{$nossoNumero18Digitos}'");
                    $parcela = $this->parcelasModel->where('nosso_numero', $nossoNumero18Digitos)->first();
                    if ($parcela) {
                        log_message('debug', "[CNAB RETORNO] Parcela encontrada no Método 2 (com '1' no início): ID {$parcela['id']}");
                    }
                }
                
                // Método 3: Buscar usando LIKE com os últimos dígitos (ID da parcela)
                if (!$parcela) {
                    // Extrair ID da parcela (últimos 3-5 dígitos)
                    $idParcela = (int)substr($nossoNumero, -5);
                    if ($idParcela > 0) {
                        $busca = '%' . str_pad($idParcela, 3, '0', STR_PAD_LEFT);
                        log_message('debug', "[CNAB RETORNO] Tentando Método 3 (LIKE): '{$busca}'");
                        $parcela = $this->parcelasModel->where('nosso_numero', 'LIKE', $busca)->first();
                        if ($parcela) {
                            log_message('debug', "[CNAB RETORNO] Parcela encontrada no Método 3 (LIKE): ID {$parcela['id']}");
                        }
                    }
                }
                
                // Método 4: Buscar diretamente pelo ID se os métodos anteriores falharem
                if (!$parcela) {
                    $idParcela = (int)substr($nossoNumero, -5);
                    if ($idParcela > 0) {
                        log_message('debug', "[CNAB RETORNO] Tentando Método 4 (busca por ID): {$idParcela}");
                        $parcela = $this->parcelasModel->find($idParcela);
                        if ($parcela) {
                            log_message('debug', "[CNAB RETORNO] Parcela encontrada no Método 4 (ID direto): ID {$parcela['id']}");
                        }
                    }
                }
                
                // DEBUG: Log se não encontrou
                if (!$parcela) {
                    log_message('warning', "[CNAB RETORNO] PARCELA NÃO ENCONTRADA para nosso número: '{$nossoNumero}'");
                }

                $detalhesRetorno[] = [
                    'id_retorno'          => $idRetorno,
                    'id_parcela'          => $parcela['id'] ?? null,
                    'nosso_numero'        => $nossoNumero,
                    'seu_numero'          => $detalhe->getNumeroDocumento(),
                    'codigo_ocorrencia'   => $codigoOcorrencia,
                    'descricao_ocorrencia' => $descricaoOcorrencia,
                    'data_ocorrencia'     => $this->formatarData($detalhe->getDataOcorrencia()),
                    'data_credito'        => $this->formatarData($detalhe->getDataCredito()),
                    'valor_titulo'        => $detalhe->getValor(),
                    'valor_pago'          => $detalhe->getValorRecebido(),
                    'valor_tarifa'        => $detalhe->getValorTarifa(),
                    'valor_juros'         => $detalhe->getValorMora(),
                    'valor_multa'         => $detalhe->getValorMulta(),
                    'valor_desconto'      => $detalhe->getValorDesconto(),
                    'processado'          => 0,
                ];

                // Contar por tipo de ocorrência
                if ($codigoOcorrencia == '06') { // Liquidação
                    $totalLiquidados++;
                    $valorTotalLiquidado += (float) $detalhe->getValorRecebido();
                } elseif ($codigoOcorrencia == '09') { // Baixa
                    $totalBaixados++;
                } elseif (in_array($codigoOcorrencia, ['02', '03', '26', '30'])) { // Rejeições
                    $totalRejeitados++;
                }

                $totalRegistros++;
            }

            // Salvar detalhes
            if (!empty($detalhesRetorno)) {
                $this->retornoDetalheModel->inserirLote($detalhesRetorno);
            }

            // Atualizar totalizadores do retorno
            $this->retornoModel->atualizarTotalizadores($idRetorno, [
                'total_registros'       => $totalRegistros,
                'total_liquidados'      => $totalLiquidados,
                'total_baixados'        => $totalBaixados,
                'total_rejeitados'      => $totalRejeitados,
                'valor_total_liquidado' => $valorTotalLiquidado,
                'status'                => 'processado',
            ]);

            // Processar ocorrências (atualizar status das parcelas)
            $this->processarOcorrencias($idRetorno);

            // Registrar log
            $this->logModel->logRetorno(
                'processar_retorno',
                "Retorno processado com {$totalRegistros} registros. Liquidados: {$totalLiquidados}, Baixados: {$totalBaixados}, Rejeitados: {$totalRejeitados}",
                $idRetorno,
                $usuarioId
            );

            return [
                'sucesso'               => true,
                'mensagem'              => 'Retorno processado com sucesso!',
                'id_retorno'            => $idRetorno,
                'total_registros'       => $totalRegistros,
                'total_liquidados'      => $totalLiquidados,
                'total_baixados'        => $totalBaixados,
                'total_rejeitados'      => $totalRejeitados,
                'valor_total_liquidado' => $valorTotalLiquidado,
            ];

        } catch (Exception $e) {
            // Registrar erro
            $this->logModel->logErro(
                'processar_retorno',
                'Erro ao processar retorno: ' . $e->getMessage(),
                $usuarioId
            );

            return [
                'sucesso'  => false,
                'mensagem' => 'Erro ao processar retorno: ' . $e->getMessage() . ' | Arquivo: ' . $e->getFile() . ' | Linha: ' . $e->getLine(),
            ];
        }
    }

    /**
     * Processar ocorrências do retorno e atualizar parcelas
     */
    private function processarOcorrencias(int $idRetorno)
    {
        $detalhes = $this->retornoDetalheModel->getDetalhesNaoProcessados($idRetorno);

        foreach ($detalhes as $detalhe) {
            try {
                if ($detalhe['id_parcela'] === null) {
                    // Parcela não encontrada
                    $this->retornoDetalheModel->registrarErro(
                        $detalhe['id'],
                        'Parcela não encontrada pelo nosso número'
                    );
                    continue;
                }

                $codigoOcorrencia = $detalhe['codigo_ocorrencia'];

                // Processar conforme código de ocorrência
                switch ($codigoOcorrencia) {
                    case '06': // Liquidação
                        $this->parcelasModel->update($detalhe['id_parcela'], [
                            'status'          => 'pago',
                            'data_pagamento'  => $detalhe['data_ocorrencia'],
                            'valor_pago'      => $detalhe['valor_pago'],
                        ]);
                        break;

                    case '09': // Baixa
                        $this->parcelasModel->update($detalhe['id_parcela'], [
                            'status' => 'cancelado',
                        ]);
                        break;

                    case '02': // Entrada confirmada
                    case '03': // Entrada rejeitada
                        // Apenas registrar, não alterar status
                        break;

                    default:
                        // Outras ocorrências
                        break;
                }

                // Marcar como processado
                $this->retornoDetalheModel->marcarComoProcessado($detalhe['id']);

            } catch (Exception $e) {
                // Registrar erro no processamento
                $this->retornoDetalheModel->registrarErro(
                    $detalhe['id'],
                    'Erro ao processar: ' . $e->getMessage()
                );
            }
        }
    }

    /**
     * Buscar dados do pagador
     */
    private function buscarDadosPagador(int $idContrato): array
    {
        // Por enquanto, retornar dados padrão
        // TODO: Integrar com tabela de alunos/responsáveis quando disponível
        return [
            'nome'     => 'Pagador - Contrato #' . $idContrato,
            'cpf'      => '00000000000',
            'endereco' => 'Rua Exemplo, 123',
            'bairro'   => 'Centro',
            'cep'      => '00000000',
            'uf'       => 'SP',
            'cidade'   => 'São Paulo',
        ];
    }

    /**
     * Gerar instruções do boleto
     */
    private function gerarInstrucoes(array $parcela): array
    {
        $instrucoes = [];

        // Juros
        if (!empty($parcela['juros_percentual']) && $parcela['juros_percentual'] > 0) {
            $instrucoes[] = "APÓS VENCIMENTO COBRAR JUROS DE {$parcela['juros_percentual']}% AO MÊS";
        }

        // Multa
        if (!empty($parcela['multa_percentual']) && $parcela['multa_percentual'] > 0) {
            $multa_apos = $parcela['multa_apos_dias'] ?? 1;
            $instrucoes[] = "APÓS {$multa_apos} DIA(S) COBRAR MULTA DE {$parcela['multa_percentual']}%";
        }

        // Não receber após
        if (!empty($parcela['nao_receber_apos_dias']) && $parcela['nao_receber_apos_dias'] > 0) {
            $instrucoes[] = "NÃO RECEBER APÓS {$parcela['nao_receber_apos_dias']} DIAS DE ATRASO";
        }

        // Protestar após
        if (!empty($parcela['protestar_apos_dias']) && $parcela['protestar_apos_dias'] > 0) {
            $instrucoes[] = "PROTESTAR APÓS {$parcela['protestar_apos_dias']} DIAS DO VENCIMENTO";
        }

        // Mensagem personalizada
        if (!empty($parcela['mensagem_banco'])) {
            $instrucoes[] = $parcela['mensagem_banco'];
        }

        return $instrucoes;
    }

    /**
     * Formatar data para Y-m-d
     * Aceita DateTime, Carbon ou string
     */
    private function formatarData($data): ?string
    {
        if (empty($data)) {
            return null;
        }

        // Se já é um objeto DateTime/Carbon
        if ($data instanceof \DateTime || $data instanceof \Carbon\Carbon) {
            return $data->format('Y-m-d');
        }

        // Se é string, tentar converter
        if (is_string($data)) {
            try {
                // Formato DDMMYYYY (CNAB 240)
                if (strlen($data) === 8 && ctype_digit($data)) {
                    $dia = substr($data, 0, 2);
                    $mes = substr($data, 2, 2);
                    $ano = substr($data, 4, 4);
                    return "$ano-$mes-$dia";
                }
                
                // Outros formatos
                $timestamp = strtotime($data);
                if ($timestamp !== false) {
                    return date('Y-m-d', $timestamp);
                }
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * Corrigir layout CNAB 240 para garantir 240 caracteres por linha
     * 
     * @param string $caminhoArquivo
     * @return void
     */
    private function corrigirLayoutCnab240(string $caminhoArquivo, array $boletos): void
    {
        // Ler arquivo
        $conteudo = file_get_contents($caminhoArquivo);
        
        // Separar linhas
        $linhas = explode("\n", $conteudo);
        
        // Corrigir cada linha
        $linhasCorrigidas = [];
        $indiceBoleto = 0;
        
        foreach ($linhas as $linha) {
            // Remover quebras de linha
            $linha = rtrim($linha, "\r\n");
            
            // Pular linhas vazias
            if (empty($linha)) {
                continue;
            }
            
            // Identificar tipo de registro (posição 8)
            $tipoRegistro = isset($linha[7]) ? $linha[7] : '';
            $segmento = isset($linha[13]) ? $linha[13] : '';
            
            // Se for Segmento P, corrigir campos
            if ($tipoRegistro == '3' && $segmento == 'P' && isset($boletos[$indiceBoleto])) {
                $boleto = $boletos[$indiceBoleto];
                
                // Corrigir data de vencimento (posições 74-81, índices 73-80)
                $vencimento = date('dmY', strtotime($boleto['vencimento']));
                $linha = substr_replace($linha, $vencimento, 73, 8);
                
                // Corrigir valor (posições 87-101, índices 86-100)
                $valor = str_pad(number_format($boleto['valor'] * 100, 0, '', ''), 15, '0', STR_PAD_LEFT);
                $linha = substr_replace($linha, $valor, 86, 15);
                
                $indiceBoleto++;
            }
            
            // Ajustar para 240 caracteres
            if (strlen($linha) < 240) {
                $linha = str_pad($linha, 240, ' ', STR_PAD_RIGHT);
            } elseif (strlen($linha) > 240) {
                $linha = substr($linha, 0, 240);
            }
            
            $linhasCorrigidas[] = $linha;
        }
        
        // Salvar arquivo corrigido
        file_put_contents($caminhoArquivo, implode("\n", $linhasCorrigidas) . "\n");
    }
}
