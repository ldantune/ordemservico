<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Cliente;
use App\Traits\ValidacoesTrait;

class Clientes extends BaseController
{

    use ValidacoesTrait;
    private $clienteModel;
    private $usuarioModel;
    private $grupoUsuarioModel;

    public function __construct()
    {
        $this->clienteModel = new \App\Models\ClienteModel();
        $this->usuarioModel = new \App\Models\UsuarioModel();
        $this->grupoUsuarioModel = new \App\Models\GrupoUsuarioModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando os clientes do sistema',
        ];

        return view('Clientes/index', $data);
    }

    public function recuperaClientes()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $atributos = [
            'id',
            'nome',
            'cpf',
            'telefone',
            'email',
            'cep',
            'endereco',
            'numero',
            'bairro',
            'cidade',
            'estado',
            'deletado_em'
        ];

        $clientes = $this->clienteModel->select($atributos)
            ->withDeleted(true)
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [];
        foreach ($clientes as $cliente) {
            $data[] = [
                'nome' => anchor("clientes/exibir/$cliente->id", esc($cliente->nome), 'title="Exibir cliente ' . esc($cliente->nome) . '"'),
                'cpf' => $cliente->cpf,
                'email' => $cliente->email,
                'telefone' => $cliente->telefone,
                'situacao' => $cliente->exibeSituacao(),
            ];
        }

        $retorno = [
            'data' => $data
        ];

        return $this->response->setJSON($retorno);
    }

    public function exibir(int $id = null)
    {
        $cliente  = $this->buscaClienteOu404($id);

        $data = [
            'titulo' => "Detalhando o cliente " . esc($cliente->nome),
            'cliente' => $cliente
        ];


        return view('Clientes/exibir', $data);
    }

    public function criar(int $id = null)
    {
        $cliente  = new Cliente();

        $this->removeBlockCepEmailSessao();

        $data = [
            'titulo' => "Cadastrar novo cliente ",
            'cliente' => $cliente
        ];


        return view('Clientes/criar', $data);
    }

    public function cadastrar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        if (session()->get('blockEmail') === true) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model']  = ['cep' => 'Informe um e-mail com domínio válido.'];

            return $this->response->setJSON($retorno);
        }

        if (session()->get('blockCep') === true) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model']  = ['cep' => 'Informe um CEP válido.'];

            return $this->response->setJSON($retorno);
        }

        // Recupero o post da requisição
        $post = $this->request->getPost();

        $cliente = new Cliente($post);

        if ($this->clienteModel->save($cliente)) {


            //criar usuário para o cliente
            $this->criaUsuarioParaCliente($cliente);

            //Enviar e-mail para o cliente informando da alteração do e-mail
            $this->enviaEmailCriacaoEmailAcesso($cliente);


            session()->setFlashdata('sucesso', 'Dados salvos com sucesso! <br> <a class="btn btn-danger mt-2" href=' . site_url('clientes/criar') . '>Criar novo cliente</a> <br><br>Importante: informe ao cliente os dados de acesso ao sistema: <p>E-mail: ' . $cliente->email . '</p> <p>Senha inicial: 123456</p> Esses mesmos dados foram enviados para o email do cliente.');
            $retorno['id'] = $this->clienteModel->getInsertID();

            return $this->response->setJSON($retorno);
        }


        //Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->clienteModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function editar(int $id = null)
    {
        $cliente  = $this->buscaClienteOu404($id);

        $this->removeBlockCepEmailSessao();

        $data = [
            'titulo' => "Editando o cliente " . esc($cliente->razao),
            'cliente' => $cliente
        ];

        return view('Clientes/editar', $data);
    }

    public function atualizar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        if (session()->get('blockEmail') === true) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model']  = ['cep' => 'Informe um e-mail com domínio válido.'];

            return $this->response->setJSON($retorno);
        }

        if (session()->get('blockCep') === true) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model']  = ['cep' => 'Informe um CEP válido.'];

            return $this->response->setJSON($retorno);
        }

        // Recupero o post da requisição
        $post = $this->request->getPost();

        $cliente = $this->buscaClienteOu404($post['id']);

        $cliente->fill($post);

        if ($cliente->hasChanged() == false) {
            $retorno['info'] = 'Não há dados para ser atualizado.';
            return $this->response->setJSON($retorno);
        }

        if ($this->clienteModel->save($cliente)) {

            if ($cliente->hasChanged('email')) {

                $this->usuarioModel->atualizaEmailDoCliente($cliente->usuario_id, $cliente->email);

                //Enviar e-mail para o cliente informando da alteração do e-mail
                $this->enviaEmailAlteracaoEmailAcesso($cliente);

                session()->setFlashdata('sucesso', 'Dados salvos com sucesso!<br><br>Importante: informe ao cliente o novo e-mail de acesso ao sistema: <p>E-mail: ' . $cliente->email . '</p> Um e-mail de notificação foi enviado para o cliente.');
                return $this->response->setJSON($retorno);
            }

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            return $this->response->setJSON($retorno);
        }


        //Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->clienteModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function excluir(int $id = null)
    {

        $cliente = $this->buscaClienteOu404($id);

        if ($cliente->deletado_em != null) {
            return redirect()->back()->with('info', "Esse cliente já encotra-se excluido");
        }

        if ($this->request->getMethod() === 'post') {

            $this->clienteModel->delete($cliente->id);

            return redirect()->to(site_url("clientes"))->with('sucesso', "Cliente $cliente->nome excluído com sucesso!");
        }

        $data = [
            'titulo' => "Excluindo o cliente " . esc($cliente->nome),
            'cliente' => $cliente
        ];


        return view('Clientes/excluir', $data);
    }

    public function desfazerExclusao(int $id = null)
    {
        $cliente  = $this->buscaClienteOu404($id);

        if ($cliente->deletado_em == null) {
            return redirect()->back()->with('info', "Apenas clientes excluídos podem ser recuparados");
        }


        $cliente->deletado_em = null;
        $this->clienteModel->protect(false)->save($cliente);
        return redirect()->back()->with('sucesso', "Cliente $cliente->nome recuperado com sucesso!");
    }

    public function consultaCep()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $cep = $this->request->getGet('cep');

        return $this->response->setJSON($this->consultaViaCep($cep));
    }

    public function consultaEmail()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $email = $this->request->getGet('email');

        return $this->response->setJSON($this->checkEmail($email));
    }

    public function historico(int $id = null)
    {
        $cliente  = $this->buscaClienteOu404($id);

        $data = [
            'titulo' => "Histórico de atendimento do cliente " . esc($cliente->nome),
            'cliente' => $cliente
        ];

        $ordemModel = new \App\Models\OrdemModel();

        $ordensCliente = $ordemModel->where('cliente_id', $cliente->id)->orderBy('ordens.id', 'DESC')->paginate(5);

        if($ordensCliente != null){
            $data['ordensCliente'] = $ordensCliente;
            $data['pager'] = $ordemModel->pager;
        }

        return view('Clientes/historico', $data);
    }
    

    private function buscaClienteOu404(int $id = null)
    {
        if (!$id || !$cliente = $this->clienteModel->withDeleted(true)->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o cliente $id");
        }

        return $cliente;
    }

    private function enviaEmailAlteracaoEmailAcesso(object $cliente): void
    {
        //$email = \Config\Services::email();
        $email = service('email');

        $email->setFrom('no-reply@ordem.com', 'Ordem de serviço Ltda');


        $email->setTo($cliente->email);

        $email->setSubject('E-mail de acesso ao sistema foi alterado');

        $data = [
            'cliente' => $cliente
        ];

        $mensagem = view('Clientes/email_acesso_alterado', $data);

        $email->setMessage($mensagem);

        $email->send();
    }

    private function removeBlockCepEmailSessao(): void
    {
        session()->remove('blockCep');
        session()->remove('blockEmail');
    }

    private function criaUsuarioParaCliente(object $cliente): void
    {

        $usuario = [
            'nome' => $cliente->nome,
            'email' => $cliente->email,
            'password_hash' => '123456', //$faker->unique()->password_hash,
            'ativo' => true
        ];

        //Criamos o usuário do cliente
        $this->usuarioModel->skipValidation(true)->protect(false)->insert($usuario);


        $grupoUsuario = [
            'grupo_id' => getenv('GRUPO_CLIENTE'),
            'usuario_id' => $this->usuarioModel->getInsertID(),
        ];

        $this->grupoUsuarioModel->protect(false)->insert($grupoUsuario);

        $this->clienteModel
            ->protect(false)
            ->where('id', $this->clienteModel->getInsertID())
            ->set('usuario_id', $this->usuarioModel->getInsertID())
            ->update();
    }

    private function enviaEmailCriacaoEmailAcesso(object $cliente): void
    {
        //$email = \Config\Services::email();
        $email = service('email');

        $email->setFrom('no-reply@ordem.com', 'Ordem de serviço Ltda');


        $email->setTo($cliente->email);

        $email->setSubject('Dados de acesso ao sistema');

        $data = [
            'cliente' => $cliente
        ];

        $mensagem = view('Clientes/email_dados_acesso', $data);

        $email->setMessage($mensagem);

        $email->send();
    }
}
