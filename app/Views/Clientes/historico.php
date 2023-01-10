<?php echo $this->extend('Layout/principal') ?>

<?php echo $this->section('titulo') ?>
<?php echo $titulo; ?>
<?php echo $this->endSection() ?>


<?php echo $this->section('estilos') ?>

<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>
<div class="row">
  <div class="col-lg-4">
    <div class="user-block block">



      <h5 class="cart-title mt-2"><?php echo esc($cliente->nome); ?></h5>
      <hr class="border-secondary">

      <p class="card-text">CPF: <?php echo esc($cliente->cpf); ?></p>
      <p class="card-text">Telefone: <?php echo esc($cliente->telefone); ?></p>
      <p class="contributions mt-0"> <?php echo $cliente->exibeSituacao() ?></p>
      <p class="card-text">Criado <?php echo $cliente->criado_em->humanize(); ?></p>
      <p class="card-text">Atualizado <?php echo $cliente->atualizado_em->humanize(); ?></p>



      <a href="<?php echo site_url("clientes/exibir/$cliente->id"); ?>" class="btn btn-secondary ml-2">Voltar</a>
    </div> <!-- ./block -->
  </div>

  <div class="col-lg-8">
    <div class="user-block block  text-center">
      <?php if (!isset($ordensCliente)) : ?>
        <div class="contributions pt-3 ">
          <p>Esse cliente não possui histórico de atendimento</p>

        </div>
      <?php else : ?>
        <div class="accordion" id="accordionExample" style="height: 650px; overflow: auto;">

          <?php foreach ($ordensCliente as $key => $ordem) : ?>

            <div class="card">
              <div class="card-header" id="heading-<?php echo $key; ?>">
                <h2 class="mb-0">
                  <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse-<?php echo $key; ?>" aria-expanded="true" aria-controls="collapse-<?php echo $key; ?>">
                    <p>Atendimento realizado em: <?php echo date('d/m/Y H:i', strtotime($ordem->criado_em)); ?>                  
                    </p>
                  </button>
                </h2>
              </div>

              <div id="collapse-<?php echo $key; ?>" class="text-left collapse <?php echo ($key === 0 ? 'show' : '') ?>" aria-labelledby="heading-<?php echo $key; ?>" data-parent="#accordionExample">
                <div class="card-body">
                  <p><strong>Código ordem:&nbsp; </strong> <?php echo $ordem->codigo;?></p>
                  <p><strong>Situação:&nbsp; </strong> <?php echo $ordem->exibeSituacao();?></p>
                  <p><strong>Equpamento:&nbsp; </strong> <?php echo esc($ordem->equipamento);?></p>
                  <p><strong>Defeito:&nbsp; </strong> <?php echo ($ordem->defeito != null ? esc($ordem->defeito) : 'Não informado');?></p>
                  <p><strong>Observações:&nbsp; </strong> <?php echo ($ordem->observacoes != null ? esc($ordem->observacoes) : 'Não informado');?></p>

                  <a class="btn btn-outline-info text-white btn-sm mr-2" href="<?php echo site_url("ordens/detalhes/$ordem->codigo") ?>" target="_blank">Mais detalhes</a>
                </div>
              </div>
            </div>

          <?php endforeach; ?>

        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php echo $this->endSection() ?>


<?php echo $this->section('scripts') ?>

<?php echo $this->endSection() ?>