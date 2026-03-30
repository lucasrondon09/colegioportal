<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\CnabService;
use App\Models\SI_CnabRetornoModel;
use App\Models\SI_CnabRetornoDetalheModel;

class SI_CnabRetorno extends BaseController
{
    private $cnabService;
    private $retornoModel;
    private $retornoDetalheModel;

    public function __construct()
    {
        $this->cnabService = new CnabService();
        $this->retornoModel = new SI_CnabRetornoModel();
        $this->retornoDetalheModel = new SI_CnabRetornoDetalheModel();
    }

    /**
     * Página principal - Lista de retornos
     */
    public function index()
    {
        $data = [
            'titulo' => 'CNAB - Retornos',
            'retornos' => $this->retornoModel->getRetornosComEstatisticas(50),
            'estatisticas' => $this->retornoModel->getEstatisticas(),
        ];

        echo view('admin/template/header.php', $data);
        echo view('admin/template/sidebar.php');
        echo view('admin/CnabRetorno/index.php', $data);
        echo view('admin/template/footer.php');
    }

    /**
     * Página de upload de arquivo de retorno
     */
    public function upload()
    {
        $data = [
            'titulo' => 'Processar Retorno CNAB',
        ];

        echo view('admin/template/header.php', $data);
        echo view('admin/template/sidebar.php');
        echo view('admin/CnabRetorno/upload.php', $data);
        echo view('admin/template/footer.php');
    }

