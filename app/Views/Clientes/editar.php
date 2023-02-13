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

      <?php echo form_open('/', ['id' => 'form'], ['id' => "$cliente->id"]) ?>

      <?php echo $this->include('Clientes/_form'); ?>
      <div class="form-group mt-5 mb-2">
        <input id="btn-salvar" value="Salvar" class="btn btn-danger mr-2" type="submit">
        <a href="<?php echo site_url("clientes/exibir/$cliente->id"); ?>" class="btn btn-secondary ml-2">Voltar</a>
      </div>

      <?php echo form_close() ?>

    </div> <!-- ./block -->

  </div>

</div>

<?php echo $this->endSection() ?>



<?php echo $this->section('scripts') ?>
<script src="<?php echo site_url('recursos/vendor/loadingoverlay/loadingoverlay.min.js') ?>"></script>
<script src="<?php echo site_url('recursos/vendor/mask/jquery.mask.min.js') ?>"></script>
<script src="<?php echo site_url('recursos/vendor/mask/app.js') ?>"></script>
<script>
  $(document).ready(function() {

    <?php echo $this->include('Clientes/_checkmail'); ?>
    <?php echo $this->include('Clientes/_viacep'); ?>

    // $("#form").LoadingOverlay("show");
    $("#form").on('submit', function(e) {
      e.preventDefault();
      $.ajax({
        type: 'POST',
        url: '<?php echo site_url('clientes/atualizar'); ?>',
        data: new FormData(this),
        dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function() {
          $("#response").html('');
          $("#btn-salvar").val('Por favor aguarde...');
        },
        success: function(response) {
          $('.limpaDiv').html('');
          
          $("#btn-salvar").val('Salvar');
          $("#btn-salvar").removeAttr("disabled");
          $('[name=csrf_ordem]').val(response.token);

          if (!response.erro) {
            if (response.info) {
              $("#response").html('<div class="alert alert-info" role="alert">' + response.info + '</div>');
            } else {
              window.location.href = "<?php echo site_url("clientes/exibir/$cliente->id"); ?>";
            }
          }

          if (response.erro) {
            $("#response").html('<div class="alert alert-danger" role="alert">' + response.erro + '</div>');
            if (response.erros_model) {
              $.each(response.erros_model, function(key, value) {
                //$("#response").append('<ul class="list-unstyled"><li class="text-danger">' + value + '</li></ul>');
                $('.' + key).html(value);
              });
            }
          }
        },
        error: function() {
          alert('Não foi possível processar a solicitação. Por favor entre em contato com suporte técnico.');
          $("#btn-salvar").val('Salvar');
          $("#btn-salvar").removeAttr("disabled");
        }
      });
    });

    $("#form").submit(function() {
      $(this).find(":submit").attr('disabled', 'disabled');
    })
  });
</script>
<?php echo $this->endSection() ?>