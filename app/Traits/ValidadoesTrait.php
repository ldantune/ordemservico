<?php

namespace App\Traits;

trait ValidacoesTrait
{
  public function consultaViaCep(string $cep) : array{

    $cep = str_replace('-', '', $cep);

    $url = "https://viacep.com.br/ws/{$cep}/json/";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $resposta = curl_exec($ch);

    $erro = curl_error($ch);

    $retorno = [];
    if($erro){

      $retorno['erro'] = $erro;

      return $retorno;
    }

    $consulta = json_decode($resposta);

    if(isset($consulta->erro) && !isset($consulta->cep)){

      session()->set('blockCep', true);

      $retorno['erro'] = '<span class="text-danger">Informe um CEP válido</span>';

      return $retorno;

    }

    // {
    //   "cep": "38025-370",
    //   "logradouro": "Rua Tobias Rosa",
    //   "complemento": "",
    //   "bairro": "Nossa Senhora da Abadia",
    //   "localidade": "Uberaba",
    //   "uf": "MG",
    //   "ibge": "3170107",
    //   "gia": "",
    //   "ddd": "34",
    //   "siafi": "5401"
    //   }

    session()->set('blockCep', false);

    $retorno['endereco'] = esc($consulta->logradouro);
    $retorno['bairro'] = esc($consulta->bairro);
    $retorno['cidade'] = esc($consulta->localidade);
    $retorno['estado'] = esc($consulta->uf);
    $retorno['cep'] = esc($consulta->cep);

    return $retorno;
  }
}