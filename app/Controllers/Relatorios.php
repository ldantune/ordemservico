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
}
