<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;
use App\Libraries\Token;
use PhpParser\Node\Expr\FuncCall;
use TheSeer\Tokenizer\Token as TokenizerToken;

class Usuario extends Entity
{
    protected $dates   = [
        'criado_em',
        'atualizado_em',
        'deletado_em'
    ];

    public function exibeSituacao()
    {

        if ($this->deletado_em != null) {

            $icone = '<span class="text-white">Excluído</span> <i class="fa fa-undo"></i>&nbsp;Desfazer';

            $situacao = anchor("usuarios/desfazerexclusao/$this->id", $icone, ['class' => 'btn btn-outline-success btn-sm']);

            return $situacao;
        }

        if ($this->ativo == true) {
            return '<i class="fa fa-unlock text-success"></i>&nbsp;Ativo';
        }

        if ($this->ativo == false) {
            return '<i class="fa fa-lock text-warning"></i>&nbsp;Inativo';
        }
    }

    public function verificaPassword(string $password): bool
    {
        return password_verify($password, $this->password_hash);
    }

    public function temPermissaoPara(string $permissao): bool
    {
        if ($this->is_admin == true) {
            return true;
        }

        if (empty($this->permissoes)) {
            return false;
        }

        if (in_array($permissao, $this->permissoes) == false) {
            return false;
        }

        return true;
    }

    public function iniciaPasswordReset(): void
    {
        $token = new Token();

        $this->reset_token = $token->getValue();

        $this->reset_hash = $token->getHash();

        $this->reset_expira_em = date('Y-m-d H:i:s', time() + 7200);

    }

    public function finalizaPasswordReset() :void {
        $this->reset_hash = null;
        $this->reset_expira_em = null;
    }
}
