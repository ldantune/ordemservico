<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GrupoTempSeeder extends Seeder
{
    public function run()
    {
        $grupoModel = new \App\Models\GrupoModel();

        $grupos = [
            [
                'nome' => 'Administrador',
                'descricao' => 'Grupo com acesso total ao sistema',
                'exibir' => false,
            ],
            [
                'nome' => 'Clientes',
                'descricao' => 'Esse grupo é destinado para atribuição de clientes, pois os mesmo poderão logar no sistema para acessar as suas ordens de serviços',
                'exibir' => true,
            ],
            [
                'nome' => 'Atendentes',
                'descricao' => 'Esse grupo acessa o sistema para realizar atendimento aos clientes',
                'exibir' => false,
            ],
        ];

        foreach($grupos as $grupo){
            $grupoModel->insert($grupo);
        }

     echo "Grupos criado com sucesso";
    }
}
