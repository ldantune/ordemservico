<?php

namespace App\Models;

use CodeIgniter\Model;

class GrupoPermissaoModel extends Model
{
    protected $table            = 'grupos_permissoes';
    protected $returnType       = 'object';
    protected $allowedFields    = ['grupo_id', 'permissao_id'];
}
