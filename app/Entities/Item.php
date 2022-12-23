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

    public function exibeTipo()
    {

        $tipoItem = "";

        if ($this->tipo === 'produto') {
            $tipoItem = '<i class="fa fa-archive text-success"></i>&nbsp;Produto';
        } else {
            $tipoItem = '<i class="fa fa-wrench text-white"></i>&nbsp;Serviço';
        }

        return $tipoItem;
    }

    public function exibeEstoque()
    {
        return ($this->tipo === 'produto' ? $this->estoque : 'Não se aplica');
    }

    public function recuperaAtribustosAlteradoes(): string
    {
        $atribustosAlterados = [];

        if ($this->hasChanged('nome')) {
            $atribustosAlterados['nome'] = "O nome foi alterado para $this->nome";
        }

        if ($this->hasChanged('preco_venda')) {
            $atribustosAlterados['preco_venda'] = "O preço de venda foi alterado para $this->preco_venda";
        }

        if ($this->hasChanged('descricao')) {
            $atribustosAlterados['descricao'] = "A descrição foi alterada para $this->descricao";
        }

        if ($this->tipo === 'produto') {
            if ($this->hasChanged('preco_custo')) {
                $atribustosAlterados['preco_custo'] = "O preço de custo foi alterado para $this->preco_custo";
            }

            if ($this->hasChanged('estoque')) {
                $atribustosAlterados['estoque'] = "O estoque foi alterado para $this->estoque";
            }

            if ($this->hasChanged('controla_estoque')) {

                if ($this->controla_estoque === true) {

                    $atribustosAlterados['controla_estoque'] = "O controle de estoque foi ativado";
                } else {
                    $atribustosAlterados['controla_estoque'] = "O controle de estoque não está mais ativo";
                }
            }
        }


        if ($this->hasChanged('ativo')) {

            if ($this->ativo === true) {

                $atribustosAlterados['ativo'] = "O item  está ativo";
            } else {
                $atribustosAlterados['ativo'] = "O item não está mais ativo";
            }
        }

        return serialize($atribustosAlterados);
    }
}
