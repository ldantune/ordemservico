<?php echo $this->extend('Layout/principal') ?>

<?php echo $this->section('titulo') ?>
<?php echo $titulo; ?>
<?php echo $this->endSection() ?>


<?php echo $this->section('estilos') ?>

<link rel="stylesheet" type="text/css" href="<?php echo site_url('recursos/vendor/selectize/selectize.bootstrap4.css'); ?>" />

<style>
  /* Estilizando o select para acompanhar a formatação do template */

  .selectize-input,
  .selectize-control.single .selectize-input.input-active {
    background: #2d3035 !important;
  }

  .selectize-dropdown,
  .selectize-input,
  .selectize-input input {
    color: #777;
  }

  .selectize-input {
    /*        height: calc(2.4rem + 2px);*/
    border: 1px solid #444951;
    border-radius: 0;
  }
</style>

<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>
<div class="row">

  <div class="col-lg-8">

    <div class="user-block block">

      <?php if (empty($permissoesDisponiveis)) : ?>

        <div class="text-center">
          <p class="contributions text-warning mt-0">
            Esse grupo já possui todas as permissões de acesso!
          </p>
        </div>
        <div class="form-group mt-2 mb-2">
          <a href="<?php echo site_url("grupos/exibir/$grupo->id"); ?>" class="btn btn-secondary ml-2">Voltar</a>
        </div>
      <?php else : ?>
        <div id="response">

        </div>

        <?php echo form_open('/', ['id' => 'form'], ['id' => "$grupo->id"]) ?>

        <div class="form-group">
          <label class="form-control-label">Escolha uma ou mais permissões</label>

          <select class="form-control permissao" name="permissao_id[]" id="" multiple>

            <option value="">Escolha...</option>
            <?php foreach ($permissoesDisponiveis as $permissao) : ?>

              <option value="<?php echo $permissao->id; ?>"><?php echo esc($permissao->nome); ?></option>
            <?php endforeach; ?>

          </select>

        </div>

        <div class="form-group mt-5 mb-2">
          <input id="btn-salvar" value="Salvar" class="btn btn-danger mr-2" type="submit">
          <a href="<?php echo site_url("grupos/exibir/$grupo->id"); ?>" class="btn btn-secondary ml-2">Voltar</a>
        </div>

        <?php echo form_close() ?>
      <?php endif; ?>

    </div>
  </div>



  <div class="col-lg-4">

    <div class="user-block block">

      <?php if (empty($grupo->permissoes)) : ?>
        <div class="text-center">
          <p class="contributions text-warning mt-0">
            Esse grupo ainda não possui permissões de acesso!
          </p>
        </div>

      <?php else : ?>

        <div class="table-responsive">

          <table class="table table-striped table-sm">
            <thead>
              <tr>
                <th>Permissão</th>
                <th>Excluir</th>
              </tr>
            </thead>
            <tbody>

              <?php foreach ($grupo->permissoes as $permissao) : ?>
                <tr>
                  <td><?php echo esc($permissao->nome) ?></td>

                  <td>
                    <?php 
                      $atributos = [
                        'onSubmit' => "return confirm('Tem certeza da exclusão da permissão?');",
                      ];
                    
                    ?>

                    <?php echo form_open("grupos/removepermissao/$permissao->principal_id", $atributos) ?>
                      <button type="submit" class="btn btn-sm btn-danger">Excluir</button>

                    <?php echo form_close(); ?>
                  </td>

                </tr>
              <?php endforeach; ?>

            </tbody>
          </table>

          <div class="mt-3 ml-2">
            <?php echo $grupo->pager->links(); ?>
          </div>


        </div>

      <?php endif; ?>

      <!-- Example single danger button -->


    </div> <!-- ./block -->
  </div>
</div>
<?php echo $this->endSection() ?>


<?php echo $this->section('scripts') ?>
<script type="text/javascript" src="<?php echo site_url('recursos/vendor/selectize/selectize.min.js'); ?>"></script>

<script>
  $(document).ready(function() {
    $(".permissao").selectize({
      create: true,
      sortField: "text"
    });


    $("#form").on('submit', function(e) {
      e.preventDefault();
      $.ajax({
        type: 'POST',
        url: '<?php echo site_url('grupos/salvarpermissoes'); ?>',
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

          $("#btn-salvar").val('Salvar');
          $("#btn-salvar").removeAttr("disabled");
          $('[name=csrf_ordem]').val(response.token);

          if (!response.erro) {

            window.location.href = "<?php echo site_url("grupos/permissoes/$grupo->id"); ?>";

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