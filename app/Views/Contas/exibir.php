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


      
      <h5 class="cart-title mt-2"><?php echo esc($conta->razao); ?></h5>
      <hr class="border-secondary">

      <p class="card-text">CNPJ:  <?php echo esc($conta->cnpj); ?></p>
      <p class="card-text">Valor Conta R$&nbsp; <?php echo esc(number_format($conta->valor_conta, 2)); ?></p>
      <p class="contributions mt-0"> <?php echo $conta->exibeSituacao() ?></p>
      <p class="card-text">Criado <?php echo $conta->criado_em->humanize(); ?></p>
      <p class="card-text">Atualizado <?php echo $conta->atualizado_em; ?></p>

      <!-- Example single danger button -->
      <div class="btn-group">
        <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
          Ações
        </button>
        <div class="dropdown-menu">
          <a class="dropdown-item" href="<?php echo site_url("contas/editar/$conta->id"); ?>">Editar conta</a>

          <div class="dropdown-divider"></div>

          <?php if ($conta->deletado_em == null) : ?>
            <a class="dropdown-item" href="<?php echo site_url("contas/excluir/$conta->id"); ?>">Excluir conta</a>
          <?php else : ?>
            <a class="dropdown-item" href="<?php echo site_url("contas/desfazerexclusao/$conta->id"); ?>">Recuperar conta</a>
          <?php endif; ?>
          
        </div>
      </div>

      <a href="<?php echo site_url("contas"); ?>" class="btn btn-secondary ml-2">Voltar</a>
    </div> <!-- ./block -->
  </div>
</div>
<?php echo $this->endSection() ?>


<?php echo $this->section('scripts') ?>

<?php echo $this->endSection() ?>