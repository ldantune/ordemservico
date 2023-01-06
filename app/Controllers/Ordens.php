<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Entities\Ordem;
use App\Traits\OrdemTrait;

// reference the Dompdf namespace
use Dompdf\Dompdf;

class Ordens extends BaseController
{
    use OrdemTrait;

    private $ordemModel;
    private $ordemResponsavelModel;
    private $transacaoModel;
    private $clienteModel;
    private $usuarioModel;

    public function __construct()
    {
        $this->ordemModel = new \App\Models\OrdemModel();
        $this->ordemResponsavelModel = new \App\Models\OrdemResponsavelModel();
        $this->transacaoModel = new \App\Models\TransacaoModel();
        $this->clienteModel = new \App\Models\ClienteModel();
        $this->usuarioModel = new \App\Models\UsuarioModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando as ordens de serviços'
        ];

        return view('Ordens/index', $data);
    }

    public function recuperaOrdens()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $ordens = $this->ordemModel->recuperaOrdens();

        $data = [];

        foreach ($ordens as $ordem) {
            $data[] = [
                'codigo' => anchor("ordens/detalhes/$ordem->codigo", esc($ordem->codigo), 'title="Exibir ordem ' . esc($ordem->codigo) . '"'),
                'nome' => esc($ordem->nome),
                'cpf' => esc($ordem->cpf),
                'criado_em' => esc($ordem->criado_em->humanize()),
                'situacao' => $ordem->exibeSituacao(),
            ];
        }

        $retorno = [
            'data' => $data
        ];

        return $this->response->setJSON($retorno);
    }

    public function criar()
    {
        $ordem = new Ordem();

        $ordem->codigo = $this->ordemModel->geraCodigoInternoOrdem();

        $data = [
            'titulo' => 'Cadastrando nova ordem de serviço',
            'ordem' => $ordem
        ];

        return view('Ordens/criar', $data);
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

        //Preenchemos os atributos do usuário com os valores do POST
        $ordem = new Ordem($post);

        if ($this->ordemModel->save($ordem)) {

            $this->finalizaCadastroOrdem($ordem);

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

            $retorno['codigo'] = $ordem->codigo;

            return $this->response->setJSON($retorno);
        }

        //Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->ordemModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function detalhes(string $codigo = null)
    {

        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        $this->preparaItensDaOrdem($ordem);

        $transacao = $this->transacaoModel->where('ordem_id', $ordem->id)->first();

        if ($transacao !== null) {

            $ordem->transacao = $transacao;
        }

        $data = [
            'titulo' => "Detalhando a ordem de serviço $ordem->codigo",
            'ordem' => $ordem,
        ];

        return view('Ordens/detalhes', $data);
    }

    public function editar(string $codigo = null)
    {

        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        if ($ordem->situacao === 'encerrada') {
            return redirect()->back()->with("info", "Esta ordem não pode ser editada,pois encontra-se " . ucfirst($ordem->situacao));
        }


        $data = [
            'titulo' => "Editando a ordem de serviço $ordem->codigo",
            'ordem' => $ordem,
        ];

        return view('Ordens/editar', $data);
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

        //Validamos a existencia do da ordem
        $ordem = $this->ordemModel->buscaOrdemOu404($post['codigo']);

        if ($ordem->situacao === 'encerrada') {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model']  = ['situacao' => "Esta ordem não pode ser editada,pois encontra-se " . ucfirst($ordem->situacao)];

            return $this->response->setJSON($retorno);
        }

        //Preenchemos os atributos do usuário com os valores do POST
        $ordem->fill($post);

        if ($ordem->hasChanged() == false) {
            $retorno['info'] = 'Não há dados para serem atualizados';
            return $this->response->setJSON($retorno);
        }

        if ($this->ordemModel->save($ordem)) {

            if (session()->has('ordem-encerrar')) {

                session()->setFlashdata('sucesso', 'Parecer técnico foi definido com sucess!');

                $retorno['redirect'] = "ordens/encerrar/$ordem->codigo";

                return $this->response->setJSON($retorno);
            }

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

            $retorno['redirect'] = "ordens/detalhes/$ordem->codigo";
            return $this->response->setJSON($retorno);
        }

        //Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->ordemModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function excluir(string $codigo = null)
    {

        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        if ($ordem->deletado_em != null) {
            return redirect()->back()->with('info', "A ordem de serviço $ordem->codigo já encotra-se excluída");
        }

        $situacoesPermitidas = [
            'encerrada',
            'cancelada'
        ];

        if (!in_array($ordem->situacao, $situacoesPermitidas)) {
            return redirect()->back()->with('info', "Apenas ordens encerradas ou canceladas podem ser excluídas");
        }

        if ($this->request->getMethod() === 'post') {

            $this->ordemModel->delete($ordem->id);

            return redirect()->to(site_url("ordens"))->with('sucesso', "A ordem de serviço $ordem->codigo excluída com sucesso!");
        }



        $data = [
            'titulo' => "Excluíndo a ordem de serviço $ordem->codigo",
            'ordem' => $ordem,
        ];

        return view('Ordens/excluir', $data);
    }

    public function desfazerExclusao(string $codigo = null)
    {
        $ordem  = $this->ordemModel->buscaOrdemOu404($codigo);

        if ($ordem->deletado_em == null) {
            return redirect()->back()->with('info', "Apenas ordens de serviço excluídas podem ser recuparados");
        }


        $ordem->deletado_em = null;

        $this->ordemModel->protect(false)->save($ordem);

        return redirect()->back()->with('sucesso', "Oderm de serviço $ordem->codigo recuperado com sucesso!");
    }


    public function buscaClientes()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $atributos = [
            'id',
            'CONCAT(nome, " CPF ", cpf) AS nome',
            'cpf'
        ];

        $termo = $this->request->getGet('termo');

        $clientes = $this->clienteModel
            ->select($atributos)
            ->asArray()
            ->like('nome', $termo)
            ->orLike('cpf', $termo)
            ->orderBy('nome', 'ASC')
            ->findAll();

        return $this->response->setJSON($clientes);
    }

    public function responsavel(string $codigo = null)
    {

        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        if ($ordem->situacao === 'encerrada') {
            return redirect()->back()->with("info", "Esta ordem já encontra-se " . ucfirst($ordem->situacao));
        }


        $data = [
            'titulo' => "Definindo o responsável técnico a ordem de serviço $ordem->codigo",
            'ordem' => $ordem,
        ];

        return view('Ordens/responsavel', $data);
    }

    public function buscaResponsaveis()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $termo = $this->request->getGet('termo');

        $responsavel = $this->usuarioModel->recuperaResponsaveisParaOrdem($termo);

        return $this->response->setJSON($responsavel);
    }

    public function definirResponsavel()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        $validacao = service('validation');

        $regras = [
            'usuario_responsavel_id' => 'required|greater_than[0]',
        ];

        $mensagens = [   // Errors
            'usuario_responsavel_id' => [
                'required' => 'Por favor pesquise um responsável técnico e tente novamente.',
                'greater_than' => 'Por favor pesquise um responsável técnico e tente novamente.',
            ],
        ];

        $validacao->setRules($regras, $mensagens);

        if ($validacao->withRequest($this->request)->run() === false) {

            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model']  = $validacao->getErrors();

            return $this->response->setJSON($retorno);
        }

        // Recupero o post da requisição
        $post = $this->request->getPost();

        //Validamos a existencia do da ordem
        $ordem = $this->ordemModel->buscaOrdemOu404($post['codigo']);

        if ($ordem->situacao === 'encerrada') {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model']  = ['situacao' => "Esta ordem não pode ser editada,pois encontra-se " . ucfirst($ordem->situacao)];

            return $this->response->setJSON($retorno);
        }

        $usuarioResponsavel = $this->buscaUsuarioOu404($post['usuario_responsavel_id']);

        if ($this->ordemResponsavelModel->defineUsuarioResponsavel($ordem->id, $usuarioResponsavel->id)) {

            if (session()->has('ordem-encerrar')) {

                session()->setFlashdata('sucesso', 'Agora já é possível encerrar a ordem de serviço');

                $retorno['redirect'] = "ordens/encerrar/$ordem->codigo";

                return $this->response->setJSON($retorno);
            }

            session()->setFlashdata('sucesso', 'Técnico responsável definido com sucess!');

            $retorno['redirect'] = "ordens/responsavel/$ordem->codigo";

            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model']  = $this->ordemResponsavelModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function email(string $codigo = null)
    {

        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        $this->preparaItensDaOrdem($ordem);

        if ($ordem->situacao === 'aberta') {
            $this->enviaOrdemEmAndamentoParaCliente($ordem);
        } else {
            $this->enviaOrdemEncerradaParaCliente($ordem);
        }

        return redirect()->to(site_url("ordens/detalhes/$ordem->codigo"))->with('sucesso', "Ordem enviada para o e-mail do cliente.");
    }

    public function gerarPdf(string $codigo = null)
    {

        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        $this->preparaItensDaOrdem($ordem);

        $data = [
            'titulo' => "Gerar PDF da ordem de serviço $ordem->codigo",
            'ordem' => $ordem,
        ];

        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('Ordens/gerar_pdf', $data));

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream("Detalhes-da-ordem-$ordem->codigo.pdf", ["Attachment" => false]);
    }

    public function encerrar(string $codigo = null)
    {

        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        if ($ordem->parecer_tecnico === null) {
            return redirect()->to(site_url("ordens/editar/$ordem->codigo"))->with('atencao', 'Por favor informe qual é o Parecer Técnico da Ordem');
        }

        session()->set('ordem-encerrar', $ordem->codigo);

        if ($ordem->situacao !== 'aberta') {
            return redirect()->back()->with('atencao', 'Apenas ordens em aberto podem ser encerradas');
        }

        if (!$this->ordemTemResponsavel($ordem->id)) {
            return redirect()->to(site_url("ordens/responsavel/$ordem->codigo"))->with('atencao', 'Escolha um resposável técnico antes de encerrar a ordem de serviço');
        }

        $this->preparaItensDaOrdem($ordem);

        $data = [
            'titulo' => "Encerrar a ordem de serviço $ordem->codigo",
            'ordem' => $ordem
        ];
        //TODO: enviar formas de pagamento
        return view('Ordens/encerrar', $data);
    }

    public function inserirDesconto()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        // Recupero o post da requisição
        $post = $this->request->getPost();

        $validacao = service('validation');

        $regras = [
            'valor_desconto' => 'required',
        ];

        $mensagens = [   // Errors
            'valor_desconto' => [
                'required' => 'Por favor informe o valor do desconto maior que zero.',
            ],
        ];

        $validacao->setRules($regras, $mensagens);

        if ($validacao->withRequest($this->request)->run() === false) {

            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model']  = $validacao->getErrors();

            return $this->response->setJSON($retorno);
        }

        $valorDesconto = str_replace([',', '.'], '', $post['valor_desconto']);

        if ($valorDesconto <= 0) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model']  = ['valor_desconto' => 'Por favor informe o valor do desconto maior que zero.'];

            return $this->response->setJSON($retorno);
        }

        //Validamos a existencia do da ordem
        $ordem = $this->ordemModel->buscaOrdemOu404($post['codigo']);

        $ordem->valor_desconto = str_replace([','], '', $post['valor_desconto']);

        if ($ordem->hasChanged() === false) {
            $retorno['infor']  = 'Não há dados para atualizar';

            return $this->response->setJSON($retorno);
        }

        if ($this->ordemModel->save($ordem)) {

            $this->defineMensagensDesconto($ordem->valor_desconto);

            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model']  = $this->ordemModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function removerDesconto()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        // Recupero o post da requisição
        $post = $this->request->getPost();

        //Validamos a existencia do da ordem
        $ordem = $this->ordemModel->buscaOrdemOu404($post['codigo']);

        $ordem->valor_desconto = null;

        if ($ordem->hasChanged() === false) {
            $retorno['infor']  = 'Não há dados para atualizar';

            return $this->response->setJSON($retorno);
        }

        if ($this->ordemModel->save($ordem)) {

            session()->setFlashdata('sucesso', "Desconto removido com sucesso!");

            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model']  = $this->ordemModel->errors();

        return $this->response->setJSON($retorno);
    }

    //---------------------Métodos privados -------------------//

    private function finalizaCadastroOrdem(object $ordem): void
    {

        $ordemAbertura = [
            'ordem_id' => $this->ordemModel->getInsertID(),
            'usuario_abertura_id' => usuario_logado()->id
        ];


        $this->ordemResponsavelModel->insert($ordemAbertura);

        $ordem->cliente = $this->clienteModel->select('nome, email')->find($ordem->cliente_id);

        $ordem->situacao = 'aberta';
        $ordem->criado_em = date('Y-m-d H:i');
        //enviar e-mail para o cliente com a ordem recém criada
        $this->enviaOrdemEmAndamentoParaCliente($ordem);
    }

    private function enviaOrdemEmAndamentoParaCliente(object $ordem): void
    {

        $email = service('email');

        $email->setFrom('no-reply@ordem.com', 'Ordem de serviço Ltda');

        if (isset($ordem->cliente)) {
            $emailCliente = $ordem->cliente->email;
        } else {
            $emailCliente = $ordem->email;
        }

        $email->setTo($emailCliente);

        $email->setSubject("Ordem de serviço $ordem->codigo em andamento");

        $data = [
            'ordem' => $ordem
        ];

        $mensagem = view('Ordens/ordem_andamento_email', $data);

        $email->setMessage($mensagem);

        $email->send();
    }

    private function enviaOrdemEncerradaParaCliente(object $ordem): void
    {

        $email = service('email');

        $email->setFrom('no-reply@ordem.com', 'Ordem de serviço Ltda');

        if (isset($ordem->cliente)) {
            $emailCliente = $ordem->cliente->email;
        } else {
            $emailCliente = $ordem->email;
        }

        $email->setTo($emailCliente);

        if (isset($ordem->transacao)) {
            $tituloEmail = "Ordem de serviço $ordem->codigo encerrada com Boleto Bancário.";
        } else {

            $tituloEmail = "Ordem de serviço $ordem->codigo encerrada.";
        }

        $email->setSubject($tituloEmail);

        $data = [
            'ordem' => $ordem
        ];

        $mensagem = view('Ordens/ordem_encerrada_email', $data);

        $email->setMessage($mensagem);

        $email->send();
    }

    private function buscaUsuarioOu404(int $usuario_responsavel_id = null)
    {
        if (!$usuario_responsavel_id || !$usuarioResponsavel = $this->usuarioModel->select('id, nome')->where('ativo', true)->find($usuario_responsavel_id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o usuário $usuario_responsavel_id");
        }

        return $usuarioResponsavel;
    }

    private function ordemTemResponsavel(int $ordem_id): bool
    {

        if ($this->ordemResponsavelModel->where('ordem_id', $ordem_id)->where('usuario_responsavel_id', null)->first()) {
            return false;
        }

        return true;
    }

    private function defineMensagensDesconto(string $valorDesconto){
        $descontoBoleto = getenv('gerenciaNetDesconto') / 100 . '%';

            $descontoAdicionado = "R$ " . number_format($valorDesconto, 2);

            session()->setFlashdata('sucesso', "Desconto de $descontoAdicionado inserido com sucesso!");

            $usuarioLogado = usuario_logado()->nome;

            session()->setFlashdata('info', "<b>$usuarioLogado</b>, se esta ordem for encerrada com <b>Boleto Bancário</b>, prevalecerá o valor de desconto de <b>$descontoBoleto</b> para esse método de pagamento.");
    }
}
