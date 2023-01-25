<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\OrdemTrait;
use App\Transacao\Gerencianet\Operacoes;

class Transacoes extends BaseController
{
    private $transacaoModel;
    private $ordemModel;
    private $eventoModel;

    public function __construct()
    {
        $this->transacaoModel = new \App\Models\TransacaoModel();
        $this->ordemModel = new \App\Models\OrdemModel();
        $this->eventoModel = new \App\Models\EventoModel();
    }

    public function editar(string $codigo = null)
    {
        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        $transacao = $this->transacaoModel->where('ordem_id', $ordem->id)->first();

        if($transacao === null){
            return redirect()->back()
                            ->with('transacao', '')
                            ->with('atencao', "Não encontramos uma transação associada à ordem de serviço $codigo");
        }

        if($transacao->status === 'canceled'){
            return redirect()->back()
                            ->with('transacao', '')
                            ->with('atencao', "Apenas, transações com status [ Aguardando ] ou [ Não paga] podem ser atualizadas");
        }

        $ordem->transacao = $transacao;

        $data = [
            'titulo' => "Definir nova data de vencimento da ordem $ordem->codigo",
            'ordem' => $ordem
        ];

        return view('Ordens/Transacoes/editar', $data);
    }

    public function atualizar()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        $validacao = service('validation');

        // Recupero o post da requisição
        $post = $this->request->getPost();

        $regras = [
            'data_vencimento' => 'required',
        ];

        $mensagens = [   // Errors
            'data_vencimento' => [
                'required' => 'Por favor informe a nova data de vencimento',
            ],
        ];

        $validacao->setRules($regras, $mensagens);

        if ($validacao->withRequest($this->request)->run() == false) {

            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = $validacao->getErrors();

            return $this->response->setJSON($retorno);
        }

        if($post['data_vencimento'] < date('Y-m-d')){
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['data_vencimento'=> 'A data de vencimento não pode ser menor que a data atual'];

            return $this->response->setJSON($retorno);
        }

        $ordem = $this->ordemModel->buscaOrdemOu404($post['codigo']);

        $transacao = $this->transacaoModel->where('ordem_id', $ordem->id)->first();

        if($transacao === null){
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['data_vencimento'=> "Não encontramos uma transação associada à ordem de serviço $ordem->codigo"];

            return $this->response->setJSON($retorno);
        }

        if($transacao->status === 'paid'){

            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['data_vencimento'=> "Só é possível editar a data de vencimento de uma ordem que ainda não foi paga. No momento ela está " . $ordem->exibeSituacao()];

            return $this->response->setJSON($retorno);
        }

        if($post['data_vencimento'] ===  $transacao->expire_at){

            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['data_vencimento'=> "A nova data de vencimento não pode igual a data de vencimento atual"];

            return $this->response->setJSON($retorno);
        }

        if($post['data_vencimento'] < substr($transacao->expire_at, 0, 10)){

            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['data_vencimento'=> "Não é possível antecipar a data de vencimento do boleto"];

            return $this->response->setJSON($retorno);
        }

        $transacao->expire_at = $post['data_vencimento'];

        $ordem->transacao = $transacao;

        $objetoOperacao = new Operacoes($ordem);

        $objetoOperacao->alteraVencimentoTransacao();

        if(isset($ordem->erro_transacao)){
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['data_vencimento'=> $ordem->erro_transacao];

            return $this->response->setJSON($retorno);
        }

        session()->setFlashdata('sucesso', 'Nova data de vencimento definida com sucesso!');
        return $this->response->setJSON($retorno);
    }
}
