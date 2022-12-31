<?php echo $this->extend('Layout/principal') ?>

<?php echo $this->section('titulo') ?>
<?php echo $titulo; ?>
<?php echo $this->endSection() ?>


<?php echo $this->section('estilos') ?>

<link rel="stylesheet" type="text/css" href="<?php echo site_url('recursos/vendor/auto-complete/jquery-ui.css'); ?>" />


<style>
  .ui-autocomplete {
    max-height: 300px;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 9999 !important;
  }

  .ui-menu-item .ui-menu-wrapper.ui-state-active {
    background: #fff !important;
    color: #007bff !important;
    border: none;
  }
</style>


<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>
<div class="row">
  <div class="col-lg-12">
    <div class="block">
      <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#itensModal">
        Adicionar itens
      </button>
      <?php if ($ordem->itens === null) : ?>

        <div class="contributions py-3">
          <p>Nenhum item foi adicionado à ordem</p>




        </div>
      <?php else : ?>



      <?php endif; ?>







      <hr class="border-secondary">

      <!-- Example single danger button -->
      <div class="btn-group">
        <button type="button" class="btn btn-danger dropdown-toggle btn-sm" data-toggle="dropdown" aria-expanded="false">
          Ações
        </button>
        <div class="dropdown-menu">
          <?php if ($ordem->situacao === 'aberta') : ?>
            <a class="dropdown-item" href="<?php echo site_url("ordens/editar/$ordem->codigo"); ?>">Editar ordem</a>
            <a class="dropdown-item" href="<?php echo site_url("ordens/encerrar/$ordem->codigo"); ?>">Encerrar ordem</a>
            <a class="dropdown-item" href="<?php echo site_url("ordensitens/itens/$ordem->codigo"); ?>">Gerênciar itens da ordem</a>
            <a class="dropdown-item" href="<?php echo site_url("ordens/respomsavel/$ordem->codigo"); ?>">Definir técnico responsável</a>
            <div class="dropdown-divider"></div>
          <?php endif; ?>


          <a class="dropdown-item" href="<?php echo site_url("ordensevidencias/evidencias/$ordem->codigo"); ?>">Evidências da ordem</a>

          <a class="dropdown-item" href="<?php echo site_url("ordens/email/$ordem->codigo"); ?>">Enviar por e-mail</a>
          <a class="dropdown-item" href="<?php echo site_url("ordens/gerarpdf/$ordem->codigo"); ?>">Gerar PDF</a>
          <div class="dropdown-divider"></div>

          <?php if ($ordem->deletado_em === null) : ?>
            <a class="dropdown-item" href="<?php echo site_url("ordens/excluir/$ordem->codigo"); ?>">Excluir ordem</a>
          <?php else : ?>
            <a class="dropdown-item" href="<?php echo site_url("ordens/desfazerexclusao/$ordem->codigo"); ?>">Recuperar ordem</a>
          <?php endif; ?>

        </div>
      </div>

      <a href="<?php echo site_url("ordens"); ?>" class="btn btn-secondary ml-2 btn-sm">Voltar</a>
    </div> <!-- ./block -->
  </div>


</div>


<!-- Modal -->
<div class="modal fade" id="itensModal" tabindex="-1" aria-labelledby="itensModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="itensModalLabel">Adicionar itens da ordem <?php echo $ordem->codigo ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="ui-widget">
          <input type="text" name="query" id="query" class="form-control form-control-lg mb-5" placeholder="Pesquise pelo nome ou código do item">
        </div>

        <div class="block-body">
          <div id="response"></div>

          <?php
          $hiddens = [
            'codigo' => $ordem->codigo,
            'item_id' => ''
          ];
          ?>

          <?php echo form_open('/', ['id' => 'form'], $hiddens) ?>

          <div class="row">
            <div class="form-group col-md-8">
              <label class="form-control-label">Item</label>
              <input type="text" name="item_nome" class="form-control" readonly required>
            </div>

            <div class="form-group col-md-2">
              <label class="form-control-label">Valor</label>
              <input type="text" name="item_preco" class="form-control money" readonly required>
            </div>

            <div class="form-group col-md-2">
              <label class="form-control-label">Qtde</label>
              <input type="number" name="item_quantidade" class="form-control" value="1" min="1" step="1" required>
            </div>
          </div>


          <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Salvar">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          </div>

          <?php echo form_close(); ?>
        </div>
      </div>

    </div>
  </div>
</div>
<?php echo $this->endSection() ?>


<?php echo $this->section('scripts') ?>

<script type="text/javascript" src="<?php echo site_url('recursos/vendor/auto-complete/jquery-ui.js'); ?>"></script>
<script src="<?php echo site_url('recursos/vendor/mask/jquery.mask.min.js') ?>"></script>
<script src="<?php echo site_url('recursos/vendor/mask/app.js') ?>"></script>


<script>
  $(document).ready(function() {

    $(function() {

      $("#query").autocomplete({
        minLength: 4,
        source: function(request, response) {
          $.ajax({
            url: "<?php echo site_url('ordensitens/pesquisaitens'); ?>",
            dataType: "json",
            data: {
              term: request.term
            },
            beforeSend: function() {
              $("#response").html('');
              $("#form")[0].reset();
            },
            success: function(data) {

              if (data.length < 1) {
                var data = [{
                  label: 'Item não encontrado',
                  value: -1
                }];
              }

              response(data);
            }, //fim success
          }); // fim ajax
        }, // fim source

        select: function(event, ui) {

          $(this).val("");

          event.preventDefault();

          if (ui.item.value == -1) {
            $(this).val("");
            return false;
          } else {
            var item_id = ui.item.id;
            var item_nome = ui.item.value;
            var item_preco = ui.item.item_preco;


            $("[name=item_id]").val(item_id);
            $("[name=item_nome]").val(item_nome);
            $("[name=item_preco]").val(item_preco);


          } //fim else

        }, // fim select

      }).data("ui-autocomplete")._renderItem = function(ul, item) {
        return $("<li class='ui-autocomplete-row'></li>")
          .data("item.autocpmplete", item)
          .append(item.label)
          .appendTo(ul);
      }
    });
  });
</script>

<?php echo $this->endSection() ?>