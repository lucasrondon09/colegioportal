<?php

namespace App\Libraries;

use Exception;
use Eduardokum\LaravelBoleto\Boleto\Banco\Caixa;
use Eduardokum\LaravelBoleto\Pessoa;

/**
 * Serviço de Boletos Bancários
 * 
 * Responsável por toda a comunicação com a biblioteca de boletos
 * e geração de arquivos CNAB 240
 */
class BoletoService
{
    private $config;
    private $db;

    public function __construct()
    {
        // Inicializar conexão com banco de dados
        $this->db = \Config\Database::connect();
        
        // Carregar configurações do .env
        $this->config = [
            'codigo_banco' => getenv('caixa.codigo_banco') ?: '104',
            'razao_social' => getenv('caixa.razao_social') ?: 'SOCIEDADE EDUC PORTAL DO ITALIA LTDA',
            'numero_inscricao' => getenv('caixa.numero_inscricao') ?: '00976721000131',
            'agencia' => getenv('caixa.agencia') ?: '16810',
            'agencia_dv' => getenv('caixa.agencia_dv') ?: '0',
            'conta' => getenv('caixa.conta') ?: '000000',
            'conta_dv' => getenv('caixa.conta_dv') ?: '0',
            'codigo_beneficiario' => getenv('caixa.codigo_beneficiario') ?: '592947',
            'codigo_beneficiario_dv' => getenv('caixa.codigo_beneficiario_dv') ?: '0',
            'carteira' => getenv('caixa.carteira') ?: 'RG',
            'dir_remessas' => WRITEPATH . (getenv('caixa.dir_remessas') ?: 'remessas/'),
            'dir_retornos' => WRITEPATH . (getenv('caixa.dir_retornos') ?: 'retornos/'),
        ];
        
        // Carregar configurações de boleto do banco de dados
        $this->carregarConfiguracoesBoleto();

        // Criar diretórios se não existirem
        $this->criarDiretorios();
    }

