<?php echo $this->extend('Layout/principal') ?>

<?php echo $this->section('titulo') ?>
<?php echo $titulo; ?>
<?php echo $this->endSection() ?>


<?php echo $this->section('estilos') ?>

<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>
<div class="row">

  <div class="col-lg-6">

    <div class="block">

      <div id="response">

      </div>

      <?php echo form_open('/', ['id' => 'form'], ['id' => "$usuario->id"]) ?>

      <?php echo $this->include('Usuarios/_form'); ?>

      <div class="block-body">
        <div class="form-group mt-5 mb-2">



          <input id="btn-salvar" value="Salvar" class="btn btn-danger mr-2" type="submit">
          <a href="<?php echo site_url("usuarios/exibir/$usuario->id"); ?>" class="btn btn-secondary ml-2">Voltar</a>
        </div>
      </div>

      <?php echo form_close() ?>

    </div> <!-- ./block -->

  </div>

</div>

<?php echo $this->endSection() ?>


<?php echo $this->section('scripts') ?>

<?php echo $this->endSection() ?>