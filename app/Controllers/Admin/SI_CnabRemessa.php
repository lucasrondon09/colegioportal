<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\CnabService;
use App\Models\SI_CnabRemessaModel;
use App\Models\SI_CnabRemessaDetalheModel;
use App\Models\SI_ParcelasContratoModel;

class SI_CnabRemessa extends BaseController
{
    private $cnabService;
    private $remessaModel;
    private $remessaDetalheModel;
    private $parcelasModel;

    public function __construct()
    {
        $this->cnabService = new CnabService();
        $this->remessaModel = new SI_CnabRemessaModel();
        $this->remessaDetalheModel = new SI_CnabRemessaDetalheModel();
        $this->parcelasModel = new SI_ParcelasContratoModel();
    }

    /**
     * Página principal - Lista de remessas
     */
    public function index()
    {
        $data = [
            'titulo' => 'CNAB - Remessas',
            'remessas' => $this->remessaModel->getRemessasComDetalhes(50),
            'estatisticas' => $this->remessaModel->getEstatisticas(),
        ];

        return view('admin/template/header', $data)
            . view('admin/CnabRemessa/index', $data)
            . view('admin/template/footer');
    }

    /**
     * Página de geração de nova remessa
     */
    public function nova()
    {
        // Buscar parcelas com boletos gerados e não enviados
        $parcelas = $this->parcelasModel
            ->select('si_parcelas_contrato.*, si_contratos.id_aluno, si_alunos.nome as nome_aluno')
            ->join('si_contratos', 'si_contratos.id = si_parcelas_contrato.id_contrato', 'left')
            ->join('si_alunos', 'si_alunos.id = si_contratos.id_aluno', 'left')
            ->where('si_parcelas_contrato.boleto_gerado', 1)
            ->where('si_parcelas_contrato.enviado_remessa', 0)
            ->where('si_parcelas_contrato.status !=', 'pago')
            ->orderBy('si_parcelas_contrato.data_vencimento', 'ASC')
            ->findAll();

        $data = [
            'titulo' => 'Nova Remessa CNAB',
            'parcelas' => $parcelas,
            'total_parcelas' => count($parcelas),
            'valor_total' => array_sum(array_column($parcelas, 'valor_parcela')),
        ];

        return view('admin/template/header', $data)
            . view('admin/CnabRemessa/nova', $data)
            . view('admin/template/footer');
    }

    /**
     * Gerar arquivo de remessa
     */
    public function gerar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/Admin/Cnab-Remessa');
        }

        $idsParcelas = $this->request->getPost('parcelas');

        if (empty($idsParcelas)) {
            return $this->response->setJSON([
                'sucesso' => false,
                'mensagem' => 'Nenhuma parcela selecionada',
            ]);
        }

        // Obter ID do usuário logado (ajustar conforme seu sistema de autenticação)
        $usuarioId = session()->get('usuario_id') ?? null;

        // Gerar remessa
        $resultado = $this->cnabService->gerarRemessa($idsParcelas, $usuarioId);

        return $this->response->setJSON($resultado);
    }

    /**
     * Visualizar detalhes de uma remessa
     */
    public function visualizar($id)
    {
        $remessa = $this->remessaModel->find($id);

        if (!$remessa) {
            return redirect()->to('/Admin/Cnab-Remessa')->with('erro', 'Remessa não encontrada');
        }

        $detalhes = $this->remessaDetalheModel->getDetalhesPorRemessa($id);

        $data = [
            'titulo' => 'Detalhes da Remessa #' . $remessa['numero_remessa'],
            'remessa' => $remessa,
            'detalhes' => $detalhes,
        ];

        return view('admin/template/header', $data)
            . view('admin/CnabRemessa/visualizar', $data)
            . view('admin/template/footer');
    }

    /**
     * Download do arquivo de remessa
     */
    public function download($id)
    {
        $remessa = $this->remessaModel->find($id);

        if (!$remessa) {
            return redirect()->to('/Admin/Cnab-Remessa')->with('erro', 'Remessa não encontrada');
        }

        if (!file_exists($remessa['arquivo_path'])) {
            return redirect()->to('/Admin/Cnab-Remessa')->with('erro', 'Arquivo não encontrado');
        }

        return $this->response->download($remessa['arquivo_path'], null)->setFileName($remessa['arquivo_nome']);
    }

    /**
     * Marcar remessa como enviada
     */
    public function marcarEnviada($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/Admin/Cnab-Remessa');
        }

        $resultado = $this->remessaModel->marcarComoEnviada($id);

        if ($resultado) {
            return $this->response->setJSON([
                'sucesso' => true,
                'mensagem' => 'Remessa marcada como enviada',
            ]);
        }

        return $this->response->setJSON([
            'sucesso' => false,
            'mensagem' => 'Erro ao atualizar remessa',
        ]);
    }

    /**
     * Excluir remessa
     */
    public function excluir($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/Admin/Cnab-Remessa');
        }

        $remessa = $this->remessaModel->find($id);

        if (!$remessa) {
            return $this->response->setJSON([
                'sucesso' => false,
                'mensagem' => 'Remessa não encontrada',
            ]);
        }

        // Apenas permitir exclusão se status for 'gerado'
        if ($remessa['status'] !== 'gerado') {
            return $this->response->setJSON([
                'sucesso' => false,
                'mensagem' => 'Apenas remessas com status "gerado" podem ser excluídas',
            ]);
        }

        // Buscar parcelas da remessa e desmarcar como enviadas
        $detalhes = $this->remessaDetalheModel->where('id_remessa', $id)->findAll();
        foreach ($detalhes as $detalhe) {
            $this->parcelasModel->update($detalhe['id_parcela'], [
                'enviado_remessa' => 0,
                'data_envio_remessa' => null,
                'id_remessa' => null,
            ]);
        }

        // Excluir arquivo físico
        if (file_exists($remessa['arquivo_path'])) {
            unlink($remessa['arquivo_path']);
        }

        // Excluir remessa (cascade vai excluir detalhes)
        $this->remessaModel->delete($id);

        return $this->response->setJSON([
            'sucesso' => true,
            'mensagem' => 'Remessa excluída com sucesso',
        ]);
    }
}
