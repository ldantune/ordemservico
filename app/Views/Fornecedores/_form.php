<div class="row">
  <div class="form-group col-6">
    <label class="form-control-label">Razão social</label>
    <input type="text" name="nome" placeholder="Insira a razão social" class="form-control" value="<?php echo esc($fornecedor->razao); ?>">
  </div>

  <div class="form-group col-6">
    <label class="form-control-label">Nome fantasia</label>
    <input type="text" name="nome" placeholder="Insira o nome fantasia" class="form-control" value="<?php echo esc($fornecedor->nome_fantasia); ?>">
  </div>

  <div class="form-group col-4">
    <label class="form-control-label">CNPJ</label>
    <input type="text" name="nome" placeholder="Insira o CNPJ" class="form-control cnpj" value="<?php echo esc($fornecedor->cnpj); ?>">
  </div>

  <div class="form-group col-4">
    <label class="form-control-label">IE</label>
    <input type="text" name="nome" placeholder="Insira a inscrição estadual" class="form-control" value="<?php echo esc($fornecedor->ie); ?>">
  </div>

  <div class="form-group col-4">
    <label class="form-control-label">Telefone</label>
    <input type="text" name="nome" placeholder="Insira o telefone" class="form-control sp_celphones" value="<?php echo esc($fornecedor->telefone); ?>">
  </div>

  <div class="form-group col-4">
    <label class="form-control-label">CEP</label>
    <input type="text" name="nome" placeholder="Insira o CEP" class="form-control cep" value="<?php echo esc($fornecedor->cep); ?>">
    <div id="cep"></div>
  </div>

  <div class="form-group col-6">
    <label class="form-control-label">Endereço</label>
    <input type="text" name="nome" placeholder="Insira o endereço" class="form-control" value="<?php echo esc($fornecedor->endereco); ?>" readonly>
  </div>

  <div class="form-group col-2">
    <label class="form-control-label">Nº</label>
    <input type="text" name="nome" placeholder="Insira o Nº" class="form-control" value="<?php echo esc($fornecedor->numero); ?>">
  </div>

  <div class="form-group col-4">
    <label class="form-control-label">Bairro</label>
    <input type="text" name="nome" placeholder="Insira o bairro" class="form-control" value="<?php echo esc($fornecedor->bairro); ?>" readonly>
  </div>

  <div class="form-group col-6">
    <label class="form-control-label">Cidade</label>
    <input type="text" name="nome" placeholder="Insira a cidade" class="form-control" value="<?php echo esc($fornecedor->cidade); ?>" readonly>
  </div>

  <div class="form-group col-2">
    <label class="form-control-label">UF</label>
    <input type="text" name="nome" placeholder="UF" class="form-control" value="<?php echo esc($fornecedor->estado); ?>" readonly>
  </div>


</div>
<div class="custom-control custom-checkbox">
  <input type="hidden" name="ativo" value="0">
  <input name="ativo" type="checkbox" value="1" class="custom-control-input" id="ativo" <?php if ($fornecedor->ativo == true) : ?> checked <?php endif; ?>>
  <label class="custom-control-label" for="ativo">Fornecedor ativo</label>
</div>