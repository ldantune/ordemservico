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
  <div class="col-lg-7">


    <div class="block">
      <?php if (empty($datasDisponiveis)) : ?>
        <p class="text-warning text-center">Não há datas disponíveis para analizar no log</p>
      <?php else : ?>
        <div class="user-block text-center col-lg-12">
          <div class="user-title mb-4">
            <h5 class="cart-title mt-2">Escolha a Data e o Usuário</h5>
          </div>

          <div id="response">
          </div>

          <?php echo form_open('/logs/consultar', ['class' => 'text-left col-12']) ?>

          <div class="form-group">
            <label class="form-control-label">Escolha a data</label>

            <select class="form-control selectize" name="data_escolhida" id="">

              <option value="">Escolha</option>
              <?php foreach ($datasDisponiveis as $data) : ?>
                <option value="<?php echo $data; ?>"><?php echo date('d/m/Y', strtotime($data)) ?></option>
              <?php endforeach; ?>

            </select>
            <div class="limpaDiv text-danger data_escolhida"></div>
          </div>

          <div class="form-group mt-5">
            <label class="form-control-label">Escolha o usuário</label>

            <select class="form-control selectize" name="usuario_id" id="">

              <option value="">Digite o nome do usuário</option>

            </select>
            <div class="limpaDiv text-danger usuario_id"></div>
          </div>
          <div class="form-group mt-5 mb-2">
            <input id="btn-salvar" value="Pesquisar" class="btn btn-danger mr-2" type="submit">

          </div>

          <?php echo form_close() ?>


        </div>
      <?php endif; ?>


    </div> <!-- ./block -->
  </div>

  <?php if (session()->has('resultadoLog')) : ?>
    <div class="col-lg-12">
      <div class="block">
        <div class="user-block">
          <div class="contributions">Resultado da última consulta:</div>
          <p class="text-info">
            <?php echo session('resultadoLog'); ?>
          </p>
        </div>
      </div>
    </div>
  <?php endif; ?>

</div>
<?php echo $this->endSection() ?>


<?php echo $this->section('scripts') ?>

<script type="text/javascript" src="<?php echo site_url('recursos/vendor/selectize/selectize.min.js'); ?>"></script>

<script>
  $(document).ready(function() {

    var $select = $(".selectize").selectize({
      create: false,
      //sortField: "text"

      maxIem: 1,
      valueField: 'id',
      labelField: 'nome',
      searchField: ['nome'],

      load: function(query, callback) {

        if (query.length < 4) {
          return callback();
        }

        $.ajax({
          url: '<?php echo site_url("logs/buscausuarios/") ?>',
          data: {
            termo: encodeURIComponent(query)
          },
          success: function(response) {

            $select.options = response;

            callback(response);

          },
          error: function() {
            alert('Não foi possível processar a solicitação. Por favor entre em contato com suporte técnico.');
          }
        });
      }

    });
  });
</script>
<?php echo $this->endSection() ?>