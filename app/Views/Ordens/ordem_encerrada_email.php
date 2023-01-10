<h3 class="text-center">Olá, <?php echo esc($ordem->nome); ?></h3>

<p>Sua ordem de serviço foi
  <strong>encerrada</strong>
</p>

<p>
  <strong>Informações da ordem de serviço</strong>
</p>
<p>
  <strong>
    Equipamento:
  </strong> <?php echo esc($ordem->equipamento); ?>
</p>

<p>
  <strong>
    Defeito:
  </strong> <?php echo esc($ordem->defeito != null ? $ordem->defeito : 'Não informado'); ?>
</p>

<p>
  <strong>
    Observações:
  </strong> <?php echo esc($ordem->observacoes != null ? $ordem->observacoes : 'Não informado'); ?>
</p>

<p>
  <strong>
    Parecer técnico:
  </strong> <?php echo esc($ordem->parecer_tecnico != null ? $ordem->parecer_tecnico : 'Não informado'); ?>
</p>

<p>
  <strong>
    Data de abertura:
  </strong> <?php echo date('d/m/Y H:i', strtotime($ordem->criado_em)); ?>
</p>

<?php if ($ordem->itens === null) : ?>
  <p>Nenhum item foi adicionado a ordem até o momento</p>
<?php else : ?>
  <?php
  $valorProduto = 0;
  $valorServicos = 0;

  foreach ($ordem->itens as $item) {
    if ($item->tipo === 'produto') {
      $valorProduto += $item->preco_venda * $item->item_quantidade;
    } else {
      $valorServicos += $item->preco_venda * $item->item_quantidade;
    }
  }
  ?>

  <p>
    <strong>
      Valores finais:
    </strong>
  </p>

  <p>
    <strong>
      Valores de produtos: R$&nbsp; <?php echo number_format($valorProduto, 2); ?>
    </strong>
  </p>
  <p>
    <strong>
      Valores de serviços: R$&nbsp; <?php echo number_format($valorServicos, 2); ?>
    </strong>
  </p>
  <p>
    <strong>
      Valores de desconto: R$&nbsp; <?php echo number_format($ordem->valor_desconto, 2); ?>
    </strong>
  </p>
  <p>
    <strong>
      Valor Total sem desconto: R$&nbsp; <?php echo number_format($valorProduto + $valorServicos, 2); ?>
    </strong>
  </p>

  <p>
    <strong>
      Valor Total com desconto: R$&nbsp; <?php echo number_format(($valorProduto + $valorServicos) - $ordem->valor_desconto, 2); ?>
    </strong>
  </p>

  <p>
    <strong>
      Forma de pagamento:&nbsp; <?php echo esc($ordem->forma_pagamento); ?>
    </strong>
  </p>
<?php endif; ?>

<hr>

<p>
  Não deixe de consultar <a target="_blank" href="<?php echo site_url("ordens/minhas") ?>">as suas ordens de serviços</a>
</p>

<small>Não é necessário responder esse e-mail</small>