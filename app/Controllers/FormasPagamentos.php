<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\FormaPagamento;

class FormasPagamentos extends BaseController
{
    private $formaPagamentoModel;


    public function __construct()
    {
        $this->formaPagamentoModel = new \App\Models\FormaPagamentoModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando as formas de pagamentos',
        ];

        return view('FormasPagamentos/index', $data);
    }

    public function recuperaFormas()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $formas = $this->formaPagamentoModel->findAll();

        $data = [];

        foreach ($formas as $forma) {
            $data[] = [
                'nome' => anchor("formas/exibir/$forma->id", esc($forma->nome), 'title="Exibir a forma de pagamento ' . esc($forma->nome) . '"'),
                'descricao' => esc($forma->descricao),
                'criado_em' => esc($forma->criado_em->humanize()),
                'situacao' => $forma->exibeSituacao(),
            ];
        }

        $retorno = [
            'data' => $data
        ];

        return $this->response->setJSON($retorno);
    }

    public function exibir(int $id = null)
    {
        $forma = $this->buscaFormasOu404($id);

        $data = [
            'titulo' => "Detalhando a forma de pagamento " .esc($forma->nome),
            'forma' => $forma
        ];

        return view('FormasPagamentos/exibir', $data);
    }

    public function criar()
    {
        $forma  = new FormaPagamento();

        $data = [
            'titulo' => "Criando nova forma de pagamento ",
            'forma' => $forma
        ];

        return view('FormasPagamentos/criar', $data);
    }

    public function cadastrar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        // Recupero o post da requisição
        $post = $this->request->getPost();

        $forma = new FormaPagamento($post);

        if ($this->formaPagamentoModel->save($forma)) {

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso! <br> <a class="btn btn-danger mt-2" href=' . site_url('formas/criar') . '>Criar nova forma de pagamento</a>');

            $retorno['id'] = $this->formaPagamentoModel->getInsertID();
            return $this->response->setJSON($retorno);
        }

        //Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->formaPagamentoModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function editar(int $id = null)
    {
        $forma  = $this->buscaFormasOu404($id);

        if($forma->id < 3){
            return redirect()
                ->to(site_url("formas/exibir/$forma->id"))
                ->with('atencao', 'A forma de pagamento <b>' .esc($forma->nome). '</b> não pode ser editado ou excluído, conforme detalhado na exibição da mesmo.');
        }

        $data = [
            'titulo' => "Editando a forma de pagamento " . esc($forma->nome),
            'forma' => $forma
        ];

        return view('FormasPagamentos/editar', $data);
    }

    public function atualizar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        // Recupero o post da requisição
        $post = $this->request->getPost();


        //Validamos a existencia do usuário
        $forma = $this->buscaFormasOu404($post['id']);

        if($forma->id < 3){

            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';

            $retorno['erros_model'] = [
                'grupo' => 'A forma de pagamento <b class="text-white">' .esc($forma->nome). '</b> não pode ser editada, conforme detalhado na exibição do mesmo.'
            ];
            return $this->response->setJSON($retorno);
        }

        //Preenchemos os atributos do usuário com os valores do POST
        $forma->fill($post);

        if ($forma->hasChanged() == false) {
            $retorno['info'] = 'Não há dados para serem atualizados';
            return $this->response->setJSON($retorno);
        }

        if ($this->formaPagamentoModel->protect(false)->save($forma)) {

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            return $this->response->setJSON($retorno);
        }

        //Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->formaPagamentoModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function excluir(int $id = null)
    {
        $forma  = $this->buscaFormasOu404($id);

        if($forma->id < 3){
            return redirect()
                ->to(site_url("formas/exibir/$forma->id"))
                ->with('atencao', 'A forma de pagamento ' .esc($forma->nome). ' não pode ser editada ou excluída, conforme detalhado na exibição do mesmo.');
        }

        if ($this->request->getMethod() === 'post') {

            $this->formaPagamentoModel->delete($forma->id);

            return redirect()->to(site_url("formas"))->with('sucesso', 'Forma de pagamento '.esc($forma->nome). ' excluída com sucesso!');
        }

        $data = [
            'titulo' => "Excluindo a forma de pagamento " . esc($forma->nome),
            'forma' => $forma
        ];

        return view('FormasPagamentos/excluir', $data);
    }

    //------------------métodos privados-----------------------//
    private function buscaFormasOu404(int $id = null)
    {
        if (!$id || !$forma = $this->formaPagamentoModel->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos a forma $id");
        }

        return $forma;
    }
}
