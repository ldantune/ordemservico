<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;

class Usuarios extends BaseController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new \App\Models\UsuarioModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando os usuários do sistema',
        ];

        return view('Usuarios/index', $data);
    }

    public function recuperaUsuarios(){
        // if(!$this->request->isAJAX()){
        //     return redirect()->back();
        // }

        $atributos = [
            'id',
            'nome',
            'email',
            'ativo',
            'imagem'
        ];

        $usuarios = $this->usuarioModel->select($atributos)
                                       ->findAll();

        $data = [];
        foreach($usuarios as $usuario){

            $data[] = [
                'imagem' => $usuario->imagem,
                'nome' => anchor("usuarios/exibir/$usuario->id", esc($usuario->nome), 'title="Exibir usuário '.esc($usuario->nome).'"'),
                'email' => $usuario->email,
                'ativo' => ($usuario->ativo == true ? '<i class="fa fa-unlock text-success"></i>&nbsp;Ativo' : '<i class="fa fa-lock text-warning"></i>&nbsp;Inativo'),
            ];
        }

        $retorno = [
            'data' => $data
        ];

        return $this->response->setJSON($retorno);
    }

    public function exibir (int $id = null){
        $usuario  = $this->buscaUsuarioOu404($id);

        $data = [
            'titulo' => "Detalhando o usuário ".esc($usuario->nome),
            'usuario' => $usuario
        ];

        return view('Usuarios/exibir', $data);
    }

    public function editar (int $id = null){
        $usuario  = $this->buscaUsuarioOu404($id);

        $data = [
            'titulo' => "Editando o usuário ".esc($usuario->nome),
            'usuario' => $usuario
        ];

        return view('Usuarios/editar', $data);
    }


    /**
     * Método que recupera o usuário
     *
     * @param integer $id
     * @return Expeptions|object
     */
    private function buscaUsuarioOu404(int $id = null){
        if(!$id || !$usuario = $this->usuarioModel->withDeleted(true)->find($id)){
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o usuário $id");
        }

        return $usuario;
    }
}
