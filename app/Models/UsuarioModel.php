<?php

namespace App\Models;

use App\Entities\Usuario;
use CodeIgniter\Model;
use App\Libraries\Token;

class UsuarioModel extends Model
{

    protected $table            = 'usuarios';
    protected $returnType       = '\App\Entities\Usuario';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'nome',
        'email',
        'password',
        'reset_hash',
        'reset_expira_em',
        'imagem'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules = [
        'nome'     => 'required|min_length[3]|max_length[120]',
        'email'    => 'required|valid_email|max_length[230]|is_unique[usuarios.email,id,{id}]',
        'password'     => 'required|min_length[6]',
        'password_confirmation' => 'required_with[password]|matches[password]',
    ];
    protected $validationMessages = [
        'nome' => [
            'required' => 'O campo Nome é obrigatorio',
            'min_length' => 'O campo Nome precisa ter pelo menos 3 caractéres',
            'max_length' => 'O campo Nome não pode ser maior que 120 caractéres',
        ],
        'email' => [
            'required' => 'O campo E-mail é obrigatorio',
            'max_length' => 'O campo E-mail não pode ser maior que 230 caractéres',
            'is_unique' => 'Esse e-mail já foi escolhido. Por favor informe outro.',
        ],
        'password_confirmation' => [
            'required_with' => 'Por favor confirme a sua senha.',
            'matches' => 'As senhas precisam combinar',
        ],

    ];

    // Callbacks
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {

            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);

            //Removemos dos dados a serem salvos
            unset($data['data']['password']);
            unset($data['data']['password_confirmation']);
        }
        return $data;
    }

    public function buscaUsuarioPorEmail(string $email)
    {
        return $this->where('email', $email)->where('deletado_em', null)->first();
    }

    public function recuperaPermissoesDoUsuarioLogado(int $usuario_id)
    {

        $atributos = [
            'usuarios.id',
            'usuarios.nome AS usuario',
            'grupos_usuarios.*',
            'permissoes.nome AS permissao',
        ];

        return $this->select($atributos)
            ->asArray()
            ->join('grupos_usuarios', 'grupos_usuarios.usuario_id = usuarios.id')
            ->join('grupos_permissoes', 'grupos_permissoes.grupo_id = grupos_usuarios.grupo_id')
            ->join('permissoes', 'permissoes.id = grupos_permissoes.permissao_id')
            ->where('usuarios.id', $usuario_id)
            ->groupBy('permissoes.nome')
            ->findAll();
    }

    public function buscaUsuarioParaRedefinirSenha(string $token)
    {
        $token = new Token($token);

        $tokenHash = $token->getHash();

        $usuario = $this->where('reset_hash', $tokenHash)
            ->where('deletado_em', null)
            ->first();

        if ($usuario === null) {
            return null;
        }

        if ($usuario->reset_expira_em < date('Y-m-d H:i:s')) {
            return null;
        }

        return $usuario;
    }

    public function atualizaEmailDoCliente(int $usuario_id, string $email)
    {

        return $this->protect(false)
            ->where('id', $usuario_id)
            ->set('email', $email)
            ->update();
    }

    public function recuperaResponsaveisParaOrdem(string $termo = null)
    {

        if ($termo === null) {
            return [];
        }

        $atributos = [
            'usuarios.id',
            'usuarios.nome',
        ];

        $reposnsaveis = $this->select($atributos)
            ->join('grupos_usuarios', 'grupos_usuarios.usuario_id = usuarios.id')
            ->join('grupos', 'grupos.id = grupos_usuarios.grupo_id')
            ->like('usuarios.nome', $termo)
            ->where('usuarios.ativo', true)
            ->where('grupos.exibir', true)
            ->where('grupos.id !=', 2)
            ->where('grupos.deletado_em', null)
            ->where('usuarios.deletado_em', null)
            ->groupBy('usuarios.nome')
            ->findAll();

        if ($reposnsaveis === null) {
            return [];
        }

        return $reposnsaveis;
    }
}
