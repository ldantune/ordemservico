<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\ValidacoesTrait;
use App\Entities\Fornecedor;

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

    public function criar(int $id = null)
    {
        $fornecedor  = new Fornecedor();



        $data = [
            'titulo' => "Cadastrar novo fornecedor ",
            'fornecedor' => $fornecedor
        ];


        return view('Fornecedores/criar', $data);
    }

    public function cadastrar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        if (session()->get('blockCep') === true) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model']  = ['cep' => 'Informe um CEP válido.'];

            return $this->response->setJSON($retorno);
        }

        // Recupero o post da requisição
        $post = $this->request->getPost();

        $fornecedor = new Fornecedor($post);


        if ($this->fornecedorModel->save($fornecedor)) {

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso! <br> <a class="btn btn-danger mt-2" href=' . site_url('fornecedores/criar') . '>Criar novo fornecedor</a>');
            $retorno['id'] = $this->fornecedorModel->getInsertID();
            return $this->response->setJSON($retorno);
        }


        //Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->fornecedorModel->errors();

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

    public function atualizar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        if (session()->get('blockCep') === true) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model']  = ['cep' => 'Informe um CEP válido.'];

            return $this->response->setJSON($retorno);
        }

        // Recupero o post da requisição
        $post = $this->request->getPost();

        $fornecedor = $this->buscaFornecedorOu404($post['id']);

        $fornecedor->fill($post);

        if ($fornecedor->hasChanged() === false) {
            $retorno['info'] = 'Não há dados para ser atualizado.';
            return $this->response->setJSON($retorno);
        }

        if ($this->fornecedorModel->save($fornecedor)) {

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            return $this->response->setJSON($retorno);
        }


        //Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->fornecedorModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function excluir(int $id = null){

        $fornecedor = $this->buscaFornecedorOu404($id);

        if ($fornecedor->deletado_em != null) {
            return redirect()->back()->with('info', "Esse usuário já encotra-se excluido");
        }

        if ($this->request->getMethod() === 'post') {

            $this->fornecedorModel->delete($fornecedor->id);

            $fornecedor->ativo = false;

            $this->fornecedorModel->protect(false)->save($fornecedor);

            return redirect()->to(site_url("fornecedores"))->with('sucesso', "Fornecedor $fornecedor->razao excluído com sucesso!");
        }

        $data = [
            'titulo' => "Excluindo o fornecedor " . esc($fornecedor->razao),
            'fornecedor' => $fornecedor
        ];


        return view('Fornecedores/excluir', $data);
    }

    public function desfazerExclusao(int $id = null)
    {
        $fornecedor  = $this->buscaFornecedorOu404($id);

        if ($fornecedor->deletado_em == null) {
            return redirect()->back()->with('info', "Apenas fornecedores excluídos podem ser recuparados");
        }


        $fornecedor->deletado_em = null;
        $this->fornecedorModel->protect(false)->save($fornecedor);
        return redirect()->back()->with('sucesso', "Fornecedor $fornecedor->razao recuperado com sucesso!");
    }

    public function consultaCep()
    {

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
