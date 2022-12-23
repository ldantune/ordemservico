<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Item;

class Itens extends BaseController
{
    private $itemModel;
    private $itemHistoricoModel;

    public function __construct()
    {
        $this->itemModel = new \App\Models\ItemModel();
        $this->itemHistoricoModel = new \App\Models\ItemHistoricoModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando os itens da base de dados'
        ];

        return view('Itens/index', $data);
    }

    public function recuperaItens()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $atributos = [
            'id',
            'nome',
            'tipo',
            'estoque',
            'preco_venda',
            'ativo',
            'deletado_em'
        ];

        $itens = $this->itemModel
            ->select($atributos)
            ->withDeleted(true)
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [];
        foreach ($itens as $item) {
            $data[] = [
                'nome' => anchor("itens/exibir/$item->id", esc($item->nome), 'title="Exibir item ' . esc($item->nome) . '"'),
                'tipo' => $item->exibeTipo(),
                'estoque' => $item->exibeEstoque(),
                'preco_venda' => 'R$&nbsp;' . $item->preco_venda,
                'ativo' => $item->exibeSituacao(),
            ];
        }

        $retorno = [
            'data' => $data
        ];

        return $this->response->setJSON($retorno);
    }

    public function exibir(int $id = null)
    {
        $item  = $this->buscaItemOu404($id);

        $this->defineHistoricoItem($item);

        $data = [
            'titulo' => "Detalhando o item " . esc($item->nome),
            'item' => $item
        ];


        return view('Itens/exibir', $data);
    }

    public function criar()
    {
        $item  = new Item();

        //dd($grupo);

        $data = [
            'titulo' => "Cadastrando novo item",
            'item' => $item
        ];

        return view('Itens/criar', $data);
    }

    public function editar(int $id = null)
    {
        $item  = $this->buscaItemOu404($id);

        $data = [
            'titulo' => "Editando o item " . esc($item->nome) . " " . $item->exibeTipo(),
            'item' => $item
        ];

        return view('Itens/editar', $data);
    }

    public function codigoBarras(int $id = null)
    {
        $item = $this->buscaItemOu404($id);

        $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
        $item->codigo_barras = $generator->getBarcode($item->codigo_interno, $generator::TYPE_CODE_128, 3, 80);

        $data = [
            'titulo' => "Código de barras do item " . esc($item->nome),
            'item' => $item
        ];

        return view('Itens/codigo_barras', $data);
    }

    public function atualizar()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        // Recupero o post da requisição
        $post = $this->request->getPost();

        $item = $this->buscaItemOu404($post['id']);

        $item->fill($post);


        if ($item->hasChanged() == false) {
            $retorno['info'] = 'Não há dados para serem atualizados';
            return $this->response->setJSON($retorno);
        }

        if ($item->tipo === 'produto') {
            if ($item->estoque == "") {
                $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
                $retorno['erros_model']  = ['estoque' => 'Para um item do tipo <b class="text-white">Produto</b>, é necessário informar a quantidade em estoque'];
                return $this->response->setJSON($retorno);
            }

            $precoCusto = str_replace([',', '.'], '', $item->preco_custo);
            $precoVenda = str_replace([',', '.'], '', $item->preco_venda);

            if ($precoCusto > $precoVenda) {
                $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
                $retorno['erros_model']  = ['estoque' => 'O preço de venda <b class="text-white">não pode ser menor</b> do que o preço de custo'];
                return $this->response->setJSON($retorno);
            }
        }

        if ($this->itemModel->save($item)) {

            $this->insereHistoricoItem($item, 'Atualização');

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            return $this->response->setJSON($retorno);
        }

        //Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->itemModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function cadastrar()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        // Recupero o post da requisição
        $post = $this->request->getPost();

        $item = new Item($post);

        $item->codigo_interno = $this->itemModel->geraCodigoInternoItem();

        if ($item->tipo === 'produto') {

            if ($item->marca == "" || $item->marca == null) {
                $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
                $retorno['erros_model']  = ['estoque' => 'Para um item do tipo <b class="text-white">Produto</b>, é necessário informar a marca do mesmo'];
                return $this->response->setJSON($retorno);
            }

            if ($item->modelo == "" || $item->modelo == null) {
                $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
                $retorno['erros_model']  = ['estoque' => 'Para um item do tipo <b class="text-white">Produto</b>, é necessário informar o modelo do mesmo'];
                return $this->response->setJSON($retorno);
            }

            if ($item->estoque == "") {
                $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
                $retorno['erros_model']  = ['estoque' => 'Para um item do tipo <b class="text-white">Produto</b>, é necessário informar a quantidade em estoque'];
                return $this->response->setJSON($retorno);
            }

            $precoCusto = str_replace([',', '.'], '', $item->preco_custo);
            $precoVenda = str_replace([',', '.'], '', $item->preco_venda);

            if ($precoCusto > $precoVenda) {
                $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
                $retorno['erros_model']  = ['estoque' => 'O preço de venda <b class="text-white">não pode ser menor</b> do que o preço de custo'];
                return $this->response->setJSON($retorno);
            }
        }

    
        if ($this->itemModel->save($item)) {

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso! <br> <a class="btn btn-danger mt-2" href=' . site_url('itens/criar') . '>Criar novo item</a>');

            $retorno['id'] = $this->itemModel->getInsertID();
            return $this->response->setJSON($retorno);
        }

        //Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->itemModel->errors();

        return $this->response->setJSON($retorno);
    }



    private function buscaItemOu404(int $id = null)
    {
        if (!$id || !$item = $this->itemModel->withDeleted(true)->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o item $id");
        }

        return $item;
    }

    private function defineHistoricoItem(object $item) : object{
        $atribustos = [
            'atributos_alterados',
            'criado_em',
            'acao',
        ];

        $historico = $this->itemHistoricoModel
                            ->asArray()
                            ->select($atribustos)
                            ->where('item_id', $item->id)
                            ->orderBy('criado_em', 'DESC')
                            ->findAll();

        if($historico != null){
            foreach($historico as $key => $hist){
                $historico[$key]['atributos_alterados'] = unserialize($hist['atributos_alterados']);
            }

            $item->historico = $historico;
        }

        return $item;
    }

    private function insereHistoricoItem(object $item, string $acao) : void{
        $historico = [
            'usuario_id' =>usuario_logado()->id,
            'item_id' =>$item->id,
            'acao' => $acao,
            'criado_em' => date('Y-m-d H:i:s'),
            'atributos_alterados' => $item->recuperaAtribustosAlteradoes()
        ];

        $this->itemHistoricoModel->insert($historico);
    }
}
