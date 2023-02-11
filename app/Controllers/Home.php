<?php

namespace App\Controllers;

use App\Libraries\Autenticacao;
use App\Traits\ValidacoesTrait;

class Home extends BaseController
{
    use ValidacoesTrait;

    public function index()
    {
        $data = [
            'titulo' => 'Home'
        ];

        if(!$this->usuarioLogado()->temPermissaoPara('visualizar-home')){
            return view('Home/index_simples', $data);
        }

        return view('Home/index', $data);
    }
}
