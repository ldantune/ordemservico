<?php if ($conta->id === null) : ?>
  <div class="form-group">
    <label class="form-control-label">Escolha o fornecedor</label>

    <select class="form-control fornecedor" name="fornecedor_id" id="">

      <option value="">Escolha...</option>
      

    </select>
    <div class="limpaDiv text-danger fornecedor_id"></div>
  </div>
<?php else : ?>

  <div class="form-group">
    <label class="form-control-label">Fornecedor</label>
    <a tabindex="0" style="text-decoration: none; cursor: pointer;" class="" role="button" data-toggle="popover" data-trigger="focus" title="Importante" data-content="Não é permitido editar o fornecedor da conta">&nbsp;&nbsp;<i class="fa fa-question-circle fa-lg text-info"></i></a>
    <input type="text" class="form-control" value="<?php echo esc($conta->razao); ?>" disabled readonly>
  </div>

<?php endif; ?>


<div class="form-group">
  <label class="form-control-label">Valor da conta</label>
  <input type="text" name="valor_conta" placeholder="Insira o valor" class="form-control money" value="<?php echo esc($conta->valor_conta); ?>">
  <div class="limpaDiv text-danger valor_conta"></div>
</div>

<div class="form-group">
  <label class="form-control-label">Data de vencimento</label>
  <input type="date" name="data_vencimento" class="form-control" value="<?php echo esc($conta->data_vencimento); ?>">
  <div class="limpaDiv text-danger data_vencimento"></div>
</div>

<div class="form-group">
  <label class="form-control-label">Descrição da conta</label>
  <textarea type="file" name="descricao_conta" class="form-control" placeholder="Insira a descrição da conta"><?php echo esc($conta->descricao_conta); ?></textarea>
  <div class="limpaDiv text-danger descricao_conta"></div>
</div>

<div class="custom-control custom-radio mb-2">

  <input name="situacao" type="radio" value="0" class="form-check-input" id="aberto" <?php if ($conta->situacao == false) : ?> checked <?php endif; ?>>
  <label class="form-check-label" for="aberto">Esta conta está em aberto</label>
</div>

<div class="custom-control custom-radio mb-2">

  <input name="situacao" type="radio" value="1" class="form-check-input" id="paga" <?php if ($conta->situacao == true) : ?> checked <?php endif; ?>>
  <label class="form-check-label" for="paga">Esta conta está paga</label>
</div>
<div class="limpaDiv text-danger situacao"></div>