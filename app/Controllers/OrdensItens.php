<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\OrdemTrait;

class OrdensItens extends BaseController
{
    use OrdemTrait;

    private $ordemModel;
    private $ordemItemModel;
    private $itemModel;

    public function __construct()
    {
        $this->ordemModel = new \App\Models\OrdemModel();
        $this->ordemItemModel = new \App\Models\OrdemItemModel();
        $this->itemModel = new \App\Models\ItemModel();
    }

    public function itens(string $codigo = null)
    {
        if(!$this->usuarioLogado()->temPermissaoPara('listar-ordens')){
            return redirect()->back()->with('atencao', $this->usuarioLogado()->nome. ', você não tem permissão para acessar esse menu.');
        }

        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        $this->preparaItensDaOrdem($ordem);

        $data = [
            'titulo' => "gerenciando os itens da ordem $ordem->codigo",
            'ordem' => $ordem
        ];

        return view('Ordens/itens', $data);
    }

    public function pesquisaItens()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $term = $this->request->getGet('term');

        $itens = $this->itemModel->pesquisaItens($term);

        $retorno = [];

        foreach ($itens as $item) {
            $data['id'] = $item->id;
            $data['item_preco'] = number_format($item->preco_venda, 2);

            $itemTipo = ucfirst($item->tipo);

            if ($item->tipo === 'produto') {

                if ($item->imagem != null) {

                    $caminhoImagem = "itens/imagem/$item->imagem";
                    $altImagem = $item->nome;
                } else {
                    $caminhoImagem = "recursos/img/item_sem_imagem.jpg";
                    $altImagem = "$item->nome não possui imagem";
                }

                $data['value'] = "[Código $item->codigo_interno ] [ $itemTipo ] [ Estoque $item->estoque ] $item->nome";
            } else {
                $caminhoImagem = "recursos/img/servicosemimagem.jpg";
                $altImagem = "$item->nome não possui imagem";

                $data['value'] = "[Código $item->codigo_interno ] [ $itemTipo ] $item->nome";
            }

            $imagem = [
                'src' => $caminhoImagem,
                'class' => 'img-fluid img-thumbnail',
                'alt' => $altImagem,
                'width' => '50',
            ];

            $data['label'] = '<span>' . img($imagem) . ' ' . $data['value'] . '</span>';

            $retorno[] = $data;
        }

