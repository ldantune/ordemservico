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

    public function buscaOrdemOu404(string $codigo = null)
    {

        if ($codigo === null) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos a ordem $codigo");
        }

        $atributos = [
            'ordens.*',
            'u_aber.id AS usuario_abertura_id',
            'u_aber.nome AS usuario_abertura',

            'u_resp.id AS usuario_responsavel_id',
            'u_resp.nome AS usuario_responsavel',

            'u_ence.id AS usuario_encerramento_id',
            'u_ence.nome AS usuario_encerramento',

            'clientes.usuario_id AS cliente_usuario_id',
            'clientes.nome',
            'clientes.cpf',
            'clientes.telefone',
            'clientes.email',
        ];

        $ordem = $this->select($atributos)
            ->join('ordens_responsaveis', 'ordens_responsaveis.ordem_id = ordens.id')
            ->join('clientes', 'clientes.id = ordens.cliente_id')
            ->join('usuarios AS u_cliente', 'u_cliente.id = clientes.usuario_id')
            ->join('usuarios AS u_aber', 'u_aber.id = ordens_responsaveis.usuario_abertura_id')
            ->join('usuarios AS u_resp', 'u_resp.id = ordens_responsaveis.usuario_responsavel_id', 'LEFT')
            ->join('usuarios AS u_ence', 'u_ence.id = ordens_responsaveis.usuario_encerramento_id', 'LEFT')
            ->where('ordens.codigo', $codigo)
            ->withDeleted(true)
            ->first();


        if ($ordem === null) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos a ordem $codigo");
        }

        return $ordem;
    }

    public function recuperaOrdens()
    {

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

    public function geraCodigoInternoOrdem(): string
    {
        do {

            $codigo = random_string('alnum', 20);

            $this->select('codigo')->where('codigo', $codigo);
        } while ($this->countAllResults() > 1);

        return $codigo;
    }
}
