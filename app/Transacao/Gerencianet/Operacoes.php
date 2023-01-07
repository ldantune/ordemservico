<?php

namespace App\Transacao\Gerencianet;

use Gerencianet\Exception\GerencianetException;
use Gerencianet\Gerencianet;
use App\Traits\OrdemTrait;

class Operacoes
{

  use OrdemTrait;

  private $options = [];
  private $gerenciaNetDesconto;
  private $ordem;
  private $formaPagamento;

  //Models
  private $ordemModel;
  private $transacaoModel;
  private $eventoModel;

  public function __construct(object $ordem = null, object $formaPagamento = null)
  {
    $this->options = [
      'client_id' => getenv('gerenciaNetClientId'),
      'client_secret' => getenv('gerenciaNetClientSecret'),
      'sandbox' => getenv('gerenciaNetSandbox'), // altere conforme o ambiente (true = homologação e false = producao)
      'timeout' => 60,
    ];

    $this->gerenciaNetDesconto = (int) getenv('gerenciaNetDesconto');

    $this->ordem = $ordem;
    $this->formaPagamento = $formaPagamento;

    $this->ordemModel = new \App\Models\OrdemModel();
    $this->transacaoModel = new \App\Models\TransacaoModel();
    $this->eventoModel = new \App\Models\EventoModel();
  }
}
