<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PermissaoSeeder extends Seeder
{
    public function run()
    {
        $permissaoModel = new \App\Models\PermissaoModel();

        $permissoes = [
            // [
            //     'nome' => 'listar-usuarios',
            // ],
            // [
            //     'nome' => 'criar-usuarios',
            // ],
            // [
            //     'nome' => 'editar-usuarios',
            // ],
            // [
            //     'nome' => 'excluir-usuarios',
            // ],
            [
                'nome' => 'listar-ordens',
            ],
            [
                'nome' => 'criar-ordens',
            ],
            [
                'nome' => 'editar-ordens',
            ],
            [
                'nome' => 'excluir-ordens',
            ],
            [
                'nome' => 'encerrar-ordens',
            ],
            
        ];

        foreach($permissoes as $permissao){
            
            $permissaoModel->protect(false)->insert($permissao);
        }

        echo 'Permissões criadas com sucesso!';

    }
}
