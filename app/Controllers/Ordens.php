<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Entities\Ordem;
use App\Traits\OrdemTrait;

class Ordens extends BaseController
{
    use OrdemTrait;
    
    private $ordemModel;
    private $transacaoModel;

    public function __construct()
    {
        $this->ordemModel = new \App\Models\OrdemModel();
        $this->transacaoModel = new \App\Models\TransacaoModel();
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

    public function detalhes(string $codigo = null){

        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        $this->preparaItensDaOrdem($ordem);

        $transacao = $this->transacaoModel->where('ordem_id', $ordem->id)->first();

        if($transacao !== null){

            $ordem->transacao = $transacao;

        }

        $data = [
            'titulo' => "Detalhando a ordem de serviço $ordem->codigo",
            'ordem' => $ordem,
        ];

        return view('Ordens/detalhes', $data);
        
    }

    public function editar(string $codigo = null){
        
        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        if($ordem->situacao === 'encerrada')
        {
            return redirect()->back()->with("info", "Esta ordem não pode ser editada,pois encontra-se " .ucfirst($ordem->situacao));
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

        if($ordem->situacao === 'encerrada')
        {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model']  = ['situacao' => "Esta ordem não pode ser editada,pois encontra-se " .ucfirst($ordem->situacao)];

            return $this->response->setJSON($retorno);
        }

        //Preenchemos os atributos do usuário com os valores do POST
        $ordem->fill($post);

        if ($ordem->hasChanged() == false) {
            $retorno['info'] = 'Não há dados para serem atualizados';
            return $this->response->setJSON($retorno);
        }

        if ($this->ordemModel->protect(false)->save($ordem)) {

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            return $this->response->setJSON($retorno);
        }

        //Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->ordemModel->errors();

        return $this->response->setJSON($retorno);

    }
}
