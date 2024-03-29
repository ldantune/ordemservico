<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Logs extends BaseController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new \App\Models\UsuarioModel();

        helper('filesystem');
    }

    public function index()
    {
        if (!$this->usuarioLogado()->temPermissaoPara('visualizar-logs')) {

            $this->registraAcaoDoUsuario('tentou listar os logs do sistema');

            return redirect()->back()->with('atencao', $this->usuarioLogado()->nome . ', você não tem permissão para acessar esse menu.');
        }

        $data = [
            'titulo' => 'Analizar Logs',
            'datasDisponiveis' => $this->recuperaDatasLog()
        ];
        
        return view('Logs/index', $data); 
    }

    public function buscausuarios()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $termo = $this->request->getGet('termo');

        $usuarios = $this->usuarioModel->recuperaUsuarioParaLog($termo);

        return $this->response->setJSON($usuarios);

    }

    public function consultar()
    {
        if($this->request->getMethod() !== 'post')
        {
            return redirect()->back();
        }

        $validacao = service('validation');

        $regras = [
            'data_escolhida' => 'required',
            'usuario_id' => 'required',
        ];

        

        $validacao->setRules($regras);

        if(!$validacao->withRequest($this->request)->run()){
            return redirect()->back()
                                ->with('atencao', 'Verifique os erros abaixo e tente novamente')
                                ->with('erros_model', $validacao->getErrors())  ;
        }

        $dataEscolhida = $this->request->getPost('data_escolhida');
        $usuarioId = $this->request->getPost('usuario_id');

        $usuario = $this->usuarioModel->select('nome')->find($usuarioId);

        $reusltadoLog = $this->consultaLog($dataEscolhida, $usuarioId);

        if($reusltadoLog === null){
            $dataEscolhida = date('d/m/Y', strtotime($dataEscolhida));

            session()->remove('resultadoLog');
            return redirect()->back()->with('info', "Não foram encontrados registros com os seguintes parâmetros: </br></br> Data: $dataEscolhida </br> Usuário: $usuario->nome");
        }

        session()->set('resultadoLog', $reusltadoLog);
        return redirect()->back()->with('sucesso', 'Registros encontrados');

        
    }
    private function recuperaDatasLog()
    {
        $arquivosLogs = get_filenames(WRITEPATH . 'logs/');

        $datasDisponiveis = [];

        if (empty($arquivosLogs)) {
            return [];
        }

        foreach ($arquivosLogs as $key  => $arquivo) {

            if (strpos($arquivo, 'html')) {
                unset($arquivosLogs[$key]);
            } else {
                $datasDisponiveis[] = substr($arquivo, 4, 10);
            }
        }

        return $datasDisponiveis;
    }

    private function consultaLog(string $dataEscolhida = null, int $usuarioId = null)
    {
        $arquivo = WRITEPATH . "logs/log-$dataEscolhida.log";

        if (!is_file($arquivo)) {
            return null;
        }

        $procurarPor = "[ACAO-USUARIO-ID-$usuarioId]";

        $arquivo = file_get_contents($arquivo);

        $padrao = preg_quote($procurarPor, '/');

        $padrao = "/^.*$padrao.*\$/m";

        if(preg_match_all($padrao, $arquivo, $correspondencias)){
            $resultado = nl2br(implode("\n\r", $correspondencias[0]));

            return $resultado;
        }

        return null;
    }
}
