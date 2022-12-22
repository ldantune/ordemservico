<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Item extends Entity
{
    protected $dates   = ['criado_em', 'atualizado_em', 'deletado_em'];

    public function exibeSituacao()
    {

        if ($this->deletado_em != null) {

            $icone = '<span class="text-white">Excluído</span> <i class="fa fa-undo"></i>&nbsp;Desfazer';

            $situacao = anchor("itens/desfazerexclusao/$this->id", $icone, ['class' => 'btn btn-outline-success btn-sm']);

            return $situacao;
        }

        if ($this->ativo == true) {
            return '<i class="fa fa-unlock text-success"></i>&nbsp;Ativo';
        }

        if ($this->ativo == false) {
            return '<i class="fa fa-lock text-warning"></i>&nbsp;Inativo';
        }
    }

    public function exibeTipo(){

        $tipoItem = "";

        if ($this->tipo === 'produto') {
            $tipoItem = '<i class="fa fa-archive text-success"></i>&nbsp;Produto';
        }else{
            $tipoItem = '<i class="fa fa-wrench text-white"></i>&nbsp;Serviço';
        }

        return $tipoItem;
    }

    public function exibeEstoque(){
        return ($this->tipo === 'produto' ? $this->estoque : 'Não se aplica');
    }
}
