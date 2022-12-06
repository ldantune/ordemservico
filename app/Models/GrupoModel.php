<?php

namespace App\Models;

use CodeIgniter\Model;

class GrupoModel extends Model
{

    protected $table            = 'grupos';
    protected $returnType       = 'App\Entities\Grupo';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['nome', 'descricao', 'exibir'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules = [
        'nome'    => 'required|max_length[230]|is_unique[usuarios.nome,id,{id}]',
        'descricao'     => 'required|min_length[3]|max_length[120]',
    ];
    protected $validationMessages = [
        'nome' => [
            'required' => 'O campo nome é obrigatorio',
            'max_length' => 'O campo nome não pode ser maior que 230 caractéres',
            'is_unique' => 'Esse nome já foi escolhido. Por favor informe outro.',
        ],
        'descricao' => [
            'required' => 'O campo Descrição é obrigatorio',
            'min_length' => 'O campo Descrição precisa ter pelo menos 3 caractéres',
            'max_length' => 'O campo Descrição não pode ser maior que 120 caractéres',
        ],
    ];
}
