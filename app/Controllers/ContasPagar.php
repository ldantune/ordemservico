<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\ContaPagar;

class ContasPagar extends BaseController
{

    private $contaPagarModel;
    private $fornecedorModel;
    private $eventoModel;

    public function __construct()
    {
        $this->contaPagarModel = new \App\Models\ContaPagarModel();
        $this->fornecedorModel = new \App\Models\FornecedorModel();
        $this->eventoModel = new \App\Models\EventoModel();
    }

    public function index()
    {
        if(!$this->usuarioLogado()->temPermissaoPara('listar-contas')){
            return redirect()->back()->with('atencao', $this->usuarioLogado()->nome. ', você não tem permissão para acessar esse menu.');
        }

        $data = [
            'titulo' => 'Listando as contas',
        ];

        return view('ContasPagar/index', $data);
    }

    public function recuperaContas()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $contas = $this->contaPagarModel->recuperaContasPagar();

        $data = [];
        foreach ($contas as $conta) {
            $data[] = [
                'razao' => anchor("contas/exibir/$conta->id", esc($conta->razao . ' - CNPJ ' . $conta->cnpj), 'title="Exibir conta ' . esc($conta->razao) . '"'),
                'valor_conta' => 'R$ ' . esc(number_format($conta->valor_conta, 2)),
                'data_vencimento' => $conta->data_vencimento,
                'situacao' => $conta->exibeSituacao(),
            ];
        }

        $retorno = [
            'data' => $data
        ];

        return $this->response->setJSON($retorno);
    }

    public function exibir(int $id)
    {
        if(!$this->usuarioLogado()->temPermissaoPara('listar-contas')){
            return redirect()->back()->with('atencao', $this->usuarioLogado()->nome. ', você não tem permissão para acessar esse menu.');
        }

        $conta = $this->contaPagarModel->buscaContaOu404($id);

        $data = [
            'titulo' => "Detalhando a conta do fornecedor $conta->razao",
            'conta' => $conta
        ];

        return view('ContasPagar/exibir', $data);
    }

    public function criar()
    {
        if(!$this->usuarioLogado()->temPermissaoPara('criar-contas')){
            return redirect()->back()->with('atencao', $this->usuarioLogado()->nome. ', você não tem permissão para acessar esse menu.');
        }

        $conta = new ContaPagar();

        $data = [
            'titulo' => "Criando nova conta de fornecedor",
            'conta' => $conta
        ];

        return view('ContasPagar/criar', $data);
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


        $conta = new ContaPagar($post);

        $conta->valor_conta = str_replace(",", "", $conta->valor_conta);



        if ($this->contaPagarModel->save($conta)) {

            if ($conta->situacao == 0) {
                $this->cadastraEventoDaConta($conta);
            }

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso! <br> <a class="btn btn-danger mt-2" href=' . site_url('contas/criar') . '>Criar nova conta</a>');

            $retorno['id'] = $this->contaPagarModel->getInsertID();

            return $this->response->setJSON($retorno);
        }


        //Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->contaPagarModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function buscaFornecedores()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $atributos = [
            'id',
            'CONCAT(razao, " CNPJ ", cnpj) AS razao',
            'cnpj'
        ];

        $termo = $this->request->getGet('termo');

        $fornecedores = $this->fornecedorModel
            ->select($atributos)
            ->asArray()
            ->like('razao', $termo)
            ->orLike('cnpj', $termo)
            ->where('ativo', true)
            ->orderBy('razao', 'ASC')
            ->findAll();

        return $this->response->setJSON($fornecedores);
    }

    public function editar(int $id)
    {
        if(!$this->usuarioLogado()->temPermissaoPara('editar-contas')){
            return redirect()->back()->with('atencao', $this->usuarioLogado()->nome. ', você não tem permissão para acessar esse menu.');
        }

        $conta = $this->contaPagarModel->buscaContaOu404($id);

        $data = [
            'titulo' => "Editando a conta do fornecedor $conta->razao",
            'conta' => $conta
        ];

        return view('ContasPagar/editar', $data);
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

        $conta = $this->contaPagarModel->buscaContaOu404($post['id']);

        $conta->fill($post);

        if ($conta->hasChanged() === false) {
            $retorno['info'] = 'Não há dados para ser atualizado.';
            return $this->response->setJSON($retorno);
        }

        $conta->valor_conta = str_replace(",", "", $conta->valor_conta);

        

        

        if ($this->contaPagarModel->save($conta)) {

            if($conta->hasChanged('data_vencimento') && $conta->situacao == 0){

                $dias = $conta->defineDataVencimentoEvento();
    
                $this->eventoModel->atualizaEvento('conta_id', $conta->id, $dias);
            }
            
            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            return $this->response->setJSON($retorno);
        }


        //Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->contaPagarModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function excluir(int $id = null)
    {
        if(!$this->usuarioLogado()->temPermissaoPara('excluir-contas')){
            return redirect()->back()->with('atencao', $this->usuarioLogado()->nome. ', você não tem permissão para acessar esse menu.');
        }

        $conta = $this->contaPagarModel->buscaContaOu404($id);

        if ($this->request->getMethod() === 'post') {

            $this->contaPagarModel->delete($conta->id);

            return redirect()->to(site_url("contas"))->with('sucesso', "Conta do fornecedor $conta->razao excluída com sucesso!");
        }

        $data = [
            'titulo' => "Excluindo a conta do fornecedor" . esc($conta->razao),
            'conta' => $conta
        ];


        return view('ContasPagar/excluir', $data);
    }

    private function cadastraEventoDaConta(object $conta) : void
    {
        $fornecedor = $this->fornecedorModel->select('razao, cnpj')->find($conta->fornecedor_id);

        $razao = esc($fornecedor->razao);
        $cnpj = esc($fornecedor->cnpj);

        $valorConta = 'R$&nbsp;' . esc(number_format($conta->valor_conta, 2));

        $tituloEvento = "Conta do fornecedor $razao - CNPJ: $cnpj | Valor $valorConta";

        $dias = $conta->defineDataVencimentoEvento();

        $contaId = $this->contaPagarModel->getInsertID();

        $this->eventoModel->cadastraEvento('conta_id', $tituloEvento, $contaId, $dias);
    }
}
