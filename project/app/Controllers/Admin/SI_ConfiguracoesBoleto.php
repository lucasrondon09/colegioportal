<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Admin\SI_ConfiguracoesBoletoModel;

class SI_ConfiguracoesBoleto extends BaseController
{
    protected $configModel;

    public function __construct()
    {
        helper('auth');
        permission();
        
        $this->configModel = new SI_ConfiguracoesBoletoModel();
    }

    /**
     * Exibir tela de configurações
     */
    public function index()
    {
        $config = $this->configModel->getConfigAtiva();

        if (!$config) {
            // Se não houver configuração, criar uma padrão
            $this->configModel->insert([
                'juros_percentual'       => 1.00,
                'multa_percentual'       => 2.00,
                'multa_apos_dias'        => 1,
                'nao_receber_apos_dias'  => 29,
                'protestar_apos_dias'    => 0,
                'aceite'                 => 'N',
                'tipo_titulo'            => 'DM',
                'desconto_percentual'    => 0.00,
                'ativo'                  => 1,
            ]);
            $config = $this->configModel->getConfigAtiva();
        }

        $data = [
            'titulo' => 'Configurações de Boleto',
            'config' => $config,
        ];

        echo view('admin/template/header.php');
        echo view('admin/template/sidebar.php');
        echo view('Admin/SI_ConfiguracoesBoleto/index', $data);
        echo view('admin/template/footer.php');
    }

    /**
     * Salvar configurações
     */
    public function salvar()
    {
        try {
            $dados = $this->request->getPost();

            // Validar dados
            if (!$this->configModel->validate($dados)) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->configModel->errors());
            }

            $config = $this->configModel->getConfigAtiva();

            if ($config) {
                // Atualizar configuração existente
                $this->configModel->update($config['id'], $dados);
            } else {
                // Criar nova configuração
                $dados['ativo'] = 1;
                $this->configModel->insert($dados);
            }

            return redirect()->to(base_url('Admin/Boleto-Config'))
                ->with('success', 'Configurações salvas com sucesso!');

        } catch (\Exception $e) {
            log_message('error', 'Erro ao salvar configurações de boleto: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao salvar configurações: ' . $e->getMessage());
        }
    }

    /**
     * Restaurar configurações padrão
     */
    public function restaurarPadrao()
    {
        try {
            $config = $this->configModel->getConfigAtiva();

            $dadosPadrao = [
                'juros_percentual'       => 1.00,
                'multa_percentual'       => 2.00,
                'multa_apos_dias'        => 1,
                'nao_receber_apos_dias'  => 29,
                'protestar_apos_dias'    => 0,
                'aceite'                 => 'N',
                'tipo_titulo'            => 'DM',
                'mensagem_banco'         => 'DÚVIDAS ENTRE EM CONTATO COM O CEDENTE/FAVORECIDO',
                'desconto_percentual'    => 0.00,
                'ativo'                  => 1,
            ];

            if ($config) {
                $this->configModel->update($config['id'], $dadosPadrao);
            } else {
                $this->configModel->insert($dadosPadrao);
            }

            return redirect()->to(base_url('Admin/Boleto-Config'))
                ->with('success', 'Configurações restauradas para o padrão!');

        } catch (\Exception $e) {
            log_message('error', 'Erro ao restaurar configurações padrão: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erro ao restaurar configurações: ' . $e->getMessage());
        }
    }

    /**
     * Testar configurações gerando um boleto de exemplo
     */
    public function testar()
    {
        // TODO: Implementar geração de boleto de teste
        return redirect()->to(base_url('Admin/Boleto-Config'))
            ->with('info', 'Funcionalidade de teste em desenvolvimento');
    }
}
