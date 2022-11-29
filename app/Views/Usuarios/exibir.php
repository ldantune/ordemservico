<?php echo $this->extend('Layout/principal') ?>

<?php echo $this->section('titulo') ?>
<?php echo $titulo; ?>
<?php echo $this->endSection() ?>


<?php echo $this->section('estilos') ?>

<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>
<div class="row">
  <div class="col-lg-3">
    <div class="block">
      <div class="text-center">
        <?php if ($usuario->imagem == null) : ?>
          <img src="<?php echo site_url('recursos/img/usuario_sem_imagem.png'); ?>" class="card-img-top" style="width: 90%;" alt="Usuário sem imagem">
        <?php else : ?>
          <img src="<?php echo site_url("usuarios/imagem/$usuario->imagem"); ?>" class="card-img-top" style="width: 90%;" alt="<?php echo esc($usuario->nome); ?>">
        <?php endif; ?>

        <a href="<?php echo site_url("usuarios/editarimagem/$usuario->id") ?>" class="btn btn-outline-primary btn-sm mt-3">Alterar imagem</a>
      </div>
      <hr class="border-secondary">
    </div>
  </div>
</div>
<?php echo $this->endSection() ?>


<?php echo $this->section('scripts') ?>

<?php echo $this->endSection() ?>