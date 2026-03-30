<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BoletoConfigModel;

class BoletoConfig extends BaseController
{
    protected $boletoConfigModel;

    public function __construct()
    {
        $this->boletoConfigModel = new BoletoConfigModel();
    }

    /**
     * Exibe a página de configurações de boleto
     */
    public function index()
    {
        // Verificar se usuário está logado e tem permissão
        if (!session()->get('logged_in')) {
            return redirect()->to('/admin/login');
        }

        $data = [
            'title' => 'Configurações de Boleto',
            'config' => $this->boletoConfigModel->getConfigAtiva(),
        ];

        // Se não existe configuração, criar uma padrão
        if (!$data['config']) {
            $this->boletoConfigModel->insert([
                'juros_percentual'       => 1.00,
                'multa_percentual'       => 2.00,
                'multa_apos_dias'        => 1,
                'desconto_percentual'    => 0.00,
                'nao_receber_apos_dias'  => 29,
                'aceite'                 => 'N',
                'protestar_apos_dias'    => 0,
                'tipo_titulo'            => 'DM',
                'mensagem_banco'         => 'DÚVIDAS ENTRE EM CONTATO COM O CEDENTE/FAVORECIDO',
                'ativo'                  => 1,
            ]);
            $data['config'] = $this->boletoConfigModel->getConfigAtiva();
        }

        return view('admin/BoletoConfig/index', $data);
    }

    /**
     * Salva as configurações de boleto
     */
    public function salvar()
    {
        // Verificar se usuário está logado e tem permissão
        if (!session()->get('logged_in')) {
            return redirect()->to('/admin/login');
        }

        // Validar dados
        $rules = [
            'juros_percentual'       => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
            'multa_percentual'       => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
            'multa_apos_dias'        => 'required|integer|greater_than_equal_to[0]',
            'desconto_percentual'    => 'permit_empty|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
            'nao_receber_apos_dias'  => 'required|integer|greater_than_equal_to[0]',
            'aceite'                 => 'required|in_list[S,N]',
            'protestar_apos_dias'    => 'required|integer|greater_than_equal_to[0]',
            'tipo_titulo'            => 'required|max_length[2]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Preparar dados
        $data = [
            'juros_percentual'       => $this->request->getPost('juros_percentual'),
            'multa_percentual'       => $this->request->getPost('multa_percentual'),
            'multa_apos_dias'        => $this->request->getPost('multa_apos_dias'),
            'desconto_percentual'    => $this->request->getPost('desconto_percentual') ?: 0.00,
            'nao_receber_apos_dias'  => $this->request->getPost('nao_receber_apos_dias'),
            'aceite'                 => $this->request->getPost('aceite'),
            'protestar_apos_dias'    => $this->request->getPost('protestar_apos_dias'),
            'tipo_titulo'            => $this->request->getPost('tipo_titulo'),
            'mensagem_banco'         => $this->request->getPost('mensagem_banco'),
            'instrucao_1'            => $this->request->getPost('instrucao_1'),
            'instrucao_2'            => $this->request->getPost('instrucao_2'),
            'instrucao_3'            => $this->request->getPost('instrucao_3'),
        ];

        // Salvar configurações
        try {
            $this->boletoConfigModel->atualizarConfigAtiva($data);
            return redirect()->to('/Admin/Boleto-Config')->with('success', 'Configurações salvas com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Erro ao salvar configurações: ' . $e->getMessage());
        }
    }

    /**
     * Testa as configurações gerando um boleto de exemplo
     */
    public function testar()
    {
        // Verificar se usuário está logado e tem permissão
        if (!session()->get('logged_in')) {
            return redirect()->to('/admin/login');
        }

        // TODO: Implementar geração de boleto de teste
        return redirect()->to('/Admin/Boleto-Config')->with('info', 'Funcionalidade de teste em desenvolvimento');
    }
}
