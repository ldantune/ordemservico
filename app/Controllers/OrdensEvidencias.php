<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class OrdensEvidencias extends BaseController
{
    private $ordemModel;
    private $ordemEvidenciaModel;

    public function __construct()
    {
        $this->ordemModel = new \App\Models\OrdemModel();
        $this->ordemEvidenciaModel = new \App\Models\OrdemEvidenciaModel();
    }

    public function evidencias(string $codigo = null)
    {
        if(!$this->usuarioLogado()->temPermissaoPara('listar-ordens')){
            return redirect()->back()->with('atencao', $this->usuarioLogado()->nome. ', você não tem permissão para acessar esse menu.');
        }

        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        $ordem->evidencias = $this->ordemEvidenciaModel->select('evidencia')->where('ordem_id', $ordem->id)->findAll();

        $data = [
            'titulo' => "Gerenciando as evidências da ordem $ordem->codigo",
            'ordem' => $ordem,
        ];

        return view('Ordens/evidencias', $data);
    }

    public function upload()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        $validacao = service('validation');

        $regras = [
            'evidencias' => 'uploaded[evidencias]|max_size[evidencias,2048]|ext_in[evidencias,png,jpg,jpeg,webp,pdf]',
        ];

        $mensagens = [   // Errors
            'evidencias' => [
                'uploaded' => 'Por favor escolha uma ou mais evidências em formato de imagem ou pdf',
                'max_size' => 'Por favor escolha um arquivo com no máximo 2048mb',
                'ext_in' => 'Por favor escolha uma arquivo no formato png, jpg, jpeg, webp, pdf',
            ]
        ];

        $validacao->setRules($regras, $mensagens);

        if ($validacao->withRequest($this->request)->run() == false) {

            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = $validacao->getErrors();

            return $this->response->setJSON($retorno);
        }

        // Recupero o post da requisição
        $post = $this->request->getPost();

        //Validamos a existencia do item
        $ordem = $this->ordemModel->buscaOrdemOu404($post['codigo']);

        $evidencias = $this->request->getFiles('ordens/evidencias');

        foreach ($evidencias['evidencias'] as $evidencia) {

            if ($evidencia->getClientExtension() != 'pdf') {
                list($largura, $altura) = getimagesize($evidencia->getPathName());

                if ($largura < "500" || $altura < "500") {

                    $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
                    $retorno['erros_model'] = ['evidencias' => 'A imagem não pode ser menor do que 500 x 500 pixels'];

                    return $this->response->setJSON($retorno);
                }
            }
        }

        $arrayEvidencias = [];

        foreach ($evidencias['evidencias'] as $evidencia) {

            $caminhoImagem = $evidencia->store('ordens/evidencias');

            $caminhoImagem = WRITEPATH . "uploads/$caminhoImagem";

            if ($evidencia->getClientExtension() != 'pdf') {
                $this->manipulaImagem($caminhoImagem, $ordem->codigo);
            }
            

            array_push($arrayEvidencias, [
                'ordem_id' => $ordem->id,
                'evidencia' => $evidencia->getName(),
            ]);
        }

        $this->ordemEvidenciaModel->insertBatch($arrayEvidencias);

        session()->setFlashdata('sucesso', 'Evidências salva com sucesso!');


        return $this->response->setJSON($retorno);
    }

    public function arquivo(string $evidencia = null)
    {
        if($evidencia !== null){
            $this->exibeArquivo('ordens/evidencias', $evidencia);
        }
    }

    public function removerEvidencia(string $evidencia = null)
    {
        if($this->request->getMethod() !== 'post'){
            return redirect()->back();
        }

        if(!$this->usuarioLogado()->temPermissaoPara('editar-ordens')){
            return redirect()->back()->with('atencao', $this->usuarioLogado()->nome. ', você não tem permissão para acessar esse menu.');
        }

        $ordem = $this->ordemModel->buscaOrdemOu404($this->request->getPost('codigo'));

        if($evidencia !== null){
            $ordemEvidencia = $this->ordemEvidenciaModel->where('evidencia', $evidencia)->where('ordem_id', $ordem->id)->first();
            if($ordemEvidencia === null){
                return redirect()->back()->with('atencao', "Não encontramos a evidência $evidencia");
            }


            if($this->ordemEvidenciaModel->delete($ordemEvidencia->id)){
                $caminhoImagem = WRITEPATH . "uploads/ordens/evidencias/$ordemEvidencia->evidencia";

                if(is_file($caminhoImagem)){
                    unlink($caminhoImagem);
                }
            }

            return redirect()->back()->with('sucesso', 'Evidência removida com sucesso!');

        }else{
            return redirect()->back()->with('atencao', "Não encontramos a evidência $evidencia");
        }
    }

    private function manipulaImagem(string $caminhoImagem, string $codigo)
    {

        service('image')
            ->withFile($caminhoImagem)
            ->fit(500, 500, 'center')
            ->save($caminhoImagem);

        $anoAtual = date('Y');
        //Adding a Text Watermark
        \Config\Services::image('imagick')
            ->withFile($caminhoImagem)
            ->text("Ordem $anoAtual - Codigo $codigo", [
                'color'      => '#fff',
                'opacity'    => 0.5,
                'withShadow' => false,
                'hAlign'     => 'center',
                'vAlign'     => 'bottom',
                'fontSize'   => 20,
            ])
            ->save($caminhoImagem);
    }
}
