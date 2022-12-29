<?php

namespace App\Models;

use CodeIgniter\Model;

class OrdemModel extends Model
{
    protected $table            = 'ordens';
    protected $returnType       = 'App\Entities\Ordem';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'cliente_id',
        'codigo',
        'forma_pagamento',
        'situacao',
        'itens',
        'valor_produtos',
        'valor_servicos',
        'valor_desconto',
        'valor_ordem',
        'equipamento',
        'defeito',
        'observacoes',
        'parecer_tecnico',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules = [
        'cliente_id'    => 'required',
        'codigo'    => 'required',
        'equipamento'    => 'required',
    ];
    protected $validationMessages = [
        'cliente_id' => [
            'required' => 'O campo nome é obrigatorio',
        ],
        'codigo' => [
            'required' => 'O campo código é obrigatorio',
        ],
        'equipamento' => [
            'required' => 'O campo equipamento é obrigatorio',
        ],
    ];

    public function geraCodigoInternoOrdem() : string{
        do{

            $codigo = random_string('alnum', 20);
            
            $this->select('codigo')->where('codigo', $codigo);

        }while($this->countAllResults() > 1);

        return $codigo;
    }

    public function recuperaOrdens(){
        
        $atributos = [
            'ordens.codigo',
            'ordens.criado_em',
            'ordens.situacao',
            'clientes.nome',
            'clientes.cpf'
        ];

        return $this->select($atributos)
                    ->join('clientes', 'clientes.id = ordens.cliente_id')
                    ->orderBy('ordens.situacao', 'ASC')
                    ->withDeleted(true)
                    ->findAll();
    }
}
