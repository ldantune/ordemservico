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
      'client_id' => env('gerenciaNetClientId'),
      'client_secret' => env('gerenciaNetClientSecret'),
      'sandbox' => env('gerenciaNetSandbox'), // altere conforme o ambiente (true = homologação e false = producao)
      'timeout' => 60,
    ];

    $this->gerenciaNetDesconto = (int) env('gerenciaNetDesconto');

    $this->ordem = $ordem;
    $this->formaPagamento = $formaPagamento;

    $this->ordemModel = new \App\Models\OrdemModel();
    $this->transacaoModel = new \App\Models\TransacaoModel();
    $this->eventoModel = new \App\Models\EventoModel();
  }

  public function registraBoleto()
  {

    foreach ($this->ordem->itens as $item) {

      $itemBoleto = [
        'name' => $item->nome, // nome do item, produto ou serviço
        'amount' => (int) $item->item_quantidade, // quantidade
        'value' => (int) str_replace([',', '.'], '', $item->preco_venda)
      ];

      $items[] = $itemBoleto;
    }

    //TODO: Url de notificações
    $urlNotificacoes = 'https://eo64fq5651r4abx.m.pipedream.net';
    $metadata = array('notification_url' => $urlNotificacoes);

    $customer = [
      'name' => $this->ordem->nome, // nome do cliente
      'cpf' => str_replace(['.', '-'], '', $this->ordem->cpf), // cpf válido do cliente
      //'phone_number' => str_replace(['(', ')', ' ', '-'], '', $this->ordem->telefone), // telefone do cliente
      'email' => $this->ordem->email,
    ];

    $discount = [ // configuração de descontos
      'type' => 'percentage', // tipo de desconto a ser aplicado
      'value' => $this->gerenciaNetDesconto // valor de desconto 
    ];

    $configurations = [ // configurações de juros e mora
      'fine' => 200, // porcentagem de multa
      'interest' => 33 // porcentagem de juros
    ];

    $bankingBillet = [
      'expire_at' => $this->ordem->data_vencimento, // data de vencimento do titulo
      'message' => "Boleto referente à ordem de serviço " . $this->ordem->codigo, // mensagem a ser exibida no boleto
      'customer' => $customer,
      'discount' => $discount,
      //'conditional_discount' => $conditional_discount
    ];

    $payment = [
      'banking_billet' => $bankingBillet // forma de pagamento (banking_billet = boleto)
    ];

    $body = [
      'items' => $items,
      'metadata' => $metadata,
      'payment' => $payment
    ];


    try {

      $api = new Gerencianet($this->options);

      $pay_charge = $api->oneStep([], $body);

      if (isset($pay_charge['error'])) {

        $this->ordem->erro_transacao = $pay_charge['error_description'];


        return $this->ordem;
      }

      $objetoRetorno = json_decode(json_encode($pay_charge));

      $this->preparaOrdemParaEncerrar($this->ordem, $this->formaPagamento);

      //Atualizamos a ordem
      $this->ordemModel->save($this->ordem);

      $transacao = new \App\Entities\Transacao();

      $transacao->ordem_id = $this->ordem->id;

      $transacao->charge_id = $objetoRetorno->data->charge_id;

      $transacao->barcode = $objetoRetorno->data->barcode;

      $transacao->link = $objetoRetorno->data->link;

      $transacao->pdf = $objetoRetorno->data->pdf->charge;

      $transacao->expire_at = $objetoRetorno->data->expire_at;

      $transacao->status = $objetoRetorno->data->status;

      $transacao->total = $objetoRetorno->data->total / 100;

      //Salva a transação
      $this->transacaoModel->save($transacao);

      //Cria o atributo transação
      $this->ordem->transacao = $transacao;

      //Criação do evento
      $tituloEvento = "Boleto para a ordem " . $this->ordem->codigo . ", cliente " . $this->ordem->nome;
      $dias = $this->ordem->defineDataVencimentoEvento($objetoRetorno->data->expire_at);

      $this->eventoModel->cadastraEvento('ordem_id', $tituloEvento, $this->ordem->id, $dias);

      return $this->ordem;

      // echo '<pre>';
      // print_r($pay_charge);
      // echo '<pre>';
      // exit;
    } catch (GerencianetException $e) {
      print_r($e);
      print_r($e->code);
      print_r($e->error);
      print_r($e->errorDescription);
    } catch (\Exception $e) {
      print_r($e->getMessage());
    }
  }

  public function alteraVencimentoTransacao()
  {
    // $charge_id refere-se ao ID da transação gerada anteriormente
    $params = [
      'id' => $this->ordem->transacao->charge_id,
    ];

    $body = [
      'expire_at' => $this->ordem->transacao->expire_at,
      //'expire_at' => '2023-01-12',
    ];

    try {
      $api = new Gerencianet($this->options);

      $charge = $api->updateBillet($params, $body);

      if ($charge['code'] != 200) {
        $this->ordem->erro_transacao = $charge['error_description'];

        return $this->ordem;
      }

      $this->transacaoModel->save($this->ordem->transacao);

      //Criação do evento
      $dias = $this->ordem->defineDataVencimentoEvento($this->ordem->transacao->expire_at);

      $this->eventoModel->atualizaEvento('ordem_id', $this->ordem->id, $dias);

      $this->marcarOrdemComoAtualizada();

      return $this->ordem;
    } catch (GerencianetException $e) {
      echo '<pre>';
      print_r($e->code);
      echo '<pre>';
      print_r($e->error);
      echo '<pre>';
      print_r($e->errorDescription);
    } catch (\Exception $e) {
      echo '<pre>';
      print_r($e->getMessage());
    }
  }

  public function cancelarTransacao()
  {
    // $charge_id refere-se ao ID da transação ("charge_id")
    $params = [
      'id' => $this->ordem->transacao->charge_id
    ];

    try {
      $api = new Gerencianet($this->options);
      $charge = $api->cancelCharge($params, []);

      if ($charge['code'] != 200) {
        $this->ordem->erro_transacao = $charge['error_description'];

        return $this->ordem;
      }



      $this->ordem->transacao->status = 'canceled';
      $this->transacaoModel->save($this->ordem->transacao);

      $this->ordem->situacao = 'cancelada';
      $this->ordemModel->save($this->ordem);

      $this->eventoModel->where('ordem_id', $this->ordem->id)->delete();

      return $this->ordem;
    } catch (GerencianetException $e) {
      print_r($e->code);
      print_r($e->error);
      print_r($e->errorDescription);
    } catch (\Exception $e) {
      print_r($e->getMessage());
    }
  }

  public function reenviarBoleto()
  {

    // $charge_id refere-se ao ID da transação ("charge_id")
    $params = [
      'id' => $this->ordem->transacao->charge_id
    ];

    $body = [
      'email' => $this->ordem->email
    ];

    try {
      $api = new Gerencianet($this->options);
      $charge = $api->resendBillet($params, $body);

      if ($charge['code'] != 200) {
        $this->ordem->erro_transacao = $charge['error_description'];

        return $this->ordem;
      }
      return $this->ordem;
    } catch (GerencianetException $e) {
      print_r($e->code);
      print_r($e->error);
      print_r($e->errorDescription);
    } catch (\Exception $e) {
      print_r($e->getMessage());
    }
  }

  public function consultarTransacao()
  {

    // $charge_id refere-se ao ID da transação ("charge_id")
    $params = [
      'id' => $this->ordem->transacao->charge_id
    ];



    try {
      $api = new Gerencianet($this->options);
      $charge = $api->detailCharge($params, []);

      if ($charge['code'] != 200) {
        $this->ordem->erro_transacao = $charge['error_description'];

        return $this->ordem;
      }
      $this->ordem->historico = $charge['data']['history'];

      return $this->ordem;
    } catch (GerencianetException $e) {
      print_r($e->code);
      print_r($e->error);
      print_r($e->errorDescription);
    } catch (\Exception $e) {
      print_r($e->getMessage());
    }
  }

  public function marcarComoPago()
  {

    // $charge_id refere-se ao ID da transação ("charge_id")
    $params = [
      'id' => $this->ordem->transacao->charge_id
    ];



    try {
      $api = new Gerencianet($this->options);
      $charge = $api->settleCharge($params, []);


      if ($charge['code'] != 200) {
        $this->ordem->erro_transacao = $charge['error_description'];

        return $this->ordem;
      }

      $this->ordem->transacao->status = 'settled';

      $this->encerrarOrdemServico();

      return $this->ordem;
    } catch (GerencianetException $e) {
      print_r($e->code);
      print_r($e->error);
      print_r($e->errorDescription);
    } catch (\Exception $e) {
      print_r($e->getMessage());
    }
  }

  public function consultaNotificacao(string $tokenNotificacao)
  {

    $params = [
      'token' => $tokenNotificacao
    ];

    try {
      $api = new Gerencianet($this->options);
      $chargeNotification = $api->getNotification($params, []);
      // Para identificar o status atual da sua transação você deverá contar o número de situações contidas no array, pois a última posição guarda sempre o último status. Veja na um modelo de respostas na seção "Exemplos de respostas" abaixo.

      // Veja abaixo como acessar o ID e a String referente ao último status da transação.

      // Conta o tamanho do array data (que armazena o resultado)
      $i = count($chargeNotification["data"]);
      // Pega o último Object chargeStatus
      $ultimoStatus = $chargeNotification["data"][$i - 1];
      // Acessando o array Status
      $status = $ultimoStatus["status"];
      // Obtendo o ID da transação        
      $charge_id = $ultimoStatus["identifiers"]["charge_id"];
      // Obtendo a String do status atual
      $statusAtual = $status["current"];

      $transacao = $this->transacaoModel->where('charge_id', $charge_id)->first();

      if ($transacao != null) {
        $transacao->status = $statusAtual;

        if ($transacao->hasChanged()) {

          $this->ordem = $this->ordemModel->find($transacao->ordem_id);

          if ($this->ordem != null) {

            $this->ordem->transacao = $transacao;

            if ($this->ordem->transacao->status === 'canceled') {

              $this->ordem->situacao = 'cancelada';

              $this->ordemModel->save($this->ordem);

              $this->transacaoModel->save($transacao);

              $this->eventoModel->where('ordem_id', $this->ordem->id)->delete();
            }

            if ($this->ordem->transacao->status === 'paid') {
              $this->encerrarOrdemServico();
            }

            if ($this->ordem->transacao->status === 'unpaid') {
              echo 'Ordem não paga';
              $this->ordem->situacao = 'nao_pago';

              $this->ordemModel->save($this->ordem);

              $this->transacaoModel->save($transacao);
            }

            if ($this->ordem->transacao->status === 'settled') {

              $this->encerrarOrdemServico();
            }
          }
        }
      }
    } catch (GerencianetException $e) {
      print_r($e->code);
      print_r($e->error);
      print_r($e->errorDescription);
    } catch (\Exception $e) {
      print_r($e->getMessage());
    }
  }


  private function marcarOrdemComoAtualizada()
  {
    unset($this->ordem->transacao);
    $this->ordem->atualizado_em = date('Y-m-d H:i:s');
    $this->ordemModel->protect(false)->save($this->ordem);
  }

  private function encerrarOrdemServico()
  {

    $this->transacaoModel->save($this->ordem->transacao);

    $this->ordem->situacao = 'encerrada';

    $this->ordemModel->save($this->ordem);

    $this->gerenciaEstoqueProduto($this->ordem);

    $this->eventoModel->where('ordem_id', $this->ordem->id)->delete();
  }
}