    /**
     * Processar arquivo de retorno
     */
    public function processar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/Admin/Cnab-Retorno');
        }

        $arquivo = $this->request->getFile('arquivo_retorno');

        if (!$arquivo || !$arquivo->isValid()) {
            return $this->response->setJSON([
                'sucesso' => false,
                'mensagem' => 'Arquivo inválido ou não enviado',
            ]);
        }

        // Validar extensão
        $extensao = strtolower($arquivo->getClientExtension());
        if (!in_array($extensao, ['ret', 'txt'])) {
            return $this->response->setJSON([
                'sucesso' => false,
                'mensagem' => 'Extensão de arquivo inválida. Use .RET ou .TXT',
            ]);
        }

        // Mover arquivo para diretório de retornos
        $nomeArquivo = $arquivo->getName();
        $caminhoDestino = WRITEPATH . 'retornos/' . $nomeArquivo;

        if (!$arquivo->move(WRITEPATH . 'retornos/', $nomeArquivo, true)) {
            return $this->response->setJSON([
                'sucesso' => false,
                'mensagem' => 'Erro ao mover arquivo',
            ]);
        }

        // Obter ID do usuário logado
        $usuarioId = session()->get('usuario_id') ?? null;

        // Processar retorno
        $resultado = $this->cnabService->processarRetorno($caminhoDestino, $usuarioId);

        return $this->response->setJSON($resultado);
    }

    /**
     * Visualizar detalhes de um retorno
     */
    public function visualizar($id)
    {
        $retorno = $this->retornoModel->find($id);

        if (!$retorno) {
            return redirect()->to('/Admin/Cnab-Retorno')->with('erro', 'Retorno não encontrado');
        }

        $detalhes = $this->retornoDetalheModel->getDetalhesPorRetorno($id);
        $ocorrencias = $this->retornoDetalheModel->contarPorOcorrencia($id);

        $data = [
            'titulo' => 'Detalhes do Retorno #' . $retorno['id'],
            'retorno' => $retorno,
            'detalhes' => $detalhes,
            'ocorrencias' => $ocorrencias,
        ];

        echo view('admin/template/header.php', $data);
        echo view('admin/template/sidebar.php');
        echo view('admin/CnabRetorno/visualizar.php', $data);
        echo view('admin/template/footer.php');
    }

    /**
     * Download do arquivo de retorno
     */
    public function download($id)
    {
        $retorno = $this->retornoModel->find($id);

        if (!$retorno) {
            return redirect()->to('/Admin/Cnab-Retorno')->with('erro', 'Retorno não encontrado');
        }

        if (!file_exists($retorno['arquivo_path'])) {
            return redirect()->to('/Admin/Cnab-Retorno')->with('erro', 'Arquivo não encontrado');
        }

        return $this->response->download($retorno['arquivo_path'], null)->setFileName($retorno['arquivo_nome']);
    }

    /**
     * Reprocessar retorno
     */
    public function reprocessar($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/Admin/Cnab-Retorno');
        }

        $retorno = $this->retornoModel->find($id);

        if (!$retorno) {
            return $this->response->setJSON([
                'sucesso' => false,
                'mensagem' => 'Retorno não encontrado',
            ]);
        }

        if (!file_exists($retorno['arquivo_path'])) {
            return $this->response->setJSON([
                'sucesso' => false,
                'mensagem' => 'Arquivo não encontrado',
            ]);
        }

        // Marcar detalhes como não processados
        $this->retornoDetalheModel->where('id_retorno', $id)->set(['processado' => 0])->update();

        // Atualizar status do retorno
        $this->retornoModel->atualizarStatus($id, 'reprocessado');

        // Obter ID do usuário logado
        $usuarioId = session()->get('usuario_id') ?? null;

        // Reprocessar
        $resultado = $this->cnabService->processarRetorno($retorno['arquivo_path'], $usuarioId);

        return $this->response->setJSON($resultado);
    }

    /**
     * Excluir retorno
     */
    public function excluir($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/Admin/Cnab-Retorno');
        }

        $retorno = $this->retornoModel->find($id);

        if (!$retorno) {
            return $this->response->setJSON([
                'sucesso' => false,
                'mensagem' => 'Retorno não encontrado',
            ]);
        }

        // Excluir arquivo físico
        if (file_exists($retorno['arquivo_path'])) {
            unlink($retorno['arquivo_path']);
        }

        // Excluir retorno (cascade vai excluir detalhes)
        $this->retornoModel->delete($id);

        return $this->response->setJSON([
            'sucesso' => true,
            'mensagem' => 'Retorno excluído com sucesso',
        ]);
    }

    /**
     * Exportar detalhes para Excel
     */
    public function exportar($id)
    {
        $retorno = $this->retornoModel->find($id);

        if (!$retorno) {
            return redirect()->to('/Admin/Cnab-Retorno')->with('erro', 'Retorno não encontrado');
        }

        $detalhes = $this->retornoDetalheModel->getDetalhesPorRetorno($id);

        // Criar CSV
        $filename = 'retorno_' . $id . '_' . date('Ymd_His') . '.csv';
        $filepath = WRITEPATH . 'uploads/' . $filename;

        $fp = fopen($filepath, 'w');

        // Cabeçalho
        fputcsv($fp, [
            'Nosso Número',
            'Seu Número',
            'Código Ocorrência',
            'Descrição',
            'Data Ocorrência',
            'Data Crédito',
            'Valor Título',
            'Valor Pago',
            'Valor Tarifa',
            'Valor Juros',
            'Valor Multa',
            'Valor Desconto',
            'Processado',
        ], ';');

        // Dados
        foreach ($detalhes as $detalhe) {
            fputcsv($fp, [
                $detalhe['nosso_numero'],
                $detalhe['seu_numero'],
                $detalhe['codigo_ocorrencia'],
                $detalhe['descricao_ocorrencia'],
                $detalhe['data_ocorrencia'],
                $detalhe['data_credito'],
                number_format($detalhe['valor_titulo'], 2, ',', '.'),
                number_format($detalhe['valor_pago'], 2, ',', '.'),
                number_format($detalhe['valor_tarifa'], 2, ',', '.'),
                number_format($detalhe['valor_juros'], 2, ',', '.'),
                number_format($detalhe['valor_multa'], 2, ',', '.'),
                number_format($detalhe['valor_desconto'], 2, ',', '.'),
                $detalhe['processado'] ? 'Sim' : 'Não',
            ], ';');
        }

        fclose($fp);

        return $this->response->download($filepath, null)->setFileName($filename);
    }
}
