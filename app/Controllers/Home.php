<?php

namespace App\Controllers;

use App\Libraries\Autenticacao;
use App\Traits\ValidacoesTrait;

class Home extends BaseController
{
    private $ordemModel;
    private $usuarioModel;
    private $ordemItemModel;
    private $clienteModel;
    private $fornecedorModel;
    private $itemModel;

    public function __construct()
    {
        $this->ordemModel = new \App\Models\OrdemModel();
        $this->usuarioModel = new \App\Models\UsuarioModel();
        $this->ordemItemModel = new \App\Models\OrdemItemModel();
        $this->clienteModel = new \App\Models\ClienteModel();
        $this->fornecedorModel = new \App\Models\FornecedorModel();
        $this->itemModel = new \App\Models\ItemModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Home'
        ];

        if (!$this->usuarioLogado()->temPermissaoPara('visualizar-home')) {
            return view('Home/index_simples', $data);
        }

        $data['totalClientes'] = $this->clienteModel->countAllResults();
        $data['totalFornecedores'] = $this->fornecedorModel->countAllResults();
        $data['totalItens'] = $this->itemModel->countAllResults();
        $data['totalOrdensEncerradas'] = $this->ordemModel->where('situacao', 'encerrada')->countAllResults();

        $data = $this->preparaDadosGraficosParaView($data);

        return view('Home/index', $data);
    }


    private function preparaDadosGraficosParaView(array $data) : array
    {
        $dadosClientes = $this->ordemModel->recuperaClientesMaisAssiduos(date('Y'));

        if(!empty($dadosClientes)){
            $data['dadosClientes'] = $dadosClientes;
        }

        $dadosDesempenho = $this->usuarioModel->recuperaAtendenteGrafico(date('Y'));

        if(!empty($dadosDesempenho)){
            $data['dadosDesempenho'] = $dadosDesempenho;
        }

        $produtosMaisVendidos = $this->ordemItemModel->recuperaItensMaisVendidosGraficos(date('Y'), 'produto', 5);

        // if(!empty($produtosMaisVendidos)){
        //     $data['produtosMaisVendidos'] = $produtosMaisVendidos;
        // }

        $data['produtosMaisVendidos'] = $produtosMaisVendidos;

        $servicosMaisVendidos = $this->ordemItemModel->recuperaItensMaisVendidosGraficos(date('Y'), 'serviço', 5);

        // if(!empty($servicosMaisVendidos)){
        //     $data['servicosMaisVendidos'] = $servicosMaisVendidos;
        // }

        $data['servicosMaisVendidos'] = $servicosMaisVendidos;

        $atendimentosPorMes = $this->ordemModel->recuperaOrdensPorMesGrafico(date('Y'));

        // if(!empty($atendimentosPorMes)){
        //     $data['atendimentosPorMes'] = $atendimentosPorMes;
        // }

        $data['atendimentosPorMes'] = $atendimentosPorMes;

        return $data;
    }
}
