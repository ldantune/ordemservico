<?php

namespace App\Models;

use CodeIgniter\Model;

class FornecedorModel extends Model
{
    protected $table            = 'fornecedores';
    protected $returnType       = 'App\Entities\Fornecedor';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'razao',
        'nome_fantasia',
        'cnpj',
        'ie',
        'telefone',
        'cep',
        'endereco',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'ativo'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules = [
        'razao'         => 'required|max_length[240]|is_unique[fornecedores.razao,id,{id}]',
        'cnpj'          => 'required|validaCNPJ|max_length[25]|is_unique[fornecedores.cnpj,id,{id}]',
        'ie'            => 'required|max_length[25]|is_unique[fornecedores.ie,id,{id}]',
        'telefone'      => 'required|max_length[18]|is_unique[fornecedores.telefone,id,{id}]',
        'cep'           => 'required',
        'endereco'      => 'required',
        'numero'        => 'max_length[45]',
        'bairro'        => 'required',
        'cidade'        => 'required',
        'estado'        => 'required',
        
    ];
    protected $validationMessages = [
        'razao' => [
            'required' => 'O campo razão é obrigatorio',
            'min_length' => 'O campo razão precisa ter pelo menos 3 caractéres',
            'max_length' => 'O campo razão não pode ser maior que 240 caractéres',
        ],
        'cnpj' => [
            'required' => 'O campo CNPJ é obrigatorio'
        ],
        'ie' => [
            'required' => 'O campo IE é obrigatorio'
        ],
        'telefone' => [
            'required' => 'O campo telefone é obrigatorio'
        ],
        'cep' => [
            'required' => 'O campo CEP é obrigatorio'
        ],
        'endereco' => [
            'required' => 'O campo endereço é obrigatorio'
        ],
        'numero' => [
            'max_length' => 'O campo razão não pode ser maior que 45 caractéres',
        ],
        'bairro' => [
            'bairro' => 'O campo bairro é obrigatorio'
        ],
        'cidade' => [
            'bairro' => 'O campo cidade é obrigatorio'
        ],
        'estado' => [
            'bairro' => 'O campo estado é obrigatorio'
        ],
        
    ];
}