        return $this->response->setJSON($retorno);
    }

    public function adicionarItem()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $validacao = service('validation');

        $regras = [
            'item_id' => 'required',
            'item_quantidade' => 'required|greater_than[0]',
        ];

        $mensagens = [   // Errors
            'item_id' => [
                'required' => 'Por favor escolha um item do tipo produto ou serviço',
            ],
            'item_quantidade' => [
                'required' => 'Por favor escolha um item do tipo produto ou serviço, e escolha uma quantidade maior que zero.',
                'greater_than' => 'Por favor escolha um item do tipo produto ou serviço, e escolha uma quantidade maior que zero.',
            ],
        ];

        $validacao->setRules($regras, $mensagens);

        if ($validacao->withRequest($this->request)->run() == false) {

            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = $validacao->getErrors();

            return $this->response->setJSON($retorno);
        }

        $post = $this->request->getPost();

        $ordem = $this->ordemModel->buscaOrdemOu404($post['codigo']);

        $item = $this->buscaItemOu404($post['item_id']);

        if ($item->tipo === 'produto' && $post['item_quantidade'] > $item->estoque) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['estoque' => "Temos  apenas <b class='text-white'>$item->estoque</b> em estoque do item $item->nome"];

            return $this->response->setJSON($retorno);
        }

        if ($this->verificaSeOrdemPossuiItem($ordem->id, $item->id)) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['estoque' => "Essa ordem de serviço já possui o item <b class='text-white'>$item->nome</b>"];

            return $this->response->setJSON($retorno);
        }

        $ordemItem = [
            'ordem_id' => (int) $ordem->id,
            'item_id' => (int) $item->id,
            'item_quantidade' => (int) $post['item_quantidade']
        ];

        if ($this->ordemItemModel->save($ordemItem)) {

            session()->setFlashdata('sucesso', "$item->nome adicionado com sucesso!");

            return $this->response->setJSON($retorno);
        }

        //Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->itemModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function atualizarQuantidade(string $codigo = null)
    {

        if ($this->request->getMethod() !== 'post') {
            return redirect()->back();
        }

        if(!$this->usuarioLogado()->temPermissaoPara('editar-ordens')){
            return redirect()->back()->with('atencao', $this->usuarioLogado()->nome. ', você não tem permissão para acessar esse menu.');
        }

        $validacao = service('validation');

        $regras = [
            'item_id' => 'required',
            'item_quantidade' => 'required|greater_than[0]',
            'id_principal' => 'required|greater_than[0]',
        ];

        $mensagens = [   // Errors
            'item_id' => [
                'required' => 'Não conseguimos identificar qual é o item a ser atualizado.',
            ],
            'item_quantidade' => [
                'required' => 'Por favor escolha uma quantidade maior que zero.',
                'greater_than' => 'Por favor escolha uma quantidade maior que zero.',
            ],
            'id_principal' => [
                'required' => 'Não conseguimos processar a sua requisição. Escolha a quantidade e tente novamente.',
                'greater_than' => 'Não conseguimos processar a sua requisição. Escolha a quantidade e tente novamente.',
            ],
        ];

        $validacao->setRules($regras, $mensagens);

        if ($validacao->withRequest($this->request)->run() === false) {

            return redirect()->back()
                ->with('atencao', 'Por favor verifique os erros abaixo e tente novamente')
                ->with('erros_model', $validacao->getErrors());
        }



        $post = $this->request->getPost();

        // Recupera a ordem de serviço
        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        // Válida a existencia do item
        $item = $this->buscaItemOu404($post['item_id']);

        // Válida a existencia do registro principal
        $ordemItem = $this->buscaOrdemItemOu404($post['id_principal'], $ordem->id);

        if ($item->tipo === 'produto' && $post['item_quantidade'] > $item->estoque) {

            return redirect()->back()
                ->with('atencao', 'Por favor verifique os erros abaixo e tente novamente')
                ->with('erros_model', ['estoque' => "Temos  apenas <b class='text-white'>$item->estoque</b> unidades em estoque do item $item->nome"]);
        }


        if ($post['item_quantidade'] === $ordemItem->item_quantidade) {
            return redirect()->back()->with('atencao', 'Informe a quantidade diferente da anterior.');
        }

        $ordemItem->item_quantidade = $post['item_quantidade'];

        if ($this->ordemItemModel->atualizarQuantidadeItem($ordemItem)) {
            return redirect()->back()->with('sucesso', 'Quantidade atualizada com sucesso!');
        }


        return redirect()->back()
            ->with('atencao', 'Por favor verifique os erros abaixo e tente novamente')
            ->with('erros_model', $this->ordemItemModel->errors());
    }

    public function removerItem(string $codigo = null)
    {

        if ($this->request->getMethod() !== 'post') {
            return redirect()->back();
        }

        if(!$this->usuarioLogado()->temPermissaoPara('editar-ordens')){
            return redirect()->back()->with('atencao', $this->usuarioLogado()->nome. ', você não tem permissão para acessar esse menu.');
        }

        $validacao = service('validation');

        $regras = [
            'item_id' => 'required',
            'id_principal' => 'required|greater_than[0]',
        ];

        $mensagens = [   // Errors
            'item_id' => [
                'required' => 'Não conseguimos identificar qual é o item a ser excluído.',
            ],
            'id_principal' => [
                'required' => 'Não conseguimos processar a sua requisição. Escolha novamente o item a ser removido.',
                'greater_than' => 'Não conseguimos processar a sua requisição. Escolha novamente o item a ser removido.',
            ],
        ];

        $validacao->setRules($regras, $mensagens);

        if ($validacao->withRequest($this->request)->run() === false) {

            return redirect()->back()
                ->with('atencao', 'Por favor verifique os erros abaixo e tente novamente')
                ->with('erros_model', $validacao->getErrors());
        }



        $post = $this->request->getPost();

        // Recupera a ordem de serviço
        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        // Válida a existencia do item
        $item = $this->buscaItemOu404($post['item_id']);

        // Válida a existencia do registro principal
        $ordemItem = $this->buscaOrdemItemOu404($post['id_principal'], $ordem->id);

        if ($this->ordemItemModel->delete($ordemItem->id)) {
            return redirect()->back()->with('sucesso', 'Item removido com sucesso!');
        }

        return redirect()->back()
            ->with('atencao', 'Por favor verifique os erros abaixo e tente novamente')
            ->with('erros_model', $this->ordemItemModel->errors());
    }

    private function buscaItemOu404(int $id = null)
    {
        if (!$id || !$item = $this->itemModel->withDeleted(true)->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o item $id");
        }

        return $item;
    }

    private function buscaOrdemItemOu404(int $idPrincipal = null, int $ordem_id)
    {
        if (!$idPrincipal || !$ordemItem = $this->ordemItemModel->where('id', $idPrincipal)->where('ordem_id', $ordem_id)->first()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o registro principal $idPrincipal");
        }

        return $ordemItem;
    }

    private function verificaSeOrdemPossuiItem(int $Ordem_id, int $item_id): bool
    {

        $possuiItem = $this->ordemItemModel
            ->where('ordem_id', $Ordem_id)
            ->where('item_id', $item_id)
            ->first();

        if ($possuiItem === null) {
            return false;
        }

        return true;
    }
}
