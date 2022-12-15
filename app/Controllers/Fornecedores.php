<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\ValidacoesTrait;

class Fornecedores extends BaseController
{
    use ValidacoesTrait;
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

    public function exibir(int $id = null)
    {
        $fornecedor  = $this->buscaFornecedorOu404($id);

        

        $data = [
            'titulo' => "Detalhando o fornecedor " . esc($fornecedor->razao),
            'fornecedor' => $fornecedor
        ];

        
        return view('Fornecedores/exibir', $data);
    }

    public function editar(int $id = null)
    {
        $fornecedor  = $this->buscaFornecedorOu404($id);

        

        $data = [
            'titulo' => "Editando o fornecedor " . esc($fornecedor->razao),
            'fornecedor' => $fornecedor
        ];

        
        return view('Fornecedores/editar', $data);
    }

    public function consultaCep(){

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $cep = $this->request->getGet('cep');

        return $this->response->setJSON($this->consultaViaCep($cep));
    }


    private function buscaFornecedorOu404(int $id = null)
    {
        if (!$id || !$fornecedor = $this->fornecedorModel->withDeleted(true)->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o fornecedor $id");
        }

        return $fornecedor;
    }
}
