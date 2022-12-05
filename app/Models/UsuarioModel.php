<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{

    protected $table            = 'usuarios';
    protected $returnType       = '\App\Entities\Usuario';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'nome',
        'email',
        'password',
        'reset_hash',
        'reset_expira_em',
        'imagem'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules = [
        'nome'     => 'required|min_length[3]|max_length[120]',
        'email'    => 'required|valid_email|max_length[230]|is_unique[usuarios.email,id,{id}]',
        'password'     => 'required|min_length[6]',
        'password_confirmation' => 'required_with[password]|matches[password]',
    ];
    protected $validationMessages = [
        'nome' => [
            'required' => 'O campo Nome é obrigatorio',
            'min_length' => 'O campo Nome precisa ter pelo menos 3 caractéres',
            'max_length' => 'O campo Nome não pode ser maior que 120 caractéres',
        ],
        'email' => [
            'required' => 'O campo E-mail é obrigatorio',
            'max_length' => 'O campo E-mail não pode ser maior que 230 caractéres',
            'is_unique' => 'Esse e-mail já foi escolhido. Por favor informe outro.',
        ],
        'password_confirmation' => [
            'required_with' => 'Por favor confirme a sua senha.',
            'matches' => 'As senhas precisam combinar',
        ],
        
    ];

    // Callbacks
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {

            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);

            //Removemos dos dados a serem salvos
            unset($data['data']['password']);
            unset($data['data']['password_confirmation']);
        }

        

        return $data;
    }
}
