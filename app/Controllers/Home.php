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

    public function email()
    {
        //$email = \Config\Services::email();
        $email = service('email');

        $email->setFrom('no-reply@ordem.com', 'Ordem de serviço Ltda');
        $email->setTo('ldantune@gmail.com');


        $email->setSubject('Recuperação de senha');
        $email->setMessage('Iniciando a recuperação de senha.');

        if($email->send()){
            echo 'Email enviado';
        }else{
            $email->printDebugger();
        }
        
    }

    public function cep(){
        $cep = "38025-370";
        return $this->response->setJSON($this->consultaViaCep($cep));
    }

    public function barcode(){
        $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
        echo $generator->getBarcode('081231723897', $generator::TYPE_CODE_128);
    }

    public function validadeemail(){
        $email = "vokynemy@lyricspad.net";
        return $this->response->setJSON($this->consultaEmail($email));
    }
}
