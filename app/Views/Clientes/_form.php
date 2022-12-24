<div class="row">
  <div class="form-group col-12">
    <label class="form-control-label">Nome Completo</label>
    <input type="text" name="nome" placeholder="Insira o nome" class="form-control" value="<?php echo esc($cliente->nome); ?>">
    <div class="limpaDiv text-danger nome"></div>
  </div>

  <div class="form-group col-12">
    <label class="form-control-label">E-mail (para acesso ao sistema)</label>
    <input type="text" name="email" placeholder="Insira o e-mail" class="form-control" value="<?php echo esc($cliente->email); ?>">
    <div class="limpaDiv text-danger email"></div>
    <div id="email"></div>
  </div>

  <div class="form-group col-4">
    <label class="form-control-label">CPF</label>
    <input type="text" name="cpf" placeholder="Insira o CPF" class="form-control cpf" value="<?php echo esc($cliente->cpf); ?>">
    <div class="limpaDiv text-danger cpf"></div>
  </div>

  <div class="form-group col-4">
    <label class="form-control-label">Telefone</label>
    <input type="text" name="telefone" placeholder="Insira o telefone" class="form-control sp_celphones" value="<?php echo esc($cliente->telefone); ?>">
    <div class="limpaDiv text-danger telefone"></div>
  </div>



  <div class="form-group col-4">
    <label class="form-control-label">CEP</label>
    <input type="text" name="cep" placeholder="Insira o CEP" class="form-control cep" value="<?php echo esc($cliente->cep); ?>">
    <div class="limpaDiv text-danger cep"></div>
    <div id="cep"></div>
  </div>

  <div class="form-group col-6">
    <label class="form-control-label">Endereço</label>
    <input type="text" name="endereco" placeholder="Insira o endereço" class="form-control" value="<?php echo esc($cliente->endereco); ?>" readonly>
    <div class="limpaDiv text-danger endereco"></div>
  </div>

  <div class="form-group col-2">
    <label class="form-control-label">Nº</label>
    <input type="text" name="numero" placeholder="Insira o Nº" class="form-control" value="<?php echo esc($cliente->numero); ?>">
    <div class="limpaDiv text-danger numero"></div>
  </div>

  <div class="form-group col-4">
    <label class="form-control-label">Bairro</label>
    <input type="text" name="bairro" placeholder="Insira o bairro" class="form-control" value="<?php echo esc($cliente->bairro); ?>" readonly>
    <div class="limpaDiv text-danger bairro"></div>
  </div>

  <div class="form-group col-6">
    <label class="form-control-label">Cidade</label>
    <input type="text" name="cidade" placeholder="Insira a cidade" class="form-control" value="<?php echo esc($cliente->cidade); ?>" readonly>
    <div class="limpaDiv text-danger cidade"></div>
  </div>

  <div class="form-group col-2">
    <label class="form-control-label">UF</label>
    <input type="text" name="estado" placeholder="UF" class="form-control" value="<?php echo esc($cliente->estado); ?>" readonly>
    <div class="limpaDiv text-danger uf"></div>
  </div>
</div>