<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Fornecedores extends BaseController
{
    private $fornecedorModel;

    public function __construct()
    {
        $this->fornecedorModel = new \App\Models\FornecedorModel();
    }
    public function index()
    {
        $data = [
            'titulo' => 'Listando os fornedores do sistema',
        ];

        return view('Fornecedores/index', $data);
    }

    public function recuperaFornecedores()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $atributos = [
            'id',
            'razao',
            'cnpj',
            'telefone',
            'ativo',
            'deletado_em'
        ];

        $fornecedores = $this->fornecedorModel->select($atributos)
            ->withDeleted(true)
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [];
        foreach ($fornecedores as $fornecedor) {
            
            $data[] = [
                'razao' => anchor("fornecedores/exibir/$fornecedor->id", esc($fornecedor->razao), 'title="Exibir fornecedor ' . esc($fornecedor->razao) . '"'),
                'cnpj' => $fornecedor->cnpj,
                'telefone' => $fornecedor->telefone,
                'ativo' => $fornecedor->exibeSituacao(),

            ];
        }

        $retorno = [
            'data' => $data
        ];

        return $this->response->setJSON($retorno);
    }
}
