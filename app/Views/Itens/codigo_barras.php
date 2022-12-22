<?php echo $this->extend('Layout/Autenticacao/principal_autenticacao') ?>

<?php echo $this->section('titulo') ?>
<?php echo $titulo; ?>
<?php echo $this->endSection() ?>


<?php echo $this->section('estilos') ?>

<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>
<div class="row" >
    <!-- Logo & Information Panel-->
    <div class="col-lg-7 mx-auto" >
        <div class="form d-flex  align-items-center bg-dark">
            <div class="content">
                <div class="mt-5" style="text-align: center;">
                    <h1 class=""><?php echo $titulo; ?></h1>
                </div>
                <div class="mt-5" style="text-align: center;">
                  <p><?php echo $item->codigo_barras ?></p>
                  <p><?php echo $item->codigo_interno ?></p>
                  <p><button class="btn btn-sm btn-primary" onclick="window.print()">Imprimir</button></p>
                </div>
            </div>
        </div>
    </div>
   
</div>
<?php echo $this->endSection() ?>


<?php echo $this->section('scripts') ?>

<?php echo $this->endSection() ?>