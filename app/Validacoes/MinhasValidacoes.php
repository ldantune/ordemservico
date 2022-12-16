<?php

namespace App\Validacoes;

class MinhasValidacoes
{
  /**
   * 
   * @param string $cnpj
   * @param string $error
   * @see inspirado em https://gist.github.com/guisehn/3276302
   * @return bool
   */
  public function validaCNPJ(string $cnpj, string &$error = null): bool
  {


    $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);

    // Valida tamanho
    if (strlen($cnpj) != 14) {
      $error = 'Por favor digite um CNPJ válido';
      return false;
    }


    // Verifica se todos os digitos são iguais
    if (preg_match('/(\d)\1{13}/', $cnpj)) {
      $error = 'Por favor digite um CNPJ válido';
      return false;
    }


    // Valida primeiro dígito verificador
    for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
      $soma += $cnpj[$i] * $j;
      $j = ($j == 2) ? 9 : $j - 1;
    }

    $resto = $soma % 11;

    if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto)) {
      $error = 'Por favor digite um CNPJ válido';
      return false;
    }


    // Valida segundo dígito verificador
    for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
      $soma += $cnpj[$i] * $j;
      $j = ($j == 2) ? 9 : $j - 1;
    }

    $resto = $soma % 11;


    $resultado = $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);

    if ($resultado == false) {

      $error = 'Por favor digite um CNPJ válido';
      return false;
    } else {

      return true;
    }
  }
}
