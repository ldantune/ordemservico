<?php echo $this->extend('Layout/principal') ?>

<?php echo $this->section('titulo') ?>
<?php echo $titulo; ?>
<?php echo $this->endSection() ?>


<?php echo $this->section('estilos') ?>


<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>

<div class="row">
  <div class="col-lg-6">
    <div class="accordion" id="accordionExample">
      <div class="card">
        <div class="card-header" id="headingOne">
          <h2 class="mb-0">
            <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
              Produtos com estoque zerado ou negativo
            </button>
          </h2>
        </div>

        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
          <div class="card-body">
            Possibilita a geração de relatório em PDF de itens do tipo produto que estejam com estoque zerado ou negativo.
          </div>
          <div class="card-footer">
            <a class="btn btn-dark btn-sm text-secondary" href="<?php echo site_url('relatorios/produtos-com-estoque-zerado-negativo') ?>" target="_blank">Gerar Relatório</a>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header" id="headingTwo">
          <h2 class="mb-0">
            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
              Itens mais vendidos
            </button>
          </h2>
        </div>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
          <div class="card-body">
            Possibilita a geração de relatório em PDF dos itens que foram mais vendidos, 
            tendo em mente que serão conteplados apenas as Ordens de Serviço que estejam com status de Encerrada.

            <?php echo form_open('/', ['id' => 'form']); ?>

            <div id="response">

            </div>

            <div class="form-row">
              <div class="form-group col-lg-12">
                <label class="form-control-label">Tipo de item</label>
                <select class="custom-select" name="tipo">
                  <option value="">Escolha o tipo</option>
                  <option value="produto">Produto</option>
                  <option value="serviço">Serviço</option>
                </select>
              </div>

              <div class="form-group col-lg-6">
                <label class="form-control-label">Data inícial</label>
                <input type="datetime-local" name="data_inicial" class="form-control">
              </div>
              <div class="form-group col-lg-6">
                <label class="form-control-label">Data final</label>
                <input type="datetime-local" name="data_final" class="form-control">
              </div>
            </div>

            <?php echo form_close(); ?>
          </div>
          <div class="card-footer">
            <input value="Gerar Relatório" class="btn btn-dark btn-sm text-secondary" target="_blank">
          </div>
        </div>
      </div>
      
    </div>
  </div>
</div>

<?php echo $this->endSection() ?>


<?php echo $this->section('scripts') ?>

<?php echo $this->endSection() ?>