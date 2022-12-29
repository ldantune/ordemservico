<div class="user-block">

  <div class="form-row mb-4">

    <div class="col-md-12">
      <?php if ($ordem->id === null) : ?>

        <div class="contributions">
          Ordem aberta por: <?php echo usuario_logado()->nome; ?>
        </div>

      <?php else : ?>
        <div class="contributions">
          Ordem aberta por: <?php echo esc($ordem->usuario_abertura); ?>
        </div>

        <?php if ($ordem->usuario_responsavel !== null) : ?>


          <p class="contributions mt-0">Técnico responsável: <?php echo esc($ordem->usuario_encerramento) ?></p>
        <?php endif; ?>

      <?php endif; ?>
    </div>
  </div>

  <?php if ($ordem->id === null) : ?>
    <div class="form-group">
      <label class="form-control-label">Escolha o cliente</label>

      <select class="form-control cliente" name="cliente_id" id="">

        <option value="">Digite o nome do cliente ou CPF</option>

      </select>
      <div class="limpaDiv text-danger cliente_id"></div>
    </div>
  <?php else : ?>

    <div class="form-group">
      <label class="form-control-label">Cliente</label>
      <a tabindex="0" style="text-decoration: none; cursor: pointer;" class="" role="button" data-toggle="popover" data-trigger="focus" title="Importante" data-content="Não é permitido editar o cliente da ordem de serviço">&nbsp;&nbsp;<i class="fa fa-question-circle fa-lg text-info"></i></a>
      <input type="text" class="form-control" value="<?php echo esc($ordem->nome); ?>" disabled readonly>
    </div>

  <?php endif; ?>

  <div class="form-group">
    <label class="form-control-label">Equipamento</label>
    <input type="text" name="equipamento" placeholder="Descreva o equipamento" class="form-control" value="<?php echo esc($ordem->equipamento); ?>">
    <div class="limpaDiv text-danger equipamento"></div>
  </div>

  <div class="form-group">
    <label class="form-control-label">Defeitos do equipamento</label>
    <textarea type="file" name="defeito" class="form-control" placeholder="Descreva os defeitos do equipamento"><?php echo esc($ordem->defeito); ?></textarea>
    <div class="limpaDiv text-danger defeito"></div>
  </div>

  <div class="form-group">
    <label class="form-control-label">Observações da ordem de serviço</label>
    <textarea type="file" name="observacoes" class="form-control" placeholder="Descreva as observações da ordem de serviço"><?php echo esc($ordem->observacoes); ?></textarea>
    <div class="limpaDiv text-danger observacoes"></div>
  </div>

  <?php if ($ordem->id) : ?>
    <div class="form-group">
      <label class="form-control-label">Parecer técnico</label>
      <textarea type="file" name="parecer_tecnico" class="form-control" placeholder="Informe o parecer técnico"><?php echo esc($ordem->parecer_tecnico); ?></textarea>
      <div class="limpaDiv text-danger parecer_tecnico"></div>
    </div>
  <?php endif; ?>
</div>