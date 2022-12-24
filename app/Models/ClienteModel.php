<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table            = 'clientes';
    protected $returnType       = 'App\Entities\Cliente';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'usuario_id',
        'nome',
        'cpf',
        'telefone',
        'email',
        'cep',
        'endereco',
        'numero',
        'bairro',
        'cidade',
        'estado',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules = [
        'nome'          => 'required|max_length[240]|is_unique[clientes.nome,id,{id}]',
        'cpf'           => 'required|validaCPF|exact_length[14]|is_unique[clientes.cpf,id,{id}]',
        'telefone'      => 'required|exact_length[15]|is_unique[clientes.telefone,id,{id}]',
        'email'         => 'required|valid_email|max_length[230]|is_unique[clientes.email,id,{id}]',
        'email'         => 'is_unique[usuarios.email,id,{id}]',
        'cep'           => 'required',
        'endereco'      => 'required',
        'numero'        => 'max_length[45]',
        'bairro'        => 'required',
        'cidade'        => 'required',
        'estado'        => 'required',
    ];

    protected $validationMessages = [
        'nome' => [
            'required' => 'O campo nome é obrigatorio',
            'min_length' => 'O campo nome precisa ter pelo menos 3 caractéres',
            'max_length' => 'O campo nome não pode ser maior que 240 caractéres',
        ],
        'cpf' => [
            'required' => 'O campo CPF é obrigatorio'
        ],
        'telefone' => [
            'required' => 'O campo telefone é obrigatorio'
        ],
        'email' => [
            'required' => 'O campo E-mail é obrigatorio',
            'max_length' => 'O campo E-mail não pode ser maior que 230 caractéres',
            'is_unique' => 'Esse e-mail já foi escolhido. Por favor informe outro.',
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
