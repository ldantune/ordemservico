<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ClienteFakerSeeder extends Seeder
{
    public function run()
    {
        $clienteModel = new \App\Models\ClienteModel();
        $usuarioModel = new \App\Models\UsuarioModel();
        $grupoUsuarioModel = new \App\Models\GrupoUsuarioModel();

        $faker = \Faker\Factory::create('pt-BR');

        $faker->addProvider(new \Faker\Provider\pt_BR\Person($faker));
        $faker->addProvider(new \Faker\Provider\pt_BR\PhoneNumber($faker));

        $criarQuantosClientes = 1000;

        for($i = 0; $i < $criarQuantosClientes; $i++){

            $nomeGerado = $faker->unique()->name;
            $emailGerado = $faker->unique()->email;

            $cliente = [
                'nome'          => $nomeGerado,
                'cpf'           => $faker->unique()->cpf,
                'telefone'      => $faker->unique()->cellphoneNumber,
                'email'         => $emailGerado,
                'cep'           => $faker->postcode,
                'endereco'      => $faker->streetName,
                'numero'        => $faker->buildingNumber,
                'bairro'        => $faker->city,
                'cidade'        => $faker->city,
                'estado'        => $faker->stateAbbr,
                'criado_em'     => $faker->dateTimeBetween('-2 month', '-1 days')->format('Y-m-d H:i:s'),
                'atualizado_em' => $faker->dateTimeBetween('-2 month', '-1 days')->format('Y-m-d H:i:s'),
            ];

            //Criamos o cliente
            $clienteModel->skipValidation(true)->insert($cliente);
 

            $usuario = [
                'nome' => $nomeGerado,
                'email' => $emailGerado,
                'password_hash' => '123456',//$faker->unique()->password_hash,
                'ativo' => true
            ];

            //Criamos o usuário do cliente
            $usuarioModel->skipValidation(true)->protect(false)->insert($usuario);


            $grupoUsuario = [
                'grupo_id' => 2,
                'usuario_id' => $usuarioModel->getInsertID(),
            ];

            $grupoUsuarioModel->protect(false)->insert($grupoUsuario);

            $clienteModel
                ->protect(false)
                ->where('id', $clienteModel->getInsertID())
                ->set('usuario_id', $usuarioModel->getInsertID())
                ->update();
        } // fim for

        echo "$criarQuantosClientes clientes semeados com sucesso.";


    }
}
