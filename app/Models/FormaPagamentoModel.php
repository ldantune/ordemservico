<?php

namespace App\Models;

use CodeIgniter\Model;

class FormaPagamentoModel extends Model
{
    protected $table            = 'formas_pagamentos';
    protected $returnType       = 'App\Entities\FormaPagamento';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nome',
        'ativo',
        'descricao'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';

    // Validation
    protected $validationRules = [
        'nome'    => 'required|max_length[128]|is_unique[formas_pagamentos.nome,id,{id}]',
        'descricao'     => 'required|min_length[3]|max_length[240]',
    ];
    protected $validationMessages = [
        'nome' => [
            'required' => 'O campo nome é obrigatorio',
            'max_length' => 'O campo nome não pode ser maior que 128 caractéres',
            'is_unique' => 'Esse nome já foi escolhido. Por favor informe outro.',
        ],
        'descricao' => [
            'required' => 'O campo Descrição é obrigatorio',
            'min_length' => 'O campo Descrição precisa ter pelo menos 3 caractéres',
            'max_length' => 'O campo Descrição não pode ser maior que 240 caractéres',
        ],
    ];
}
