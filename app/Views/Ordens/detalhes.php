<?php echo $this->extend('Layout/principal') ?>

<?php echo $this->section('titulo') ?>
<?php echo $titulo; ?>
<?php echo $this->endSection() ?>


<?php echo $this->section('estilos') ?>

<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>
<div class="row">
  <div class="col-lg-12">
    <div class="block">

      <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="pills-home-tab" data-toggle="pill" data-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Detalhes da Ordem</button>
        </li>

        <?php if (isset($ordem->transacao)) : ?>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-profile-tab" data-toggle="pill" data-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Transações da Ordem</button>
          </li>
        <?php endif; ?>
      </ul>
      <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">

          <div class="user-block text-center">
            <div class="user-title mb-4">
              <h5 class="cart-title mt-2"><?php echo esc($ordem->nome); ?></h5>
              <span>Ordem: <?php echo esc($ordem->codigo) ?></span>
            </div>
            <p class="contributions mt-0"> <?php echo $ordem->exibeSituacao() ?></p>
            <p class="contributions mt-0">Aberta por: <?php echo esc($ordem->usuario_abertura) ?></p>
            <p class="contributions mt-0">Responsável técnico: <?php echo esc($ordem->usuario_responsavel !== null ? $ordem->usuario_responsavel : "Não definido") ?></p>

            <?php if ($ordem->situacao === 'encerrada') : ?>


              <p class="contributions mt-0">Encerrada por: <?php echo esc($ordem->usuario_encerramento) ?></p>
            <?php endif; ?>


            <p class="card-text">Criado <?php echo $ordem->criado_em->humanize(); ?></p>
            <p class="card-text">Atualizado <?php echo $ordem->atualizado_em->humanize(); ?></p>

            <hr class="border-secondary">

            <?php if ($ordem->itens === null) : ?>

              <div class="contributions py-3">
                <p>Nenhum item foi adicionado à ordem</p>

                <?php if ($ordem->situacao === 'aberta') : ?>

                  <a class="btn btn-outline-info btn-sm" href="<?php echo site_url("ordensitens/itens/$ordem->codigo") ?>">Adicionar itens</a>

                <?php endif; ?>
              </div>
            <?php else : ?>



            <?php endif; ?>

          </div>
        </div>
        <?php if (isset($ordem->transacao)) : ?>
          <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">Transações da ordem</div>
        <?php endif; ?>
      </div>






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
<?php echo $this->endSection() ?>


<?php echo $this->section('scripts') ?>

<?php echo $this->endSection() ?>