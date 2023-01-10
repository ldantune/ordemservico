<?php echo $this->extend('Layout/principal') ?>

<?php echo $this->section('titulo') ?>
<?php echo $titulo; ?>
<?php echo $this->endSection() ?>


<?php echo $this->section('estilos') ?>

<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>
<div class="row">
  <div class="col-lg-12" id="divPrincipalDetalhes">
    <div class="block">

      <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="pills-home-tab" data-toggle="pill" data-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Detalhes da Ordem</button>
        </li>

        <?php if (isset($ordem->evidencias)) : ?>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-profile-tab" data-toggle="pill" data-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Evidências da Ordem</button>
          </li>
        <?php endif; ?>
      </ul>
      <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">

          <div class="user-block text-center">
            <div class="user-title mb-4">
              <h5 class="cart-title mt-2"><?php echo esc($ordem->nome); ?></h5>
              <span>Ordem: <?php echo esc($ordem->codigo) ?></span>
            </div>
            <p class="contributions mt-0"> <?php echo $ordem->exibeSituacao() ?></p>
            <p class="contributions mt-0">Aberta por: <?php echo esc($ordem->usuario_abertura) ?></p>
            <p class="contributions mt-0">Responsável técnico: <?php echo esc($ordem->usuario_responsavel !== null ? $ordem->usuario_responsavel : "Não definido") ?></p>

            <?php if ($ordem->situacao === 'encerrada') : ?>


              <p class="contributions mt-0">Encerrada por: <?php echo esc($ordem->usuario_encerramento) ?></p>
            <?php endif; ?>


            <p class="card-text">Criado <?php echo $ordem->criado_em->humanize(); ?></p>
            <p class="card-text">Atualizado <?php echo $ordem->atualizado_em->humanize(); ?></p>

            <hr class="border-secondary">

            <?php if ($ordem->itens === null) : ?>

              <div class="contributions py-3">
                <p>Nenhum item foi adicionado à ordem</p>
              </div>
            <?php else : ?>
              <div class="table-responsive my-5">
                <table class="table table-striped text-left">
                  <thead>
                    <tr>
                      <th scope="col">Item</th>
                      <th scope="col">Tipo</th>
                      <th scope="col">Preço</th>
                      <th scope="col">Qtde</th>
                      <th scope="col">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>

                    <?php
                    $valorProdutos = 0;
                    $valorServicos = 0;
                    ?>

                    <?php foreach ($ordem->itens as $item) : ?>

                      <?php
                      if ($item->tipo === 'produto') {
                        $valorProdutos += $item->preco_venda * $item->item_quantidade;
                      } else {
                        $valorServicos += $item->preco_venda * $item->item_quantidade;
                      }
                      ?>

                      <tr>
                        <th scope="row"><?php echo ellipsize($item->nome, 32, .5); ?></th>
                        <td><?php echo esc(ucfirst($item->tipo)); ?></td>
                        <td>R$ <?php echo esc(number_format($item->preco_venda, 2)); ?></td>
                        <td><?php echo $item->item_quantidade ?></td>
                        <td>R$<?php echo esc(number_format($item->item_quantidade * $item->preco_venda, 2)) ?></td>
                      </tr>

                    <?php endforeach; ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td class="text-right font-weight-bold" colspan="4">
                        <label>Valor produtos: </label>
                      </td>
                      <td class="font-weight-bold">R$ <?php echo esc(number_format($valorProdutos, 2)); ?></td>
                    </tr>
                    <tr>
                      <td class="text-right font-weight-bold" colspan="4">
                        <label>Valor serviços: </label>
                      </td>
                      <td class="font-weight-bold">R$ <?php echo esc(number_format($valorServicos, 2)); ?></td>
                    </tr>
                    <tr>
                      <td class="text-right font-weight-bold" colspan="4">
                        <label>Valor desconto: </label>
                      </td>
                      <td class="font-weight-bold">R$ <?php echo esc(number_format($ordem->valor_desconto, 2)); ?></td>
                    </tr>
                    <tr>
                      <td class="text-right font-weight-bold" colspan="4">
                        <label>Valor total da ordem: </label>
                      </td>
                      <td class="font-weight-bold">R$ <?php echo esc(number_format($valorServicos + $valorProdutos, 2)); ?></td>
                    </tr>
                    <tr>
                      <td class="text-right font-weight-bold" colspan="4">
                        <label>Valor total com desconto: </label>
                      </td>
                      <td class="font-weight-bold">R$
                        <?php
                        $valorItens = $valorServicos + $valorProdutos;
                        echo esc(number_format($valorItens - $ordem->valor_desconto, 2));
                        ?>
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            <?php endif; ?>

          </div>
        </div>
        <?php if (isset($ordem->evidencias)) : ?>
          <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">

            <div class="col-md-12">
              <?php if (empty($ordem->evidencias)) : ?>
                <div class="user-block block col-lg-12">
                  <div class="text-center">
                    <p class="contributions text-warning mt-0">
                      Essa ordem de serviço, ainda não possui evidências
                    </p>
                  </div>
                </div>

              <?php else : ?>
                <div class="col-lg-12">
                  <div class="user-block block ">
                    <ul class="list-inline">
                      <?php foreach ($ordem->evidencias as $evidencia) : ?>
                        <li class="list-inline-item">
                          <div class="card" style="width: 8rem;">
                            <?php if ($ordem->isImage($evidencia->evidencia)) : ?>
                              <a data-toogle="tooltip" data-placement="top" title="Exibir imagem" target="_blank" class="btn btn-outline-danger mt-0" href="<?php echo site_url("ordensevidencias/arquivo/$evidencia->evidencia"); ?>">
                                <img alt="<?php echo $ordem->codigo ?>" width="42" src="<?php echo site_url("ordensevidencias/arquivo/$evidencia->evidencia") ?>" />
                              </a>
                            <?php else : ?>
                              <a data-toogle="tooltip" data-placement="top" title="Exibir PDF" target="_blank" class="btn btn-outline-danger mt-0" href="<?php echo site_url("ordensevidencias/arquivo/$evidencia->evidencia"); ?>">
                                PDF
                              </a>
                            <?php endif; ?>                           
                          </div>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
      <a href="<?php echo site_url("ordens/minhas"); ?>" class="btn btn-secondary ml-2 btn-sm">Voltar</a>
    </div> <!-- ./block -->
  </div>


</div>
<?php echo $this->endSection() ?>


<?php echo $this->section('scripts') ?>

<script src="<?php echo site_url('recursos/vendor/loadingoverlay/loadingoverlay.min.js') ?>"></script>

<script>
  $(document).ready(function() {
    $("#btn-enviar-email").on('click', function() {
      $("#divPrincipalDetalhes").LoadingOverlay("show", {
        image: "",
        text: "Enviando e-mail...",
        fontawesome: "fa fa-paper-plane",
      });
    });
  });
</script>

<?php echo $this->endSection() ?>