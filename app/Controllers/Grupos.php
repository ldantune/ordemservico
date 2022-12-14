<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Grupo;

class Grupos extends BaseController
{
    private $grupoModel;
    private $grupoPermissaoModel;
    private $permissaoModel;

    public function __construct()
    {
        $this->grupoModel = new \App\Models\GrupoModel();
        $this->grupoPermissaoModel = new \App\Models\GrupoPermissaoModel();
        $this->permissaoModel = new \App\Models\PermissaoModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando os grupos de acesso ao sistema',
        ];

        return view('Grupos/index', $data);
    }

    public function recuperaGrupos()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $atributos = [
            'id',
            'nome',
            'descricao',
            'exibir',
            'deletado_em'
        ];

        $grupos = $this->grupoModel->select($atributos)
            ->withDeleted(true)
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [];
        foreach ($grupos as $grupo) {
            $data[] = [
                'nome' => anchor("grupos/exibir/$grupo->id", esc($grupo->nome), 'title="Exibir grupo ' . esc($grupo->nome) . '"'),
                'descricao' => $grupo->descricao,
                'exibir' => $grupo->exibeSituacao(),
            ];
        }

        $retorno = [
            'data' => $data
        ];

        return $this->response->setJSON($retorno);
    }

    public function exibir(int $id = null)
    {
        $grupo  = $this->buscaGrupoOu404($id);

        $data = [
            'titulo' => "Detalhando o grupo de acesso " . esc($grupo->nome),
            'grupo' => $grupo
        ];

        return view('Grupos/exibir', $data);
    }

    public function criar()
    {
        $grupo  = new Grupo();

        //dd($grupo);

        $data = [
            'titulo' => "Criando novo grupo de acesso ",
            'grupo' => $grupo
        ];

        return view('Grupos/criar', $data);
    }

    public function cadastrar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        // Recupero o post da requisi????o
        $post = $this->request->getPost();

        $grupo = new Grupo($post);

        if ($this->grupoModel->save($grupo)) {

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso! <br> <a class="btn btn-danger mt-2" href=' . site_url('grupos/criar') . '>Criar novo grupo de acesso</a>');

            $retorno['id'] = $this->grupoModel->getInsertID();
            return $this->response->setJSON($retorno);
        }

        //Retornamos os erros de valida????o
        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->grupoModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function editar(int $id = null)
    {
        $grupo  = $this->buscaGrupoOu404($id);

        if($grupo->id < 3){
            return redirect()
                ->back()
                ->with('atencao', 'O grupo ' .esc($grupo->nome). ' n??o pode ser editado ou exclu??do, conforme detalhado na exibi????o do mesmo.');
        }

        $data = [
            'titulo' => "Editando o grupo de acesso " . esc($grupo->nome),
            'grupo' => $grupo
        ];

        return view('Grupos/editar', $data);
    }

    public function atualizar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        // Recupero o post da requisi????o
        $post = $this->request->getPost();


        //Validamos a existencia do usu??rio
        $grupo = $this->buscaGrupoOu404($post['id']);

        if($grupo->id < 3){

            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';

            $retorno['erros_model'] = [
                'grupo' => 'O grupo <b class="text-white">' .esc($grupo->nome). '</b> n??o pode ser editado, conforme detalhado na exibi????o do mesmo.'
            ];
            return $this->response->setJSON($retorno);
        }

        //Preenchemos os atributos do usu??rio com os valores do POST
        $grupo->fill($post);

        if ($grupo->hasChanged() == false) {
            $retorno['info'] = 'N??o h?? dados para serem atualizados';
            return $this->response->setJSON($retorno);
        }

        if ($this->grupoModel->protect(false)->save($grupo)) {

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            return $this->response->setJSON($retorno);
        }

        //Retornamos os erros de valida????o
        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->grupoModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function excluir(int $id = null)
    {
        $grupo  = $this->buscaGrupoOu404($id);

        if($grupo->id < 3){
            return redirect()
                ->back()
                ->with('atencao', 'O grupo ' .esc($grupo->nome). ' n??o pode ser editado ou exclu??do, conforme detalhado na exibi????o do mesmo.');
        }

        if($grupo->deletado_em != null){
            return redirect()->back()->with('info', "Esse grupo de acesso j?? encotra-se excluido");
        }

        if ($this->request->getMethod() === 'post') {

            $this->grupoModel->delete($grupo->id);

            return redirect()->to(site_url("grupos"))->with('sucesso', 'Grupo de acesso '.esc($grupo->nome). ' exclu??do com sucesso!');
        }

        $data = [
            'titulo' => "Excluindo o grupo de acesso " . esc($grupo->nome),
            'usuario' => $grupo
        ];

        return view('Grupos/excluir', $data);
    }

    public function desfazerexclusao(int $id = null)
    {
        $grupo  = $this->buscaGrupoOu404($id);

        if($grupo->deletado_em == null){
            return redirect()->back()->with('info', "Apenas grupos de acesso exclu??dos podem ser recuparados");
        }

        
        $grupo->deletado_em = null;
        $this->grupoModel->protect(false)->save($grupo);
        return redirect()->back()->with('sucesso', 'Grupo de acesso ' .esc($grupo->nome). ' recuperado com sucesso!');

        
    }

    public function permissoes(int $id = null)
    {
        $grupo  = $this->buscaGrupoOu404($id);

        if($grupo->id == getenv('GRUPO_ADMINISTRADOR')){
            return redirect()
                ->back()
                ->with('info', 'N??o ?? necess??rio atribuir ou remover permiss??es de acesso para o grupo <b>' .esc($grupo->nome). '</b>, pois esse grupo ?? Administrador.');
        }

        if($grupo->id == getenv('GRUPO_CLIENTE')){
            return redirect()
                ->back()
                ->with('info', 'N??o ?? necess??rio atribuir ou remover permiss??es de acesso para o grupo de Clientes');
        }

        if($grupo->id > 2){

            $grupo->permissoes = $this->grupoPermissaoModel->recuperaPermissoesDoGrupo($grupo->id, 5);
            $grupo->pager = $this->grupoPermissaoModel->pager;

        }

        $data = [
            'titulo' => "Gerenciando as permiss??es do grupo de acesso " . esc($grupo->nome),
            'grupo' => $grupo
        ];

        if(!empty($grupo->permissoes)){
            $permissoesExistentes = array_column($grupo->permissoes, 'permissao_id');

            $data['permissoesDisponiveis'] = $this->permissaoModel->whereNotIn('id', $permissoesExistentes)->findAll();
        }else{
            $data['permissoesDisponiveis'] = $this->permissaoModel->findAll();
        }

        return view('Grupos/permissoes', $data);
    }

    public function salvarpermissoes()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        // Recupero o post da requisi????o
        $post = $this->request->getPost();


        //Validamos a existencia do usu??rio
        $grupo = $this->buscaGrupoOu404($post['id']);

        if(empty($post['permissao_id'])){
            //Retornamos os erros de valida????o
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['permissao_id' => 'Escolha uma ou mais permiss??es para salvar'];

            return $this->response->setJSON($retorno);
        }

        $permissaoPush = [];

        foreach($post['permissao_id'] as $permissao){
            array_push($permissaoPush,[
                'grupo_id' => $grupo->id,
                'permissao_id' => $permissao
            ]);
        }

        $this->grupoPermissaoModel->insertBatch($permissaoPush);

        session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
        return $this->response->setJSON($retorno);
    }

    public function removepermissao(int $principal_id = null){



        if ($this->request->getMethod() === 'post') {

            $this->grupoPermissaoModel->delete($principal_id);

            return redirect()->back()->with('sucesso', 'Permiss??o removida com sucesso!');
        }

        return redirect()->back();

        
    }

    /**
     * M??todo que recupera o grupo de acesso
     *
     * @param integer $id
     * @return Expeptions|object
     */
    private function buscaGrupoOu404(int $id = null)
    {
        if (!$id || !$grupo = $this->grupoModel->withDeleted(true)->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("N??o encontramos o grupo de acesso $id");
        }

        return $grupo;
    }
}
