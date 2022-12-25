<?php

namespace App\Models;

use CodeIgniter\Model;

class ContaPagarModel extends Model
{
    protected $table            = 'contas_pagar';
    protected $returnType       = 'App\Entities\ContaPagar';
    protected $allowedFields    = [
        'fornecedor_id',
        'valor_conta',
        'data_vencimento',
        'descricao_conta',
        'situacao',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';


    // Validation
    protected $validationRules = [
        'fornecedor_id'         => 'required',
        'valor_conta'           => 'required|greater_than[0]',
        'data_vencimento'       => 'required',
        'descricao_conta'       => 'required',
        'situacao'              => 'required',
    ];
    protected $validationMessages = [
        'fornecedor_id' => [
            'required' => 'O campo fornecedor é obrigatorio',
        ],
        'valor_conta' => [
            'required' => 'O campo valor da conta é obrigatorio',
        ],
        'data_vencimento' => [
            'required' => 'O campo data de vencimento é obrigatorio',
        ],
        'descricao_conta' => [
            'required' => 'O campo descrição da conta é obrigatorio',
        ],
        'situacao' => [
            'required' => 'O campo situação da conta é obrigatorio',
        ],
    ];

    // Callbacks
    protected $beforeInsert   = ['removeVirgulaValores'];
    protected $beforeUpdate   = ['removeVirgulaValores'];

    protected function removeVirgulaValores(array $data)
    {
        if (isset($data['data']['valor_conta'])) {

            $data['data']['valor_conta'] = str_replace(",", "", $data['data']['valor_conta']);

        }

        return $data;
    }

    public function recuperaContasPagar()
    {

        $atributos = [
            'fornecedores.razao',
            'fornecedores.cnpj',
            'contas_pagar.*',
        ];

        return $this->select($atributos)
            ->join('fornecedores', 'fornecedores.id = contas_pagar.fornecedor_id')
            ->orderBy('contas_pagar.situacao', 'ASC')
            ->findAll();
    }

    public function buscaContaOu404(int $id = null)
    {

        if ($id === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos a conta a pagar $id");
        }

        $atributos = [
            'fornecedores.razao',
            'fornecedores.cnpj',
            'contas_pagar.*',
        ];

        $conta = $this->select($atributos)
            ->join('fornecedores', 'fornecedores.id = contas_pagar.fornecedor_id')
            ->find($id);


        if ($conta === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos a conta a pagar $id");
        }

        return $conta;
    }
}
