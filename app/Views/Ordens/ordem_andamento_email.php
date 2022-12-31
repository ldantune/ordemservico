<h3 class="text-center">Olá, <?php echo esc($ordem->cliente->nome); ?></h3>

<p>Até o momento a sua ordem de serviço está com o status de <strong><?php echo esc(ucfirst($ordem->situacao)) ?></strong></p>

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
      Valores até o momento:
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
      Valor Total: R$&nbsp; <?php echo number_format($valorProduto + $valorServicos, 2); ?>
    </strong>
  </p>
<?php endif; ?>

<hr>

<p>
  Não deixe de consultar <a target="_blank" href="<?php echo site_url("ordens/minhas-ordens") ?>">as suas ordens de serviços</a>
</p>

<small>Não é necessário responder esse e-mail</small>