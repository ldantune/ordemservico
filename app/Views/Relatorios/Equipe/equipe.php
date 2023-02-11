<?php echo $this->extend('Layout/principal') ?>

<?php echo $this->section('titulo') ?>
<?php echo $titulo; ?>
<?php echo $this->endSection() ?>


<?php echo $this->section('estilos') ?>


<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>

<div class="row">
  <div class="col-lg-7">
    <div class="block">
      <?php echo form_open('/', ['id' => 'form']); ?>
      <div class="card-body">
        
        <div id="response">

        </div>

        <div class="form-row">
          <div class="form-group col-lg-12">
            <label class="form-control-label">Escolha um grupo</label>
            <select class="custom-select" name="grupo">
              <option value="">Escolha...</option>
              <option value="atendentes">Desempenho dos atendentes</option>
              <option value="responsaveis">Desempenho dos responsáveis</option>
            </select>
          </div>

          <div class="form-group col-lg-6 vencidas">
            <label class="form-control-label">Data inícial</label>
            <input type="datetime-local" name="data_inicial" class="form-control">
          </div>
          <div class="form-group col-lg-6 vencidas">
            <label class="form-control-label">Data final</label>
            <input type="datetime-local" name="data_final" class="form-control">
          </div>
        </div>


      </div>
      <div class="card-footer">
        <input id="btn-relatorios" type="submit" value="Gerar Relatório" class="btn btn-dark btn-sm text-secondary">
      </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>

<?php echo $this->endSection() ?>


<?php echo $this->section('scripts') ?>

<script>
  $(document).ready(function() {

    // $("[name=situacao]").on('change', function(){
    //   var situacao = $(this).val();
      
    //   if(situacao === 'vencidas'){
    //     $('.vencidas').hide('slow');
    //     $("[name=data_inicial]").prop('disabled', true);
    //     $("[name=data_final]").prop('disabled', true);
    //   }else{
    //     $('.vencidas').show('slow');
    //     $("[name=data_inicial]").prop('disabled', false);
    //     $("[name=data_final]").prop('disabled', false);
    //   }
    // });

    $("#form").on('submit', function(e) {
      e.preventDefault();
      $.ajax({
        type: 'POST',
        url: '<?php echo site_url('relatorios/gerarrelatorioequipes'); ?>',
        data: new FormData(this),
        dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function() {
          $("#response").html('');
          $("#btn-relatorios").val('Por favor aguarde...');
        },
        success: function(response) {

          $("#btn-relatorios").val('Gerar Relatório');
          $("#btn-relatorios").removeAttr("disabled");
          $('[name=csrf_ordem]').val(response.token);

          if (!response.erro) {
            if (response.info) {
              $("#response").html('<div class="alert alert-info" role="alert">' + response.info + '</div>');
            } else {

              var url = "<?php echo site_url(); ?>" + response.redirect;

              var win = window.open(url, '_blank');
              win.focus();

            }
          }
          

          if (response.erro) {
            $("#response").html('<div class="alert alert-danger" role="alert">' + response.erro + '</div>');
            if (response.erros_model) {
              $.each(response.erros_model, function(key, value) {
                $("#response").append('<ul class="list-unstyled"><li class="text-danger">' + value + '</li></ul>');
              });
            }
          }
        },
        error: function() {
          alert('Não foi possível processar a solicitação. Por favor entre em contato com suporte técnico.');
          $("#btn-relatorios").val('Gerar Relatório');
          $("#btn-relatorios").removeAttr("disabled");
        }
      });
    });

    $("#form").submit(function() {
      $(this).find(":submit").attr('disabled', 'disabled');
    })
  });
</script>

<?php echo $this->endSection() ?>