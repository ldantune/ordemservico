<style>
  #body-pdf {
    font-family: Arial, Helvetica, sans-serif;
  }

  #pdf {
    font-family: Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
  }

  #pdf td,
  #pdf th {
    border: 1px solid #ddd;
    padding: 8px;
  }

  #pdf tr:nth-child(even) {
    background-color: #f2f2f2;
  }

  #pdf tr:hover {
    background-color: #ddd;
  }

  #pdf th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #04AA6D;
    color: white;
  }

  .color {
    color: #04AA6D;
  }
</style>

<div id="body-pdf">

  <?php if (empty($ordens)) : ?>
    <h3 class="color">Não há ordens em aberto até o momento</h3>
  <?php else : ?>
    
    <div>
      <h3 class="color"><?php echo $titulo ?></h3>
      <h4 class="color"><?php echo $periodo ?></h4>
    </div>
    <table id="pdf">
      <thead>
        <tr>
          <th scope="col">Ordem</th>
          <th scope="col">Cliente</th>
          <th scope="col">CPF</th>
          <th scope="col">Data última atualização</th>
          <th scope="col">Situação</th>
          <th scope="col">Valor da ordem</th>
        </tr>
      </thead>
      <tbody>

        <?php
          $valorTotal = 0;
        ?>

        <?php foreach ($ordens as $ordem) : ?>

          <?php
             $valorTotal += $ordem->valor_ordem;
          ?>
          <tr>
            <td><?php echo esc($ordem->codigo); ?></td>
            <td><?php echo esc($ordem->nome); ?></td>
            <td><?php echo esc($ordem->cpf); ?></td>
            <td><?php echo ($ordem->deletado_em === null ? date_format($ordem->atualizado_em, 'H:m d-m-Y') : date_format($ordem->deletado_em, 'd-m-Y H:m')); ?></td>
            <td><?php echo $ordem->exibeSituacao(); ?></td>
            <td>R$ <?php echo number_format($ordem->valor_ordem, 2); ?></td>
          </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="4"></td>
            <td style="text-align: center; font-weight: 900;">Total</td>
            <td>R$ <?php echo number_format($valorTotal, 2); ?></td>
        </tr>
      </tbody>
      
    </table>
  <?php endif; ?>


</div>