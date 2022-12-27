<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FormaPagamentoSeeder extends Seeder
{
    public function run()
    {
        $formaPagamentoModel = new \App\Models\FormaPagamentoModel();

        $formas = [
            [
                'nome' => 'Boleto bancário',
                'descricao' => 'Pagamento com boleto bancário gerado junto com à Gerencianet',
                'ativo' => true,
            ],
            [
                'nome' => 'Cortesia',
                'descricao' => 'Forma de pagamento destinada apenas às ordens que não geraram valor',
                'ativo' => true,
            ],
            [
                'nome' => 'Cartão de crédito',
                'descricao' => 'Forma de pagamento com cartão de crédito',
                'ativo' => true,
            ],
            [
                'nome' => 'Cartão de débito',
                'descricao' => 'Forma de pagamento com cartão de débito',
                'ativo' => true,
            ],
        ];

        foreach($formas as $forma){
            $formaPagamentoModel->skipValidation(true)->protect(false)->insert($forma);
        }

        echo "Formas de Pagamento criadas com sucesso";
    }
}
