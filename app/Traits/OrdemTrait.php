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
}
