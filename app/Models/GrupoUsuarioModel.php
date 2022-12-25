<?php

namespace App\Models;

use CodeIgniter\Model;

class GrupoUsuarioModel extends Model
{
    protected $table            = 'grupos_usuarios';
    protected $returnType       = 'object';
    protected $allowedFields    = ['grupo_id', 'usuario_id'];


    public function recuperaGruposDoUsuario(int $usuario_id, int $quantidade_paginacao)
    {
        $atributos = [
            'grupos_usuarios.id AS principal_id',
            'grupos.id AS grupo_id',
            'grupos.nome',
            'grupos.descricao'
        ];

        return $this->select($atributos)
                    ->join('grupos', 'grupos.id = grupos_usuarios.grupo_id')
                    ->join('usuarios', 'usuarios.id = grupos_usuarios.usuario_id')
                    ->where('grupos_usuarios.usuario_id', $usuario_id)
                    ->groupBy('grupos.nome')
                    ->paginate($quantidade_paginacao);

    }

    //Método que recupera o grupo ao qual o usuário fazr parte
    public function usuarioEstaNoGrupo(int $grupo_id, int $usuario_id){
        return $this->where('grupo_id', $grupo_id)
                    ->where('usuario_id', $usuario_id)
                    ->first();
    }

    public function recuperaGrupos()
    {
        $atributos = [
            'grupos_usuarios.usuario_id',
            'grupos.id AS grupo_id',
            'grupos.nome'
        ];

        return $this->select($atributos)
                    ->asArray()
                    ->join('grupos', 'grupos.id = grupos_usuarios.grupo_id')
                    ->join('usuarios', 'usuarios.id = grupos_usuarios.usuario_id')
                    ->where('grupos.deletado_em', null)
                    ->findAll();

    }
}
