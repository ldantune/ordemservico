<?php

namespace App\Libraries;

use PhpParser\Builder\Function_;
use PhpParser\Node\Expr\FuncCall;

class Token
{
  private $token;

  public function __construct(string $token = null)
  {
    if ($token === null) {
      $this->token = bin2hex(random_bytes(16));
    }else{
      $this->token = $token;
    }
  }

  public function getValue(): string 
  {
    return $this->token;
  }

  public function getHash(): string
  {
    return hash_hmac("sha256", $this->token, getenv('CHAVE_RECUPERACAO_SENHA'));
  }
}
