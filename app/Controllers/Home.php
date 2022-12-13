<?php

namespace App\Controllers;

use App\Libraries\Autenticacao;

class Home extends BaseController
{
    public function index()
    {
        //dd(usuario_logado());
        $data = [
            'titulo' => 'Home'
        ];
        return view('Home/index', $data);
    }

    // public function login(){
    //     $autenticacao = service('autenticacao');

        

    //     //$autenticacao->login('bruen.ezequiel@yundt.com', '123456');
    //     $usuario = $autenticacao->pegaUsuarioLogado();
    //     dd($usuario->temPermissaoPara('listar-usuarios'));
    //     //$autenticacao->logout();
    //     //return redirect()->to(site_url('/'));

    //     //dd($autenticacao->estaLogado());
    // }
}
