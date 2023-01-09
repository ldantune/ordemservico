<?php

namespace App\Traits;

trait OrdemTrait
{
  public function preparaItensDaOrdem(object $ordem): object
  {

    $ordemItemModel = new \App\Models\OrdemItemModel();

    if ($ordem->situacao === 'aberta') {

      $ordemItens = $ordemItemModel->recuperaItensDaOrdem($ordem->id);

      $ordem->itens = (!empty($ordemItens) ? $ordemItens : null);

      return $ordem;
    }

    if ($ordem->itens !== null) {

      $ordem->itens = unserialize($ordem->itens);
      
    }

    return $ordem;
  }

  public function preparaOrdemParaEncerrar(object $ordem, object $formaPagamento) : object{

    $ordem->situacao = ($formaPagamento->id == 1 ? 'aguardando' : 'encerrada');

    if($ordem->itens === null){

      $ordem->forma_pagamento = 'Cortesia';

      $ordem->valor_produtos = null;
      $ordem->valor_servicos = null;
      $ordem->valor_desconto = null;
      $ordem->valor_ordem = null;

      return $ordem;
    }

    $ordem->forma_pagamento = esc($formaPagamento->nome);

    $valorProdutos = null;
    $valorServicos = null;

    $produtos = [];

    foreach($ordem->itens as $item){
      if($item->tipo === 'produto'){
        $valorProdutos += $item->preco_venda * $item->item_quantidade;

        if($item->controla_estoque == true){

          array_push($produtos, [
            'id' => $item->id,
            'quantidade' => (int) $item->item_quantidade
          ]);

        }
      }else{

        $valorServicos += $item->preco_venda * $item->item_quantidade;

      }
    }

    if(!empty($produtos)){

      $ordem->produtos = $produtos;

    }

    $ordem->valor_produtos = str_replace(',', '', number_format($valorProdutos, 2));
    $ordem->valor_servicos = str_replace(',', '', number_format($valorServicos, 2));

    if($formaPagamento->id == 1){

      $valor = $valorProdutos + $valorServicos;

      $porcentagem = (int) getenv('gerenciaNetDesconto') / 100;

      $ordem->valor_desconto = $valor * ($porcentagem / 100);
    }

    $valorFinalOrdem = number_format(($valorProdutos + $valorServicos) - $ordem->valor_desconto, 2);

    $ordem->valor_ordem = str_replace(',', '', $valorFinalOrdem);

    $ordem->itens = serialize($ordem->itens);
    
    return $ordem;
  }
}