    /**
     * Carregar configurações de boleto do banco de dados
     */
    private function carregarConfiguracoesBoleto()
    {
        try {
            $configBoleto = $this->db->table('si_boleto_config')
                ->where('ativo', 1)
                ->get()
                ->getRowArray();

            if ($configBoleto) {
                // Adicionar configurações de boleto ao array de config
                $this->config['boleto'] = $configBoleto;
                
                // Atualizar especie_doc e aceite com valores do banco
                $this->config['especie_doc'] = $configBoleto['tipo_titulo'];
                $this->config['aceite'] = $configBoleto['aceite'];
            } else {
                // Se não houver configuração, usar valores padrão
                $this->config['boleto'] = [
                    'juros_percentual' => 1.00,
                    'multa_percentual' => 2.00,
                    'multa_apos_dias' => 1,
                    'nao_receber_apos_dias' => 29,
                    'protestar_apos_dias' => 0,
                    'aceite' => 'N',
                    'tipo_titulo' => 'DM',
                    'mensagem_sacado' => null,
                    'mensagem_banco' => 'NÃO RECEBER APÓS 29 DIAS DE ATRASO',
                    'desconto_percentual' => 0.00,
                ];
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao carregar configurações de boleto: ' . $e->getMessage());
            // Usar valores padrão em caso de erro
            $this->config['boleto'] = [
                'juros_percentual' => 1.00,
                'multa_percentual' => 2.00,
                'multa_apos_dias' => 1,
                'nao_receber_apos_dias' => 29,
                'protestar_apos_dias' => 0,
                'aceite' => 'N',
                'tipo_titulo' => 'DM',
                'mensagem_sacado' => null,
                'mensagem_banco' => 'NÃO RECEBER APÓS 29 DIAS DE ATRASO',
                'desconto_percentual' => 0.00,
            ];
        }
    }

    /**
     * Criar diretórios necessários para arquivos CNAB
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
     * Gera os dados de um boleto individual
     * 
     * @param int $idParcela ID da parcela
     * @return array Dados formatados do boleto
     * @throws Exception
     */
    public function gerarBoletoIndividual(int $idParcela): array
    {
        try {
            // Buscar dados da parcela no banco
            $parcela = $this->buscarDadosParcela($idParcela);
            
            if (!$parcela) {
                throw new Exception('Parcela não encontrada');
            }

            // Buscar dados do pagador (responsável)
            $pagador = $this->buscarDadosPagador($parcela);

            // Criar objeto do beneficiário (escola)
            $beneficiario = new Pessoa([
                'nome' => $this->config['razao_social'],
                'endereco' => 'ALESSANDRIA, 5',
                'bairro' => 'JARDIM ITALIA',
                'cep' => '78060-820',
                'uf' => 'MT',
                'cidade' => 'CUIABA',
                'documento' => $this->config['numero_inscricao'],
            ]);

            // Criar objeto do pagador (responsável)
            $pagadorObj = new Pessoa([
                'nome' => $pagador['nome'],
                'endereco' => $pagador['endereco'] ?? '',
                'bairro' => $pagador['bairro'] ?? '',
                'cep' => $pagador['cep'] ?? '',
                'uf' => $pagador['uf'] ?? 'MT',
                'cidade' => $pagador['cidade'] ?? 'CUIABA',
                'documento' => $pagador['cpf_cnpj'] ?? '',
            ]);

            // Gerar nosso número (formato Caixa SIGCB)
            $nossoNumero = $this->gerarNossoNumero($idParcela);

            // Calcular valores
            $valorOriginal = floatval($parcela['valor_parcela']);
            $valorDesconto = floatval($parcela['valor_desconto'] ?? 0);
            $valorJuros = floatval($parcela['valor_juros'] ?? 0);
            $valorMulta = floatval($parcela['valor_multa'] ?? 0);
            $valorFinal = $valorOriginal - $valorDesconto + $valorJuros + $valorMulta;
            
            // Usar configurações da parcela (se existirem) ou da configuração global
            $configBoleto = [
                'juros_percentual' => $parcela['juros_percentual'] ?? $this->config['boleto']['juros_percentual'] ?? 1.00,
                'multa_percentual' => $parcela['multa_percentual'] ?? $this->config['boleto']['multa_percentual'] ?? 2.00,
                'multa_apos_dias' => $parcela['multa_apos_dias'] ?? $this->config['boleto']['multa_apos_dias'] ?? 1,
                'desconto_percentual' => $parcela['desconto_percentual'] ?? $this->config['boleto']['desconto_percentual'] ?? 0.00,
                'nao_receber_apos_dias' => $parcela['nao_receber_apos_dias'] ?? $this->config['boleto']['nao_receber_apos_dias'] ?? 29,
                'protestar_apos_dias' => $parcela['protestar_apos_dias'] ?? $this->config['boleto']['protestar_apos_dias'] ?? 0,
                'aceite' => $parcela['aceite'] ?? $this->config['boleto']['aceite'] ?? 'N',
                'tipo_titulo' => $parcela['tipo_titulo'] ?? $this->config['boleto']['tipo_titulo'] ?? 'DM',
                'mensagem_banco' => $parcela['mensagem_banco'] ?? $this->config['boleto']['mensagem_banco'] ?? 'DÚVIDAS ENTRE EM CONTATO COM O CEDENTE/FAVORECIDO',
            ];
            
            $multa = floatval($configBoleto['multa_percentual']); // Percentual
            $juros = floatval($configBoleto['juros_percentual']) / 30; // Converter % ao mês para % ao dia
            $desconto = floatval($configBoleto['desconto_percentual']);

            // Criar boleto da Caixa
            $boleto = new Caixa([
                // Dados do beneficiário
                'logo' => FCPATH . 'assets/admin/dist/img/logo_portal.png',
                'dataVencimento' => new \Carbon\Carbon($parcela['data_vencimento']),
                'valor' => $valorFinal,
                'multa' => $multa,
                'juros' => $juros,
                'numero' => $idParcela,
                'numeroDocumento' => str_pad($idParcela, 10, '0', STR_PAD_LEFT),
                'pagador' => $pagadorObj,
                'beneficiario' => $beneficiario,
                'carteira' => $this->config['carteira'],
                'agencia' => $this->config['agencia'],
                'agenciaDv' => $this->config['agencia_dv'],
                'conta' => $this->config['codigo_beneficiario'], // No SIGCB, usa código do beneficiário
                'contaDv' => $this->config['codigo_beneficiario_dv'],
                'codigoCliente' => $this->config['codigo_beneficiario'],
                'nossoNumero' => $nossoNumero,
                'aceite' => $configBoleto['aceite'],
                'especieDoc' => $configBoleto['tipo_titulo'],
                'descricaoDemonstrativo' => [
                    $parcela['descricao'] ?? 'Mensalidade escolar',
                    'Referente ao período: ' . date('m/Y', strtotime($parcela['data_vencimento'])),
                ],
                'instrucoes' => $this->gerarInstrucoes($parcela, $configBoleto, $valorFinal),
            ]);

            // Atualizar parcela no banco com dados do boleto
            $this->atualizarParcelaComBoleto($idParcela, [
                'nosso_numero' => $nossoNumero,
                'linha_digitavel' => $boleto->getLinhaDigitavel(),
                'codigo_barras' => $boleto->getCodigoBarras(),
                'boleto_gerado_em' => date('Y-m-d H:i:s'),
                'boleto_gerado' => 1,
                'enviado_remessa' => 0,
            ]);

            // Retornar dados do boleto
            return [
                'success' => true,
                'nosso_numero' => $nossoNumero,
                'linha_digitavel' => $boleto->getLinhaDigitavel(),
                'codigo_barras' => $boleto->getCodigoBarras(),
                'valor' => $valorFinal,
                'vencimento' => $parcela['data_vencimento'],
                'boleto_obj' => $boleto, // Objeto para renderização
                'parcela' => $parcela,
                'pagador' => $pagador,
            ];

        } catch (Exception $e) {
            log_message('error', 'Erro ao gerar boleto: ' . $e->getMessage());
            throw new Exception('Erro ao gerar boleto: ' . $e->getMessage());
        }
    }

    /**
     * Buscar dados da parcela no banco
     */
    private function buscarDadosParcela(int $idParcela)
    {
        try {
            $result = $this->db->table('si_parcelas_contrato')
                ->where('id', $idParcela)
                ->get()
                ->getRowArray();
            
            if (!$result) {
                log_message('error', "Parcela {$idParcela} não encontrada");
            }
            
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar parcela: ' . $e->getMessage());
            throw new Exception('Erro ao buscar dados da parcela: ' . $e->getMessage());
        }
    }

    /**
     * Buscar dados do pagador (responsável) relacionado à parcela
     */
    private function buscarDadosPagador($parcela)
    {
        // Buscar contrato
        $contrato = $this->db->table('si_contrato')
            ->where('id', $parcela['id_contrato'])
            ->get()
            ->getRowArray();

        if (!$contrato) {
            return [
                'nome' => 'RESPONSÁVEL NÃO CADASTRADO',
                'cpf_cnpj' => '',
                'endereco' => '',
                'bairro' => '',
                'cep' => '',
                'cidade' => 'CUIABA',
                'uf' => 'MT',
            ];
        }

        // Buscar responsável financeiro na tabela si_pai
        $responsavel = $this->db->table('si_pai')
            ->where('id', $contrato['id_responsavel'])
            ->get()
            ->getRowArray();

        if ($responsavel) {
            // Determinar qual responsável usar (financeiro, pai, mãe ou outro)
            $nome = $responsavel['rm_resp_financeiro_nome'] ?: 
                    ($responsavel['nome_pai'] ?: $responsavel['nome_mae']);
            $cpf = $responsavel['rm_resp_financeiro_cpf'] ?: 
                   ($responsavel['cpf_pai'] ?: $responsavel['cpf_mae']);
            $endereco = $responsavel['rm_resp_financeiro_endereco_correspondencia'] ?: 
                        ($responsavel['end_pai'] ?: $responsavel['end_mae']);
            $bairro = $responsavel['rm_resp_financeiro_bairro'] ?: 
                      ($responsavel['bairro_pai'] ?: $responsavel['bairro_mae']);
            $cep = $responsavel['rm_resp_financeiro_cep'] ?: '';
            $cidadeEstado = $responsavel['rm_resp_financeiro_cidade_estado'] ?: 
                           ($responsavel['cid_pai'] ?: $responsavel['cid_mae']);
            
            // Separar cidade e UF
            $cidade = 'CUIABA';
            $uf = 'MT';
            if ($cidadeEstado && strpos($cidadeEstado, '/') !== false) {
                list($cidade, $uf) = explode('/', $cidadeEstado);
                $cidade = trim($cidade);
                $uf = trim($uf);
            } elseif ($responsavel['uf_pai']) {
                $uf = $responsavel['uf_pai'];
            } elseif ($responsavel['uf_mae']) {
                $uf = $responsavel['uf_mae'];
            }
            
            return [
                'nome' => $nome ?: 'RESPONSÁVEL',
                'cpf_cnpj' => preg_replace('/[^0-9]/', '', $cpf),
                'endereco' => $endereco ?: '',
                'bairro' => $bairro ?: '',
                'cep' => preg_replace('/[^0-9]/', '', $cep),
                'cidade' => $cidade,
                'uf' => $uf,
            ];
        }

        return [
            'nome' => 'RESPONSÁVEL NÃO CADASTRADO',
            'cpf_cnpj' => '',
            'endereco' => '',
            'bairro' => '',
            'cep' => '',
            'cidade' => 'CUIABA',
            'uf' => 'MT',
        ];
    }

    /**
     * Atualizar parcela com dados do boleto gerado
     */
    private function atualizarParcelaComBoleto(int $idParcela, array $dados)
    {
        try {
            $affected = $this->db->table('si_parcelas_contrato')
                ->where('id', $idParcela)
                ->update($dados);
            
            if ($affected) {
                log_message('info', "Parcela {$idParcela} atualizada com dados do boleto");
            } else {
                log_message('warning', "Nenhuma linha afetada ao atualizar parcela {$idParcela}");
            }
            
            return $affected;
        } catch (\Exception $e) {
            log_message('error', 'Erro ao atualizar parcela com boleto: ' . $e->getMessage());
            throw new Exception('Erro ao salvar dados do boleto: ' . $e->getMessage());
        }
    }

    /**
     * Gera nosso número único para o boleto (formato Caixa SIGCB)
     * 
     * Formato: 143 + 15 dígitos sequenciais
     * Exemplo: 14300000000002766
     * 
     * @param int $idParcela ID da parcela
     * @return string Nosso número formatado (sem DV, a biblioteca calcula)
     */
    private function gerarNossoNumero(int $idParcela): string
    {
        // Prefixo 143 (modalidade Caixa SIGCB) + 15 dígitos do ID
        // A biblioteca adiciona o DV automaticamente
        return '143' . str_pad($idParcela, 15, '0', STR_PAD_LEFT);
    }

    /**
     * Renderiza boleto em HTML
     * 
     * @param object $boletoObj Objeto do boleto
     * @return string HTML do boleto
     */
    public function renderizarBoletoHTML($boletoObj): string
    {
        return $boletoObj->renderHTML();
    }

    /**
     * Renderiza boleto em PDF
     * 
     * @param object $boletoObj Objeto do boleto
     * @param string $caminhoSaida Caminho para salvar o PDF (opcional)
     * @return string PDF em base64 ou caminho do arquivo
     */
    public function renderizarBoletoPDF($boletoObj, string $caminhoSaida = null): string
    {
        if ($caminhoSaida) {
            return $boletoObj->renderPDF($caminhoSaida);
        }
        
        // Retornar PDF para download direto
        return $boletoObj->renderPDF();
    }

    /**
     * Gera instruções do boleto baseado nas configurações
     * 
     * @param array $parcela Dados da parcela
     * @param array $config Configurações de boleto
     * @param float $valorFinal Valor final do boleto
     * @return array Instruções formatadas
     */
    private function gerarInstrucoes(array $parcela, array $config, float $valorFinal): array
    {
        $instrucoes = [];
        
        // Instrução de não receber após X dias
        if ($config['nao_receber_apos_dias'] > 0) {
            $instrucoes[] = 'NÃO RECEBER APÓS ' . $config['nao_receber_apos_dias'] . ' DIAS DE ATRASO';
        }
        
        // Instrução de juros
        if ($config['juros_percentual'] > 0) {
            $instrucoes[] = 'JUROS: ' . number_format($config['juros_percentual'], 2, ',', '.') . '% AO MÊS (DIAS CORRIDOS) A PARTIR DO VENCIMENTO';
        }
        
        // Instrução de multa
        if ($config['multa_percentual'] > 0) {
            $valorMulta = ($valorFinal * $config['multa_percentual']) / 100;
            $dataMulta = date('d/m/Y', strtotime($parcela['data_vencimento'] . ' +' . $config['multa_apos_dias'] . ' day'));
            $instrucoes[] = 'MULTA: R$ ' . number_format($valorMulta, 2, ',', '.') . ' A PARTIR DE ' . $dataMulta;
        }
        
        // Instrução de desconto
        if ($config['desconto_percentual'] > 0) {
            $instrucoes[] = 'DESCONTO: ' . number_format($config['desconto_percentual'], 2, ',', '.') . '% ATÉ ' . date('d/m/Y', strtotime($parcela['data_vencimento']));
        }
        
        // Instrução de protesto
        if ($config['protestar_apos_dias'] > 0) {
            $instrucoes[] = 'PROTESTAR APÓS ' . $config['protestar_apos_dias'] . ' DIAS DO VENCIMENTO';
        }
        
        // Mensagem customizada do banco
        if (!empty($config['mensagem_banco'])) {
            $instrucoes[] = strtoupper($config['mensagem_banco']);
        }
        
        return $instrucoes;
    }

    /**
     * Gera arquivo de remessa CNAB 240
     * 
     * @param array $parcelas Array de parcelas para incluir na remessa
     * @return string Caminho do arquivo gerado
     * @throws Exception
     */
    public function gerarArquivoRemessa(array $parcelas): string
    {
        try {
            // TODO: Implementar geração de arquivo CNAB 240
            // Esta funcionalidade será desenvolvida na Fase 3
            
            $nomeArquivo = 'remessa_' . date('YmdHis') . '.txt';
            $caminhoCompleto = $this->config['dir_remessas'] . $nomeArquivo;

            // Por enquanto, criar arquivo vazio como placeholder
            file_put_contents($caminhoCompleto, '');

            return $caminhoCompleto;

        } catch (Exception $e) {
            log_message('error', 'Erro ao gerar arquivo de remessa: ' . $e->getMessage());
            throw new Exception('Erro ao gerar arquivo de remessa: ' . $e->getMessage());
        }
    }

    /**
     * Processa arquivo de retorno CNAB 240
     * 
     * @param string $caminhoArquivo Caminho do arquivo de retorno
     * @return array Resultado do processamento (pagamentos, rejeições, etc)
     * @throws Exception
     */
    public function processarArquivoRetorno(string $caminhoArquivo): array
    {
        try {
            // TODO: Implementar processamento de arquivo de retorno
            // Esta funcionalidade será desenvolvida na Fase 3

            if (!file_exists($caminhoArquivo)) {
                throw new Exception('Arquivo de retorno não encontrado');
            }

            // Por enquanto, retornar array vazio como placeholder
            return [
                'pagamentos' => [],
                'rejeicoes' => [],
            ];

        } catch (Exception $e) {
            log_message('error', 'Erro ao processar arquivo de retorno: ' . $e->getMessage());
            throw new Exception('Erro ao processar arquivo de retorno: ' . $e->getMessage());
        }
    }
}
