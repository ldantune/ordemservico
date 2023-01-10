<?php echo $this->extend('Layout/principal') ?>

<?php echo $this->section('titulo') ?>
<?php echo $titulo; ?>
<?php echo $this->endSection() ?>


<?php echo $this->section('estilos') ?>

<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>
<div class="row">

  <?php if ($ordem->situacao === 'aberta') : ?>
    <div class="col-lg-5">

      <div class="block">

        <div id="response">

        </div>

        <?php echo form_open_multipart('/', ['id' => 'form'], ['codigo' => "$ordem->codigo"]) ?>

        <div class="form-group">
          <label class="form-control-label">Escolha uma ou mais evidências ( imagem ou PDF)</label>
          <input type="file" name="evidencias[]" multiple class="form-control">
          <div class="limpaDiv text-danger evidencias"></div>
        </div>

        <div class="form-group mt-5 mb-2">
          <input id="btn-salvar" value="Salvar" class="btn btn-danger mr-2" type="submit">
          <a href="<?php echo site_url("ordens/detalhes/$ordem->codigo"); ?>" class="btn btn-secondary ml-2">Voltar</a>
        </div>

        <?php echo form_close() ?>

      </div> <!-- ./block -->

    </div>
  <?php endif; ?>


  <?php if (empty($ordem->evidencias)) : ?>
    <div class="user-block block col-lg-7">
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

                <?php if ($ordem->situacao === 'aberta') : ?>

                  <div class="card-body text-center">
                    <?php echo form_open("ordensevidencias/removerevidencia/$evidencia->evidencia", ['onSubmit' => 'return confirm("Tem certeza da exclusão?");'], ['codigo' => $ordem->codigo]); ?>
                      <button type="submit" class="btn btn-danger"><i class="fa fa-trash fa fa-lg"></i></button>

                    <?php echo form_close(); ?>
                  </div>

                <?php endif; ?>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php echo $this->endSection() ?>



<?php echo $this->section('scripts') ?>
<script src="<?php echo site_url('recursos/vendor/loadingoverlay/loadingoverlay.min.js') ?>"></script>

<script>
  $(document).ready(function() {


    // $("#form").LoadingOverlay("show");
    $("#form").on('submit', function(e) {
      e.preventDefault();
      $.ajax({
        type: 'POST',
        url: '<?php echo site_url('ordensevidencias/upload'); ?>',
        data: new FormData(this),
        dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function() {
          $("#response").html('');
          $("#btn-salvar").val('Por favor aguarde...');
          $("#form").LoadingOverlay("show");
          $('.limpaDiv').html('');
        },
        success: function(response) {
          $('.limpaDiv').html('');
          $("#form").LoadingOverlay("hide", true);
          $("#btn-salvar").val('Salvar');
          $("#btn-salvar").removeAttr("disabled");
          $('[name=csrf_ordem]').val(response.token);

          if (!response.erro) {
            window.location.href = "<?php echo site_url("ordensevidencias/evidencias/$ordem->codigo"); ?>";
          }

          if (response.erro) {
            $("#response").html('<div class="alert alert-danger" role="alert">' + response.erro + '</div>');
            if (response.erros_model) {
              console.log(response.erros_model);
              $.each(response.erros_model, function(key, value) {
                $ //("#response").append('<ul class="list-unstyled"><li class="text-danger">' + value + '</li></ul>');
                $('.' + key).html(value);

              });
            }
          }
        },
        error: function() {
          alert('Não foi possível processar a solicitação. Por favor entre em contato com suporte técnico.');
          $("#btn-salvar").val('Salvar');
          $("#btn-salvar").removeAttr("disabled");
          $("#form").LoadingOverlay("hide", true);
        }
      });
    });

    $("#form").submit(function() {
      $(this).find(":submit").attr('disabled', 'disabled');
    })
  });
</script>
<?php echo $this->endSection() ?>