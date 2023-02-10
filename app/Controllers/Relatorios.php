<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Dompdf\Dompdf;

class Relatorios extends BaseController
{
    private $itemModel;
    private $ordemItemModel;
    private $ordemModel;
    private $contaPagarModel;
    private $usuarioModel;

    public function __construct()
    {
        $this->itemModel = new \App\Models\ItemModel();
        $this->ordemItemModel = new \App\Models\OrdemItemModel();
        $this->ordemModel = new \App\Models\OrdemModel();
        $this->contaPagarModel = new \App\Models\ContaPagarModel();
        $this->usuarioModel = new \App\Models\UsuarioModel();
    }

    public function itens()
    {
        $data = [
            'titulo' => 'Relatórios de itens',
        ];

        return view('Relatorios/Itens/itens', $data);
    }

    public function GerarRelatorioProdutosEstoqueZerado(){
       
        //TODO COLOCAR ACL AQUI

        $produtos = $this->itemModel
                ->where('tipo', 'produto')
                ->where('controla_estoque', true)
                ->where('estoque <', 1)->findAll();

        $data = [
            'titulo' => 'Relatório de produtos com estoque zerado ou negativo, gerado em: '.date('d/m/Y H:i'),
            'produtos' => $produtos
        ];

        $nomeArquivo = 'relatorio-produtos-com-estoque-zerado-negativo.pdf';

        $dompdf = new Dompdf();
        
        $dompdf->loadHtml(view('Relatorios/Itens/relatorio_estoque_zerado', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream($nomeArquivo, ['Attachment' => false]);

        unset($data);
        unset($dompdf);

    }

    public function ItensMaisVendidos(){

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        $validacao = service('validation');

        $regras = [
            'tipo' => 'required|in_list[produto,serviço]',
            'data_inicial' => 'required',
            'data_final' => 'required',
        ];

        $mensagens = [   // Errors
            'tipo' => [
                'required' => 'Por favor escolha um tipo de item',
            ],
            'data_inicial' => [
                'required' => 'Por favor informe a data inicial de busca'
            ],
            'data_final' => [
                'required' => 'Por favor informe a data final de busca'
            ]
        ];

        $validacao->setRules($regras, $mensagens);

        if ($validacao->withRequest($this->request)->run() == false) {

            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = $validacao->getErrors();

            return $this->response->setJSON($retorno);
        }

        $post = $this->request->getPost();

        //1675264860 | 1675524060
        $dataInicial = strtotime($post['data_inicial']);
        $dataFinal = strtotime($post['data_final']);

        if($dataInicial > $dataFinal){
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['datas' => 'A Data Inicial não pode ser menor que a Data Final'];

            return $this->response->setJSON($retorno);
        }
        
        $itens = $this->ordemItemModel->recuperaItensMaisVendidos($post['tipo'], $post['data_inicial'], $post['data_final']);

        $retorno['redirect'] = 'relatorios/itens-mais-vendidos';

        session()->set('itens', $itens);
        session()->set('post', $post);

        return $this->response->setJSON($retorno);
    }

    public function gerarRelatorioItensMaisVendidos(){

        //TODO: COLOCAR ACL AQUI;

        if(!session()->has('itens') || !session()->has('post')){
            return redirect()->to(site_url('relatorios/itens'))->with('atencao', 'Não foi possível gerar o relatório. Tente novamente');
        }

        $itens = session()->get('itens');
        $post = session()->get('post');

        
        $data = [
            'titulo' => 'Relatório de ' . plural(ucfirst($post['tipo'])).' mais vendidos, gerado em: '.date('d/m/Y H:i'),
            'itens' => $itens,
            'periodo' => 'Compreendendo o período entre ' .date('d/m/Y H:i', strtotime($post['data_inicial'])). ' e ' .date('d/m/Y H:i', strtotime($post['data_final'])),
        ];

        $nomeArquivo = 'relatorio-itens-mais-vendidos.pdf';

        $dompdf = new Dompdf();
        
        $dompdf->loadHtml(view('Relatorios/Itens/relatorio_itens_mais_vendidos', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream($nomeArquivo, ['Attachment' => false]);

        unset($data);
        unset($dompdf);
    }

    //-------------Ordens ----------//

    public function ordens()
    {
        $data = [
            'titulo' => 'Relatórios de ordens de serviços',
        ];

        return view('Relatorios/Ordens/ordens', $data);
    }
}
