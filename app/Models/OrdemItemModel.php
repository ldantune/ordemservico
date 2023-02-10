<?php

namespace App\Models;

use CodeIgniter\Model;

class OrdemItemModel extends Model
{
    protected $table            = 'ordens_itens';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'ordem_id',
        'item_id',
        'item_quantidade'
    ];

    public function recuperaItensDaOrdem(int $ordem_id)
    {

        $atributos = [
            'itens.id',
            'itens.nome',
            'itens.preco_venda',
            'itens.tipo',
            'itens.controla_estoque',
            'ordens_itens.id AS id_principal',
            'ordens_itens.item_quantidade',
        ];

        return $this->select($atributos)
                    ->join('itens', 'itens.id = ordens_itens.item_id')
                    ->where('ordens_itens.ordem_id', $ordem_id)
                    ->groupBy('itens.nome')
                    ->orderBy('itens.tipo', 'ASC')
                    ->findAll();
    }

    public function atualizarQuantidadeItem(object $ordemItem) 
    {
        
        return $this->set('item_quantidade', $ordemItem->item_quantidade)
                    ->where('id', $ordemItem->id)
                    ->update();
    }

    public function recuperaItensMaisVendidos(string $tipo, string $dataInicial, string $dataFinal)
    {

        $atributos = [
            'itens.nome',
            'itens.codigo_interno',
            'itens.tipo',
            'SUM(ordens_itens.item_quantidade) AS quantidade',
        ];

        $dataInicial = str_replace('T', ' ', $dataInicial);
        $dataFinal = str_replace('T', ' ', $dataFinal);

        $where = 'ordens.atualizado_em  BETWEEN "' .$dataInicial . '" AND "' .$dataFinal . '"';

        return $this->select($atributos)
                    ->join('itens', 'itens.id = ordens_itens.item_id')
                    ->join('ordens', 'ordens.id = ordens_itens.ordem_id')
                    ->where($where)
                    ->where('itens.tipo', $tipo)
                    ->where('ordens.situacao', 'encerrada')
                    ->groupBy('itens.nome')
                    ->orderBy('quantidade', 'DESC')
                    //->builder->getCompiledSelect();
                    ->findAll();
    }
}