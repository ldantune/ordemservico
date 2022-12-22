<?php echo $this->extend('Layout/principal') ?>

<?php echo $this->section('titulo') ?>
<?php echo $titulo; ?>
<?php echo $this->endSection() ?>


<?php echo $this->section('estilos') ?>

<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>
<div class="row">
  <div class="col-lg-3">
    <div class="user-block block">


      <?php if ($item->tipo === 'produto') : ?>
        <div class="text-center">
          <?php if ($item->imagem == null) : ?>
            <img src="<?php echo site_url('recursos/img/item_sem_imagem.jpg'); ?>" class="card-img-top" style="width: 90%;" alt="Usuário sem imagem">
          <?php else : ?>
            <img src="<?php echo site_url("itens/imagem/$item->imagem"); ?>" class="card-img-top" style="width: 90%;" alt="<?php echo esc($item->nome); ?>">
          <?php endif; ?>

          <a href="<?php echo site_url("itens/editarimagem/$item->id") ?>" class="btn btn-outline-primary btn-sm mt-3">Alterar imagem</a>
        </div>
        <hr class="border-secondary">
      <?php endif; ?>



      <h5 class="cart-title mt-2"><?php echo esc($item->nome); ?></h5>
      <p class="contributions mt-0"> <?php echo $item->exibeTipo() ?></p>
      <p class="contributions mt-0">Estoque: <?php echo $item->exibeEstoque() ?></p>
      <p class="contributions mt-0">Preço Venda: R$&nbsp; <?php echo $item->preco_venda ?></p>
      <p class="contributions mt-0">
        <a class="btn btn-sm" target="_black" href="<?php echo site_url("itens/codigobarras/$item->id") ?>">Gerar código de barras&nbsp;<i class="fa fa-barcode" aria-hidden="true"></i></a>
      </p>
      <p class="contributions mt-0"> <?php echo $item->exibeSituacao() ?></p>
      <p class="card-text">Criado <?php echo $item->criado_em->humanize(); ?></p>
      <p class="card-text">Atualizado <?php echo $item->atualizado_em->humanize(); ?></p>

      <!-- Example single danger button -->
      <div class="btn-group">
        <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
          Ações
        </button>
        <div class="dropdown-menu">
          <a class="dropdown-item" href="<?php echo site_url("itens/editar/$item->id"); ?>">Editar item</a>

          <div class="dropdown-divider"></div>

          <?php if ($item->deletado_em == null) : ?>
            <a class="dropdown-item" href="<?php echo site_url("itens/excluir/$item->id"); ?>">Excluir item</a>
          <?php else : ?>
            <a class="dropdown-item" href="<?php echo site_url("itens/desfazerexclusao/$item->id"); ?>">Recuperar item</a>
          <?php endif; ?>

        </div>
      </div>

      <a href="<?php echo site_url("itens"); ?>" class="btn btn-secondary ml-2">Voltar</a>
    </div> <!-- ./block -->
  </div>
</div>
<?php echo $this->endSection() ?>


<?php echo $this->section('scripts') ?>

<?php echo $this->endSection() ?>